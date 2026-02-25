<?php

namespace App\Http\Controllers\API;

use App\Models\RelatedProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RelatedProductController extends Controller
{
    /**
     * Display a listing of related products.
     */
    public function index(Request $request)
    {
        $query = RelatedProduct::with(['product', 'relatedProduct']);

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by relationship type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Order by display order
        $query->ordered();

        $related = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Related products retrieved successfully',
            'data' => $related
        ]);
    }

    /**
     * Get related products for a specific product.
     */
    public function forProduct($productId, Request $request)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $type = $request->get('type');
        $related = RelatedProduct::getForProduct($productId, $type);

        // Also get products that have this as related (reverse relations)
        $reverseRelated = [];
        if ($request->boolean('include_reverse')) {
            $reverseQuery = RelatedProduct::with('product')
                ->where('related_product_id', $productId)
                ->ordered();

            if ($type) {
                $reverseQuery->ofType($type);
            }

            $reverseRelated = $reverseQuery->get()->map(function($item) {
                return [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'slug' => $item->product->slug,
                    'price' => $item->product->prices,
                    'primary_image' => $item->product->primary_image_url,
                    'relationship_type' => $item->relationship_type,
                    'relationship_label' => $item->relationship_type_label,
                    'relationship_badge' => $item->relationship_badge,
                    'display_order' => $item->display_order
                ];
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Related products retrieved successfully',
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug
                ],
                'related_products' => $related,
                'reverse_related' => $reverseRelated
            ]
        ]);
    }

    /**
     * Get relationship types.
     */
    public function getTypes()
    {
        return response()->json([
            'success' => true,
            'data' => RelatedProduct::getRelationshipTypes()
        ]);
    }

    /**
     * Store a newly created related product relationship.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'related_product_id' => 'required|string|exists:products,id|different:product_id',
            'relationship_type' => 'nullable|in:upsell,cross-sell,alternative',
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
            // Check if relationship already exists
            $exists = RelatedProduct::where('product_id', $request->product_id)
                ->where('related_product_id', $request->related_product_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This relationship already exists'
                ], 400);
            }

            $related = RelatedProduct::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Related product created successfully',
                'data' => $related->load(['product', 'relatedProduct'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create related product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple related products at once.
     */
    public function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'relations' => 'required|array',
            'relations.*.related_product_id' => 'required|string|exists:products,id|different:product_id',
            'relations.*.relationship_type' => 'nullable|in:upsell,cross-sell,alternative',
            'relations.*.display_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $created = [];
            $errors = [];

            foreach ($request->relations as $index => $item) {
                // Check if relationship already exists
                $exists = RelatedProduct::where('product_id', $request->product_id)
                    ->where('related_product_id', $item['related_product_id'])
                    ->exists();

                if ($exists) {
                    $errors[] = "Product {$item['related_product_id']} already related";
                    continue;
                }

                if ($request->product_id === $item['related_product_id']) {
                    $errors[] = "Cannot relate product to itself";
                    continue;
                }

                $related = RelatedProduct::create([
                    'product_id' => $request->product_id,
                    'related_product_id' => $item['related_product_id'],
                    'relationship_type' => $item['relationship_type'] ?? 'cross-sell',
                    'display_order' => $item['display_order'] ?? $index
                ]);

                $created[] = $related->load('relatedProduct');
            }

            DB::commit();

            $message = count($created) . ' relationships created successfully';
            if (!empty($errors)) {
                $message .= '. Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'created' => $created,
                    'errors' => $errors
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create related products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified related product relationship.
     */
    public function show($productId, $relatedId)
    {
        $related = RelatedProduct::with(['product', 'relatedProduct'])
            ->where('product_id', $productId)
            ->where('related_product_id', $relatedId)
            ->first();

        if (!$related) {
            return response()->json([
                'success' => false,
                'message' => 'Related product relationship not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Related product retrieved successfully',
            'data' => $related
        ]);
    }

    /**
     * Update the specified related product relationship.
     */
    public function update(Request $request, $productId, $relatedId)
    {
        $related = RelatedProduct::where('product_id', $productId)
            ->where('related_product_id', $relatedId)
            ->first();

        if (!$related) {
            return response()->json([
                'success' => false,
                'message' => 'Related product relationship not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'relationship_type' => 'nullable|in:upsell,cross-sell,alternative',
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
            $related->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Related product updated successfully',
                'data' => $related->load(['product', 'relatedProduct'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update related product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified related product relationship.
     */
    public function destroy($productId, $relatedId)
    {
        $related = RelatedProduct::where('product_id', $productId)
            ->where('related_product_id', $relatedId)
            ->first();

        if (!$related) {
            return response()->json([
                'success' => false,
                'message' => 'Related product relationship not found'
            ], 404);
        }

        try {
            $related->delete();

            return response()->json([
                'success' => true,
                'message' => 'Related product relationship deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete related product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete relationships.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'relations' => 'required|array',
            'relations.*.product_id' => 'required|string|exists:products,id',
            'relations.*.related_product_id' => 'required|string|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $deleted = 0;
            $errors = [];

            foreach ($request->relations as $item) {
                $related = RelatedProduct::where('product_id', $item['product_id'])
                    ->where('related_product_id', $item['related_product_id'])
                    ->first();

                if ($related) {
                    $related->delete();
                    $deleted++;
                } else {
                    $errors[] = "Relation {$item['product_id']}-{$item['related_product_id']} not found";
                }
            }

            DB::commit();

            $message = $deleted . ' relationships deleted successfully';
            if (!empty($errors)) {
                $message .= '. Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted_count' => $deleted,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete relationships',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder relationships for a product.
     */
    public function reorder(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.related_product_id' => 'required|string|exists:products,id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            foreach ($request->orders as $order) {
                RelatedProduct::where('product_id', $productId)
                    ->where('related_product_id', $order['related_product_id'])
                    ->update(['display_order' => $order['display_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Relationships reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder relationships',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upsell products for a product.
     */
    public function getUpsells($productId)
    {
        return $this->getForProduct($productId, 'upsell');
    }

    /**
     * Get cross-sell products for a product.
     */
    public function getCrossSells($productId)
    {
        return $this->getForProduct($productId, 'cross-sell');
    }

    /**
     * Get alternative products for a product.
     */
    public function getAlternatives($productId)
    {
        return $this->getForProduct($productId, 'alternative');
    }

    /**
     * Helper method to get related products by type
     */
    private function getForProduct($productId, $type)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $related = RelatedProduct::with('relatedProduct')
            ->where('product_id', $productId)
            ->ofType($type)
            ->ordered()
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->relatedProduct->id,
                    'title' => $item->relatedProduct->title,
                    'slug' => $item->relatedProduct->slug,
                    'price' => $item->relatedProduct->prices,
                    'primary_image' => $item->relatedProduct->primary_image_url,
                    'display_order' => $item->display_order
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $related
        ]);
    }
}