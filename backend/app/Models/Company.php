<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $primaryKey = 'company_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'companies';

    protected $fillable = [
        'company_name',
        'tagline',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'founded_year',
        'headquarters',
        'website_url',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'founded_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    /**
 * Get the portfolio projects for the company
 */
public function portfolioProjects()
{
    return $this->hasMany(PortfolioProject::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->ordered();
}

/**
 * Get all portfolio projects (including inactive) for admin
 */
public function allPortfolioProjects()
{
    return $this->hasMany(PortfolioProject::class, 'company_id', 'company_id')
                ->ordered();
}

/**
 * Get featured portfolio projects
 */
public function featuredPortfolioProjects()
{
    return $this->portfolioProjects()->featured();
}

/**
 * Get portfolio categories
 */
public function getPortfolioCategoriesAttribute()
{
    return $this->portfolioProjects()
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');
}

/**
 * Get portfolio years
 */
public function getPortfolioYearsAttribute()
{
    return $this->portfolioProjects()
                ->selectRaw('YEAR(completion_date) as year')
                ->whereNotNull('completion_date')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
}
    /**
 * Get the site settings for the company
 */
public function siteSettings()
{
    return $this->hasMany(SiteSetting::class, 'company_id', 'company_id');
}

/**
 * Get a specific setting by key
 */
public function getSetting($key, $default = null)
{
    $setting = $this->siteSettings()->byKey($key)->first();
    
    if (!$setting) {
        return $default;
    }
    
    return $setting->setting_value;
}

/**
 * Get all settings as key-value pairs
 */
public function getAllSettings()
{
    $settings = [];
    
    foreach ($this->siteSettings as $setting) {
        $settings[$setting->setting_key] = $setting->setting_value;
    }
    
    return $settings;
}

/**
 * Set a setting value
 */
public function setSetting($key, $value, $description = null)
{
    $setting = $this->siteSettings()->byKey($key)->first();
    
    if ($setting) {
        $setting->setting_value = $value;
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();
    } else {
        $setting = new SiteSetting([
            'setting_key' => $key,
            'setting_value' => $value,
            'description' => $description
        ]);
        $this->siteSettings()->save($setting);
    }
    
    return $setting;
}
    /**
 * Get the contact inquiries for the company
 */
public function contactInquiries()
{
    return $this->hasMany(ContactInquiry::class, 'company_id', 'company_id')
                ->orderBy('created_at', 'desc');
}

/**
 * Get pending inquiries count
 */
public function getPendingInquiriesCountAttribute()
{
    return $this->contactInquiries()->pending()->count();
}

/**
 * Get inquiries statistics
 */
public function getInquiriesStatsAttribute()
{
    $total = $this->contactInquiries()->count();
    $pending = $this->contactInquiries()->pending()->count();
    $contacted = $this->contactInquiries()->contacted()->count();
    $resolved = $this->contactInquiries()->resolved()->count();

    return [
        'total' => $total,
        'pending' => $pending,
        'contacted' => $contacted,
        'resolved' => $resolved,
        'pending_percentage' => $total > 0 ? round(($pending / $total) * 100) : 0,
        'contacted_percentage' => $total > 0 ? round(($contacted / $total) * 100) : 0,
        'resolved_percentage' => $total > 0 ? round(($resolved / $total) * 100) : 0,
    ];
}

    /**
 * Get the testimonials for the company
 */
public function testimonials()
{
    return $this->hasMany(Testimonial::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all testimonials (including inactive) for admin
 */
public function allTestimonials()
{
    return $this->hasMany(Testimonial::class, 'company_id', 'company_id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get average rating from testimonials
 */
public function getAverageRatingAttribute()
{
    return $this->testimonials()->avg('rating') ?? 0;
}

/**
 * Get total testimonials count
 */
public function getTotalTestimonialsAttribute()
{
    return $this->testimonials()->count();
}

/**
 * Get rating distribution
 */
public function getRatingDistributionAttribute()
{
    $distribution = [];
    for ($i = 1; $i <= 5; $i++) {
        $distribution[$i] = $this->testimonials()
            ->where('rating', $i)
            ->count();
    }
    return $distribution;
}
    /**
 * Get the team members for the company
 */
public function teamMembers()
{
    return $this->hasMany(TeamMember::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all team members (including inactive) for admin
 */
public function allTeamMembers()
{
    return $this->hasMany(TeamMember::class, 'company_id', 'company_id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get team members by position
 */
public function getTeamByPosition($position)
{
    return $this->teamMembers()
                ->where('position', 'LIKE', "%{$position}%")
                ->get();
}

    /**
 * Get the why choose us points for the company
 */
public function whyChooseUsPoints()
{
    return $this->hasMany(WhyChooseUsPoint::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all why choose us points (including inactive) for admin
 */
public function allWhyChooseUsPoints()
{
    return $this->hasMany(WhyChooseUsPoint::class, 'company_id', 'company_id')
                ->orderBy('display_order', 'asc');
}
    /**
 * Get the statistics for the company
 */
public function statistics()
{
    return $this->hasMany(Statistic::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all statistics (including inactive) for admin
 */
public function allStatistics()
{
    return $this->hasMany(Statistic::class, 'company_id', 'company_id')
                ->orderBy('display_order', 'asc');
}

/**
 * Get statistics summary (total counts, etc.)
 */
public function getStatisticsSummaryAttribute()
{
    $stats = $this->statistics;
    
    return [
        'total_stats' => $stats->count(),
        'total_value' => $stats->sum('value'),
        'average_value' => $stats->avg('value'),
        'max_value' => $stats->max('value'),
        'min_value' => $stats->min('value')
    ];
}
    /**
 * Get the process steps for the company
 */
public function processSteps()
{
    return $this->hasMany(ProcessStep::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('step_number', 'asc');
}

/**
 * Get all process steps (including inactive) for admin
 */
public function allProcessSteps()
{
    return $this->hasMany(ProcessStep::class, 'company_id', 'company_id')
                ->orderBy('step_number', 'asc');
}
    /**
 * Get the commitments for the company
 */
public function commitments()
{
    return $this->hasMany(Commitment::class, 'company_id', 'company_id')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc');
}

/**
 * Get all commitments (including inactive) for admin
 */
public function allCommitments()
{
    return $this->hasMany(Commitment::class, 'company_id', 'company_id')
                ->orderBy('display_order', 'asc');
}

    /**
     * Get the primary color with default
     */
    public function getPrimaryColorAttribute($value)
    {
        return $value ?? '#9333ea';
    }

    /**
     * Get the secondary color with default
     */
    public function getSecondaryColorAttribute($value)
    {
        return $value ?? '#00d9ff';
    }

    /**
     * Get full logo URL
     */
    public function getLogoUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    /**
     * Get full favicon URL
     */
    public function getFaviconUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }
}