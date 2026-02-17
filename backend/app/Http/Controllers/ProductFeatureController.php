<?php
// app/Http/Controllers/API/ProductFeatureController.php

namespace App\Http\Controllers\API;

use App\Models\ProductFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductFeatureController extends Controller
{
    /**
     * GET /api/product-features
     * List all features (with optional product filter)
     */
    public function index(Request $request)
    {
        $query = ProductFeature::with('product');
        
        // Filter by product_id if provided
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        $features = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $features,
            'total' => $features->count()
        ]);
    }

    /**
     * POST /api/product-features
     * Create a new product feature
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'feature' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $feature = ProductFeature::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product feature created successfully',
            'data' => $feature->load('product')
        ], 201);
    }

    /**
     * GET /api/product-features/{id}
     * Get single product feature
     */
    public function show($id)
    {
        $feature = ProductFeature::with('product')->find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * PUT /api/product-features/{id}
     * Update product feature
     */
    public function update(Request $request, $id)
    {
        $feature = ProductFeature::find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|required|exists:products,id',
            'feature' => 'sometimes|required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $feature->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product feature updated successfully',
            'data' => $feature->load('product')
        ]);
    }

    /**
     * DELETE /api/product-features/{id}
     * Delete product feature
     */
    public function destroy($id)
    {
        $feature = ProductFeature::find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product feature deleted successfully'
        ]);
    }

    /**
     * POST /api/products/{productId}/features/bulk
     * Bulk create features for a product
     */
    public function bulkStore(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'features' => 'required|array',
            'features.*' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $features = [];
        foreach ($request->features as $featureText) {
            $features[] = ProductFeature::create([
                'product_id' => $productId,
                'feature' => $featureText
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($features) . ' features created successfully',
            'data' => $features
        ], 201);
    }
}