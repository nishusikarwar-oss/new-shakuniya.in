<?php

namespace App\Http\Controllers\API;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of product images.
     */
    public function index(Request $request)
    {
        $query = ProductImage::with('product');

        // Filter by product
        if ($request->has('product_id')) {
            $query->forProduct($request->product_id);
        }

        // Filter primary only
        if ($request->boolean('primary_only')) {
            $query->primary();
        }

        // Order by display order
        $query->ordered();

        $images = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Product images retrieved successfully',
            'data' => $images
        ]);
    }

    /**
     * Get images for a specific product.
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

        $images = ProductImage::forProduct($productId)
            ->ordered()
            ->get()
            ->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'sizes' => $image->sizes,
                    'alt_text' => $image->alt_text,
                    'is_primary' => $image->is_primary,
                    'display_order' => $image->display_order
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Product images retrieved successfully',
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug
                ],
                'images' => $images
            ]
        ]);
    }

    /**
     * Get primary image for a product.
     */
    public function getPrimary($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $image = ProductImage::forProduct($productId)
            ->primary()
            ->first();

        if (!$image) {
            // Return default placeholder
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => asset('images/default-product.png'),
                    'alt_text' => $product->title
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $image->id,
                'url' => $image->image_url,
                'sizes' => $image->sizes,
                'alt_text' => $image->alt_text ?? $product->title
            ]
        ]);
    }

    /**
     * Store a newly created product image.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
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
            // Handle image upload
            $path = $request->file('image')->store('products', 'public');

            $data = [
                'product_id' => $request->product_id,
                'image_url' => $path,
                'alt_text' => $request->alt_text,
                'is_primary' => $request->boolean('is_primary', false),
                'display_order' => $request->get('display_order', 0)
            ];

            $image = ProductImage::create($data);

            // If this is primary, remove primary from others
            if ($image->is_primary) {
                $image->setAsPrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Product image uploaded successfully',
                'data' => $image->load('product')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store multiple images at once.
     */
    public function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploaded = [];
            $startOrder = ProductImage::forProduct($request->product_id)->max('display_order') + 1;

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                
                $image = ProductImage::create([
                    'product_id' => $request->product_id,
                    'image_url' => $path,
                    'display_order' => $startOrder + $index
                ]);
                
                $uploaded[] = $image;
            }

            return response()->json([
                'success' => true,
                'message' => count($uploaded) . ' images uploaded successfully',
                'data' => $uploaded
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product image.
     */
    public function show($id)
    {
        try {
            $image = ProductImage::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product image retrieved successfully',
                'data' => $image
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found'
            ], 404);
        }
    }

    /**
     * Update the specified product image.
     */
    public function update(Request $request, $id)
    {
        try {
            $image = ProductImage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'alt_text' => 'nullable|string|max:255',
                'is_primary' => 'nullable|boolean',
                'display_order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except(['image']);

            // Handle new image upload
            if ($request->hasFile('image')) {
                // Delete old image
                Storage::disk('public')->delete($image->image_url);
                
                $path = $request->file('image')->store('products', 'public');
                $data['image_url'] = $path;
            }

            $wasPrimary = $image->is_primary;
            $image->update($data);

            // If setting as primary, handle primary logic
            if ($request->boolean('is_primary') && !$wasPrimary) {
                $image->setAsPrimary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Product image updated successfully',
                'data' => $image->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found'
            ], 404);
        }
    }

    /**
     * Remove the specified product image.
     */
    public function destroy($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            
            // Delete file from storage
            Storage::disk('public')->delete($image->image_url);
            
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found'
            ], 404);
        }
    }

    /**
     * Set image as primary.
     */
    public function setPrimary($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            $image->setAsPrimary();

            return response()->json([
                'success' => true,
                'message' => 'Primary image set successfully',
                'data' => $image->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found'
            ], 404);
        }
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|exists:products,id',
            'orders' => 'required|array',
            'orders.*.id' => 'required|string|exists:product_images,id',
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
                ProductImage::where('id', $order['id'])
                    ->where('product_id', $request->product_id)
                    ->update(['display_order' => $order['display_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images reordered successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete images.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'string|exists:product_images,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete files from storage
            $images = ProductImage::whereIn('id', $request->ids)->get();
            foreach ($images as $image) {
                Storage::disk('public')->delete($image->image_url);
            }
            
            ProductImage::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' images deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}