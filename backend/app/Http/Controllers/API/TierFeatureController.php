<?php

namespace App\Http\Controllers\API;

use App\Models\TierFeature;
use App\Models\ProductPricingTier;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TierFeatureController extends Controller
{
    /**
     * Display a listing of tier features.
     */
    public function index(Request $request)
    {
        $query = TierFeature::with('tier.product');

        // Filter by tier
        if ($request->has('tier_id')) {
            $query->forTier($request->tier_id);
        }

        // Filter by product (through tier)
        if ($request->has('product_id')) {
            $query->whereHas('tier', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter available only
        if ($request->boolean('available_only')) {
            $query->available();
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('feature_description', 'LIKE', "%{$search}%");
        }

        // Order by display order
        $query->ordered();

        $features = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Tier features retrieved successfully',
            'data' => $features
        ]);
    }

    /**
     * Get features for a specific tier.
     */
    public function forTier($tierId)
    {
        $tier = ProductPricingTier::with('product')->find($tierId);

        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing tier not found'
            ], 404);
        }

        $features = TierFeature::forTier($tierId)
            ->ordered()
            ->get()
            ->map(function($feature) {
                return [
                    'id' => $feature->id,
                    'description' => $feature->feature_description,
                    'excerpt' => $feature->excerpt,
                    'is_available' => $feature->is_available,
                    'availability_icon' => $feature->availability_icon,
                    'availability_color' => $feature->availability_color,
                    'status_badge' => $feature->status_badge,
                    'display_order' => $feature->display_order
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Tier features retrieved successfully',
            'data' => [
                'tier' => [
                    'id' => $tier->id,
                    'tier_name' => $tier->tier_name,
                    'product' => [
                        'id' => $tier->product->id,
                        'title' => $tier->product->title,
                        'slug' => $tier->product->slug
                    ]
                ],
                'features' => $features
            ]
        ]);
    }

    /**
     * Get comparison table for a product.
     */
    public function getComparison($productId)
    {
        $product = Product::with('pricingTiers')->find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $tiers = $product->pricingTiers;
        
        if ($tiers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pricing tiers found for this product'
            ], 404);
        }

        // Get all unique features across tiers
        $allFeatures = collect();
        foreach ($tiers as $tier) {
            $features = TierFeature::forTier($tier->id)->ordered()->get();
            $allFeatures = $allFeatures->merge($features);
        }
        
        // Get unique features by description
        $uniqueFeatures = $allFeatures->unique('feature_description')->values();

        $comparison = [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug
            ],
            'tiers' => $tiers->map(function($tier) {
                return [
                    'id' => $tier->id,
                    'name' => $tier->tier_name,
                    'price' => $tier->price_with_period,
                    'is_popular' => $tier->is_popular,
                    'features' => TierFeature::forTier($tier->id)
                        ->ordered()
                        ->get()
                        ->keyBy('feature_description')
                        ->map(function($feature) {
                            return [
                                'available' => $feature->is_available,
                                'icon' => $feature->availability_icon,
                                'color' => $feature->availability_color
                            ];
                        })
                ];
            }),
            'features' => $uniqueFeatures->map(function($feature) use ($tiers) {
                return [
                    'description' => $feature->feature_description,
                    'display_order' => $feature->display_order
                ];
            })->sortBy('display_order')->values()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Comparison table retrieved successfully',
            'data' => $comparison
        ]);
    }

    /**
     * Store a newly created tier feature.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|string|exists:product_pricing_tiers,id',
            'feature_description' => 'required|string',
            'is_available' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feature = TierFeature::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tier feature created successfully',
                'data' => $feature->load('tier.product')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tier feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple features for a tier at once.
     */
    public function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|string|exists:product_pricing_tiers,id',
            'features' => 'required|array',
            'features.*.description' => 'required|string',
            'features.*.is_available' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $created = [];
            $startOrder = TierFeature::forTier($request->tier_id)->max('display_order') + 1;

            foreach ($request->features as $index => $item) {
                $feature = TierFeature::create([
                    'tier_id' => $request->tier_id,
                    'feature_description' => $item['description'],
                    'is_available' => $item['is_available'] ?? true,
                    'display_order' => $startOrder + $index
                ]);
                $created[] = $feature;
            }

            return response()->json([
                'success' => true,
                'message' => count($created) . ' features created successfully',
                'data' => $created
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create features',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tier feature.
     */
    public function show($id)
    {
        try {
            $feature = TierFeature::with('tier.product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Tier feature retrieved successfully',
                'data' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tier feature not found'
            ], 404);
        }
    }

    /**
     * Update the specified tier feature.
     */
    public function update(Request $request, $id)
    {
        try {
            $feature = TierFeature::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'feature_description' => 'sometimes|required|string',
                'is_available' => 'nullable|boolean',
                'display_order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $feature->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tier feature updated successfully',
                'data' => $feature->load('tier.product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tier feature not found'
            ], 404);
        }
    }

    /**
     * Remove the specified tier feature.
     */
    public function destroy($id)
    {
        try {
            $feature = TierFeature::findOrFail($id);
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tier feature deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tier feature not found'
            ], 404);
        }
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability($id)
    {
        try {
            $feature = TierFeature::findOrFail($id);
            $feature->is_available = !$feature->is_available;
            $feature->save();

            return response()->json([
                'success' => true,
                'message' => 'Feature availability updated successfully',
                'is_available' => $feature->is_available
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }
    }

    /**
     * Reorder features for a tier
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|string|exists:product_pricing_tiers,id',
            'orders' => 'required|array',
            'orders.*.id' => 'required|string|exists:tier_features,id',
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
                TierFeature::where('id', $order['id'])
                    ->where('tier_id', $request->tier_id)
                    ->update(['display_order' => $order['display_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Features reordered successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder features',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete features
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'string|exists:tier_features,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            TierFeature::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' features deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete features',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Copy features from one tier to another
     */
    public function copyFromTier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_tier_id' => 'required|string|exists:product_pricing_tiers,id',
            'to_tier_id' => 'required|string|exists:product_pricing_tiers,id|different:from_tier_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sourceFeatures = TierFeature::where('tier_id', $request->from_tier_id)->get();
            $copiedCount = 0;

            // Delete existing features in target tier
            TierFeature::where('tier_id', $request->to_tier_id)->delete();

            foreach ($sourceFeatures as $feature) {
                $newFeature = $feature->replicate();
                $newFeature->tier_id = $request->to_tier_id;
                $newFeature->save();
                $copiedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$copiedCount} features copied successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to copy features',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}