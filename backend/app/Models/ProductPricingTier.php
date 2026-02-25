<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPricingTier extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'product_pricing_tiers';

    protected $fillable = [
        'product_id',
        'tier_name',
        'price_usd',
        'price_inr',
        'billing_period',
        'is_popular',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_inr' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'is_active' => true,
        'is_popular' => false,
        'display_order' => 0,
        'billing_period' => 'monthly'
    ];

    /**
     * Billing period options
     */
    const BILLING_PERIODS = [
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'one-time' => 'One Time'
    ];

    /**
     * Popular tier names
     */
    const TIER_NAMES = [
        'Basic' => 'Basic',
        'Pro' => 'Pro',
        'Enterprise' => 'Enterprise',
        'Premium' => 'Premium',
        'Standard' => 'Standard',
        'Professional' => 'Professional',
        'Business' => 'Business',
        'Advanced' => 'Advanced'
    ];
    /**
 * Get the features for this tier
 */
public function features()
{
    return $this->hasMany(TierFeature::class, 'tier_id', 'id')
                ->where('is_available', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all features (including unavailable)
 */
public function allFeatures()
{
    return $this->hasMany(TierFeature::class, 'tier_id', 'id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get features count
 */
public function getFeaturesCountAttribute(): int
{
    return $this->features()->count();
}

/**
 * Get available features list
 */
public function getFeaturesListAttribute(): array
{
    return $this->features->pluck('feature_description')->toArray();
}

    /**
     * Get the product that owns this tier
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get formatted price in USD
     */
    public function getFormattedPriceUsdAttribute(): ?string
    {
        return $this->price_usd ? '$' . number_format($this->price_usd, 2) : null;
    }

    /**
     * Get formatted price in INR
     */
    public function getFormattedPriceInrAttribute(): ?string
    {
        return $this->price_inr ? '₹' . number_format($this->price_inr, 2) : null;
    }

    /**
     * Get both prices
     */
    public function getPricesAttribute(): array
    {
        return [
            'usd' => $this->formatted_price_usd,
            'inr' => $this->formatted_price_inr,
            'usd_raw' => $this->price_usd,
            'inr_raw' => $this->price_inr
        ];
    }

    /**
     * Get billing period label
     */
    public function getBillingPeriodLabelAttribute(): string
    {
        return self::BILLING_PERIODS[$this->billing_period] ?? ucfirst($this->billing_period);
    }

    /**
     * Get badge for popular tier
     */
    public function getPopularBadgeAttribute(): ?string
    {
        if (!$this->is_popular) {
            return null;
        }
        
        return '<span class="badge bg-warning text-dark">Most Popular</span>';
    }

    /**
     * Get price with period
     */
    public function getPriceWithPeriodAttribute(): string
    {
        $prices = [];
        
        if ($this->price_inr) {
            $prices[] = $this->formatted_price_inr;
        }
        if ($this->price_usd) {
            $prices[] = $this->formatted_price_usd;
        }
        
        $priceStr = implode(' / ', $prices);
        
        if ($this->billing_period === 'one-time') {
            return $priceStr . ' one-time';
        }
        
        return $priceStr . '/' . $this->billing_period_label;
    }

    /**
     * Get yearly savings (if monthly price is available)
     */
    public function getYearlySavingsAttribute(): ?array
    {
        if ($this->billing_period !== 'yearly' || !$this->price_inr) {
            return null;
        }

        // Assuming monthly price is 1/10th of yearly (20% savings)
        $monthlyEquivalent = $this->price_inr / 10;
        $monthlyPrice = $this->product?->pricingTiers()
            ->where('billing_period', 'monthly')
            ->where('tier_name', $this->tier_name)
            ->first();

        if ($monthlyPrice) {
            $yearlyTotal = $this->price_inr;
            $monthlyTotal = $monthlyPrice->price_inr * 12;
            $savings = $monthlyTotal - $yearlyTotal;
            $savingsPercent = round(($savings / $monthlyTotal) * 100);

            return [
                'amount' => $savings,
                'formatted' => '₹' . number_format($savings, 2),
                'percent' => $savingsPercent,
                'description' => "Save {$savingsPercent}% with yearly billing"
            ];
        }

        return null;
    }

    /**
     * Scope a query to only active tiers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only popular tiers
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope a query to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by billing period
     */
    public function scopeWithBillingPeriod($query, $period)
    {
        return $query->where('billing_period', $period);
    }

    /**
     * Get all available tier names
     */
    public static function getTierNames(): array
    {
        return array_keys(self::TIER_NAMES);
    }

    /**
     * Get all billing periods
     */
    public static function getBillingPeriods(): array
    {
        $periods = [];
        foreach (self::BILLING_PERIODS as $value => $label) {
            $periods[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $periods;
    }
}