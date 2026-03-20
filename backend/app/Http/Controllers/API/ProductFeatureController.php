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
     * Store a newly created product feature.
     * This method receives productId from route and feature data from request body
     */
    public function store(Request $request, $productId)
    {
        // First check if product exists
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'icon_name' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feature = new ProductFeature();
            $feature->product_id = $productId;
            $feature->title = $request->title;
            $feature->description = $request->description;
            $feature->icon_name = $request->icon_name ?? 'Star';
            $feature->is_active = $request->is_active ?? true;
            $feature->display_order = $request->display_order ?? 0;
            $feature->save();

            return response()->json([
                'success' => true,
                'message' => 'Product feature created successfully',
                'data' => $feature
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

        $features = ProductFeature::where('product_id', $productId)
            ->orderBy('display_order')
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Product features retrieved successfully',
            'data' => $features
        ]);
    }

    /**
     * Remove the specified product feature.
     */
    public function destroy($productId, $featureId)
    {
        try {
            $feature = ProductFeature::where('product_id', $productId)
                ->where('id', $featureId)
                ->firstOrFail();
            
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
    
    // Optional: Add update method if needed
    public function update(Request $request, $productId, $featureId)
    {
        try {
            $feature = ProductFeature::where('product_id', $productId)
                ->where('id', $featureId)
                ->firstOrFail();
            
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:200',
                'description' => 'sometimes|string',
                'icon_name' => 'sometimes|string|max:50',
                'is_active' => 'sometimes|boolean',
                'display_order' => 'sometimes|integer|min:0'
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
                'data' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found'
            ], 404);
        }
    }
}