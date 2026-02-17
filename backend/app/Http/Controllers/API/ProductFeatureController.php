<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductFeatureResource;
use App\Models\Product;
use App\Models\ProductFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductFeatureController extends Controller
{
    /**
     * Display all product features.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = ProductFeature::with('product');

            // Filter by product_id
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('feature', 'like', "%{$search}%");
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $perPage = min(max(1, $perPage), 100);

            $features = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $features->items(),
                'meta' => [
                    'total' => $features->total(),
                    'current_page' => $features->currentPage(),
                    'last_page' => $features->lastPage(),
                    'per_page' => $features->perPage(),
                    'from' => $features->firstItem(),
                    'to' => $features->lastItem(),
                ],
                'message' => 'Product features retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product features.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get features by product ID.
     * 
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function byProduct($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            $features = ProductFeature::where('product_id', $productId)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductFeatureResource::collection($features),
                'total' => $features->count(),
                'product_id' => (int) $productId,
                'product_title' => $product->title,
                'message' => 'Product features retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product features.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple features for a product.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBulk(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'features' => 'required|array|min:1',
                'features.*' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $features = [];
            foreach ($request->features as $featureText) {
                $features[] = ProductFeature::create([
                    'product_id' => $request->product_id,
                    'feature' => $featureText,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => ProductFeatureResource::collection($features),
                'message' => count($features) . ' product features created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product features.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a single product feature.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'feature' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $feature = ProductFeature::create([
                'product_id' => $request->product_id,
                'feature' => $request->feature,
            ]);

            return response()->json([
                'success' => true,
                'data' => new ProductFeatureResource($feature),
                'message' => 'Product feature created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product feature.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product feature.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $feature = ProductFeature::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new ProductFeatureResource($feature),
                'message' => 'Product feature retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product feature.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product feature.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $feature = ProductFeature::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'feature' => 'sometimes|required|string|max:255',
                'product_id' => 'sometimes|required|integer|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            if ($request->has('feature')) {
                $feature->feature = $request->feature;
            }

            if ($request->has('product_id')) {
                $feature->product_id = $request->product_id;
            }

            $feature->save();

            return response()->json([
                'success' => true,
                'data' => new ProductFeatureResource($feature),
                'message' => 'Product feature updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product feature.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product feature.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $feature = ProductFeature::findOrFail($id);
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product feature deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product feature not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product feature.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple features at once.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyBulk(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|integer|exists:product_features,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $count = ProductFeature::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $count . ' product features deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product features.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}