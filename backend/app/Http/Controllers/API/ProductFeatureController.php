<?php

namespace App\Http\Controllers\API;

use App\Models\ProductFeature;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductFeatureController extends Controller
{
    /**
     * Display a listing of product features.
     */
    public function index(Request $request)
    {
        $query = ProductFeature::with('product');

        // Filter by product
        if ($request->has('product_id')) {
            $query->forProduct($request->product_id);
        }

        // Filter active only
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Order by display order
        $query->ordered();

        $features = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Product features retrieved successfully',
            'data' => $features
        ]);
    }

    /**
     * Get features for a specific product.
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

        $features = ProductFeature::forProduct($productId)
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Product features retrieved successfully',
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug
                ],
                'features' => $features
            ]
        ]);
    }

    /**
     * Get available icons list.
     */
    public function getIcons()
    {
        return response()->json([
            'success' => true,
            'data' => ProductFeature::getIconOptions()
        ]);
    }

    /**
     * Store a newly created product feature.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'icon_name' => 'required|string|max:50|in:' . implode(',', ProductFeature::getAvailableIcons()),
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
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
            $feature = ProductFeature::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product feature created successfully',
                'data' => $feature->load('product')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product feature.
     */
    public function show($id)
    {
        try {
            $feature = ProductFeature::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product feature retrieved successfully',
                'data' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }
    }

    /**
     * Update the specified product feature.
     */
    public function update(Request $request, $id)
    {
        try {
            $feature = ProductFeature::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'icon_name' => 'sometimes|required|string|max:50|in:' . implode(',', ProductFeature::getAvailableIcons()),
                'title' => 'sometimes|required|string|max:200',
                'description' => 'nullable|string',
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

            $feature->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product feature updated successfully',
                'data' => $feature->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }
    }

    /**
     * Remove the specified product feature.
     */
    public function destroy($id)
    {
        try {
            $feature = ProductFeature::findOrFail($id);
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product feature deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $feature = ProductFeature::findOrFail($id);
            $feature->is_active = !$feature->is_active;
            $feature->save();

            return response()->json([
                'success' => true,
                'message' => 'Feature status updated successfully',
                'is_active' => $feature->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }
    }

    /**
     * Reorder features for a product
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'orders' => 'required|array',
            'orders.*.id' => 'required|string|exists:product_features,id',
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
                ProductFeature::where('id', $order['id'])
                    ->where('product_id', $request->product_id)
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
            'ids.*' => 'string|exists:product_features,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            ProductFeature::whereIn('id', $request->ids)->delete();

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
     * Clone features from one product to another
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
            $sourceFeatures = ProductFeature::where('product_id', $request->from_product_id)->get();
            $clonedCount = 0;

            foreach ($sourceFeatures as $feature) {
                $newFeature = $feature->replicate();
                $newFeature->product_id = $request->to_product_id;
                $newFeature->save();
                $clonedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$clonedCount} features cloned successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clone features',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}