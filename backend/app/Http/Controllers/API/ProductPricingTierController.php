<?php

namespace App\Http\Controllers\API;

use App\Models\ProductPricingTier;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductPricingTierController extends Controller
{
    /**
     * Display a listing of pricing tiers.
     */
    public function index(Request $request)
    {
        $query = ProductPricingTier::with('product');

        // Filter by product
        if ($request->has('product_id')) {
            $query->forProduct($request->product_id);
        }

        // Filter active only
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter popular only
        if ($request->boolean('popular_only')) {
            $query->popular();
        }

        // Filter by billing period
        if ($request->has('billing_period')) {
            $query->withBillingPeriod($request->billing_period);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('tier_name', 'LIKE', "%{$search}%");
        }

        // Order by display order
        $query->ordered();

        $tiers = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Pricing tiers retrieved successfully',
            'data' => $tiers
        ]);
    }

    /**
     * Get tiers for a specific product.
     */
    public function forProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $tiers = ProductPricingTier::forProduct($productId)
            ->active()
            ->ordered()
            ->get()
            ->map(function($tier) {
                return [
                    'id' => $tier->id,
                    'tier_name' => $tier->tier_name,
                    'prices' => $tier->prices,
                    'price_with_period' => $tier->price_with_period,
                    'billing_period' => $tier->billing_period,
                    'billing_period_label' => $tier->billing_period_label,
                    'is_popular' => $tier->is_popular,
                    'popular_badge' => $tier->popular_badge,
                    'yearly_savings' => $tier->yearly_savings,
                    'display_order' => $tier->display_order
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Pricing tiers retrieved successfully',
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug
                ],
                'tiers' => $tiers
            ]
        ]);
    }

    /**
     * Get popular tier for a product.
     */
    public function getPopular($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $tier = ProductPricingTier::forProduct($productId)
            ->popular()
            ->active()
            ->first();

        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'No popular tier found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Popular tier retrieved successfully',
            'data' => $tier->load('product')
        ]);
    }

    /**
     * Get billing periods list.
     */
    public function getBillingPeriods()
    {
        return response()->json([
            'success' => true,
            'data' => ProductPricingTier::getBillingPeriods()
        ]);
    }

    /**
     * Get tier names list.
     */
    public function getTierNames()
    {
        return response()->json([
            'success' => true,
            'data' => ProductPricingTier::getTierNames()
        ]);
    }

    /**
     * Store a newly created pricing tier.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'tier_name' => 'required|string|max:100',
            'price_usd' => 'nullable|numeric|min:0',
            'price_inr' => 'nullable|numeric|min:0',
            'billing_period' => 'nullable|in:monthly,yearly,one-time',
            'is_popular' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // If this is marked as popular, remove popular from other tiers
            if ($request->boolean('is_popular')) {
                ProductPricingTier::forProduct($request->product_id)
                    ->update(['is_popular' => false]);
            }

            $tier = ProductPricingTier::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pricing tier created successfully',
                'data' => $tier->load('product')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pricing tier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified pricing tier.
     */
    public function show($id)
    {
        try {
            $tier = ProductPricingTier::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Pricing tier retrieved successfully',
                'data' => $tier
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing tier not found'
            ], 404);
        }
    }

    /**
     * Update the specified pricing tier.
     */
    public function update(Request $request, $id)
    {
        try {
            $tier = ProductPricingTier::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tier_name' => 'sometimes|required|string|max:100',
                'price_usd' => 'nullable|numeric|min:0',
                'price_inr' => 'nullable|numeric|min:0',
                'billing_period' => 'nullable|in:monthly,yearly,one-time',
                'is_popular' => 'nullable|boolean',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If setting as popular, remove popular from other tiers
            if ($request->boolean('is_popular') && !$tier->is_popular) {
                ProductPricingTier::forProduct($tier->product_id)
                    ->where('id', '!=', $tier->id)
                    ->update(['is_popular' => false]);
            }

            $tier->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pricing tier updated successfully',
                'data' => $tier->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing tier not found'
            ], 404);
        }
    }

    /**
     * Remove the specified pricing tier.
     */
    public function destroy($id)
    {
        try {
            $tier = ProductPricingTier::findOrFail($id);
            $tier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pricing tier deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing tier not found'
            ], 404);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $tier = ProductPricingTier::findOrFail($id);
            $tier->is_active = !$tier->is_active;
            $tier->save();

            return response()->json([
                'success' => true,
                'message' => 'Tier status updated successfully',
                'is_active' => $tier->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tier not found'
            ], 404);
        }
    }

    /**
     * Toggle popular status
     */
    public function togglePopular($id)
    {
        try {
            $tier = ProductPricingTier::findOrFail($id);

            // If setting as popular, remove popular from other tiers
            if (!$tier->is_popular) {
                ProductPricingTier::forProduct($tier->product_id)
                    ->where('id', '!=', $tier->id)
                    ->update(['is_popular' => false]);
            }

            $tier->is_popular = !$tier->is_popular;
            $tier->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular status updated successfully',
                'is_popular' => $tier->is_popular
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tier not found'
            ], 404);
        }
    }

    /**
     * Reorder tiers for a product
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'orders' => 'required|array',
            'orders.*.id' => 'required|string|exists:product_pricing_tiers,id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->orders as $order) {
                ProductPricingTier::where('id', $order['id'])
                    ->where('product_id', $request->product_id)
                    ->update(['display_order' => $order['display_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tiers reordered successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder tiers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete tiers
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'string|exists:product_pricing_tiers,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            ProductPricingTier::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' tiers deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tiers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clone tiers from one product to another
     */
    public function clone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_product_id' => 'required|string|exists:products,id',
            'to_product_id' => 'required|string|exists:products,id|different:from_product_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sourceTiers = ProductPricingTier::where('product_id', $request->from_product_id)->get();
            $clonedCount = 0;

            foreach ($sourceTiers as $tier) {
                $newTier = $tier->replicate();
                $newTier->product_id = $request->to_product_id;
                $newTier->is_popular = false; // Don't clone popular status
                $newTier->save();
                $clonedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$clonedCount} pricing tiers cloned successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clone pricing tiers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}