<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['creator:id,name']);

        if ($request->boolean('active_only')) {
            $query->active();
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $query->ordered();

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products
        ]);
    }

    public function show($id)
    {
        try {
            $product = Product::with(['creator:id,name', 'updater:id,name', 'features'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function findBySlug($slug)
    {
        $product = Product::with(['features'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product
        ]);
    }

  public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:200',
        'slug' => 'nullable|string|max:100|unique:products,slug',
        'short_description' => 'nullable|string',
        'full_description' => 'nullable|string',
        'price_usd' => 'nullable|numeric|min:0',
        'price_inr' => 'nullable|numeric|min:0',
        'image_url' => 'nullable|string',
        'video_url' => 'nullable|string',
        'meta_title' => 'nullable|string|max:200',
        'meta_description' => 'nullable|string',
        'meta_keywords' => 'nullable|string',
        // 'tags' => 'nullable|string',
        'is_active' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422);
    }

    try {

        $data = $request->only([
            'title',
            'slug',
            'short_description',
            'full_description',
            'price_usd',
            'price_inr',
            'image_url',
            'video_url',
            'meta_title',
            'meta_description',
            'meta_keywords',
            // 'tags',
            'is_active'
        ]);

        // Auto generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Convert is_active to boolean
        $data['is_active'] = $request->input('is_active') ? 1 : 0;
        // File Uploads
            if ($request->hasFile('image')) {
                if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
                    Storage::disk('public')->delete($product->image_url);
                }
               $data['image_url'] = $request->file('image')->store('products', 'public');
            }
        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to create product',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:200',
                'slug' => 'nullable|string|max:100|unique:products,slug,' . $id . ',id',
                'short_description' => 'nullable|string',
                'full_description' => 'nullable|string',
                'price_usd' => 'nullable|numeric|min:0',
                'price_inr' => 'nullable|numeric|min:0',
                // 'image_url' => 'nullable|string',
                'video_url' => 'nullable|string',
                'video_text' => 'nullable|string',
                'is_active' => 'nullable',
                'meta_title' => 'nullable|string|max:200',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                // 'tags' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string',
                'og_description' => 'nullable|string',
                'twitter_title' => 'nullable|string',
                'twitter_description' => 'nullable|string',
               
                'features' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            
            $data = $request->except(['_method', 'featured_image', 'og_image', 'twitter_image', 'features', 'image_url']);

            // if ($request->filled('image')) {
            //     $data['image'] = $request->image;
            // }

            if (isset($data['is_active'])) {
                $data['is_active'] = $data['is_active'] === '1' || $data['is_active'] === 'true' || $data['is_active'] === true;
            }
           unset($data['image']);
            $product->update($data);

            // File Uploads
            if ($request->hasFile('image')) {
                if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
                    Storage::disk('public')->delete($product->image_url);
                }
                $product->image_url = $request->file('image')->store('products', 'public');
            }
        
            $product->save();

            // Sync Features
            if ($request->has('features') && is_array($request->features)) {
                $product->features()->delete();
                foreach ($request->features as $index => $featureData) {
                    $product->features()->create([
                        'icon_name' => $featureData['icon_name'] ?? 'Zap',
                        'title' => $featureData['title'],
                        'description' => $featureData['description'] ?? null,
                        'display_order' => $index,
                        'is_active' => true
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load('features')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete associated images
            if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
                Storage::disk('public')->delete($product->image_url);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function toggleActive($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_active = !$product->is_active;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated',
                'is_active' => $product->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}
