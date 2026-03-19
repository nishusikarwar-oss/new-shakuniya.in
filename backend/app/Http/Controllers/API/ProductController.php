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
            'slug' => 'nullable|string|max:100|unique:products',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'price_usd' => 'nullable|numeric|min:0',
            'price_inr' => 'nullable|numeric|min:0',
            'image_url' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_text' => 'nullable|string',
            'is_active' => 'nullable',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'twitter_title' => 'nullable|string',
            'twitter_description' => 'nullable|string',
            'schema_markup' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120',
            'og_image' => 'nullable|image|max:5120',
            'twitter_image' => 'nullable|image|max:5120',
            'features' => 'nullable|array',
            'features.*.icon_name' => 'required_with:features|string',
            'features.*.title' => 'required_with:features|string',
            'features.*.description' => 'nullable|string',
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
            $data = $request->except(['featured_image', 'og_image', 'twitter_image', 'features', 'image_url']);
            
            if ($request->filled('image_url')) {
                $data['image'] = $request->image_url;
            }

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($request->title);
            }

            // Fix boolean for is_active from FormData
            $data['is_active'] = $request->input('is_active') === '1' || $request->input('is_active') === 'true' || $request->input('is_active') === true;

            $product = Product::create($data);

            // File Uploads
            if ($request->hasFile('featured_image')) {
                $product->image = $request->file('featured_image')->store('products', 'public');
            }
            if ($request->hasFile('og_image')) {
                $product->og_image = $request->file('og_image')->store('seo', 'public');
            }
            if ($request->hasFile('twitter_image')) {
                $product->twitter_image = $request->file('twitter_image')->store('seo', 'public');
            }
            $product->save();

            // Features
            if ($request->has('features') && is_array($request->features)) {
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
                'message' => 'Product created successfully',
                'data' => $product->load('features')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
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
                'image_url' => 'nullable|string',
                'video_url' => 'nullable|string',
                'video_text' => 'nullable|string',
                'is_active' => 'nullable',
                'meta_title' => 'nullable|string|max:200',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'tags' => 'nullable|string',
                'canonical_url' => 'nullable|string',
                'og_title' => 'nullable|string',
                'og_description' => 'nullable|string',
                'twitter_title' => 'nullable|string',
                'twitter_description' => 'nullable|string',
                'schema_markup' => 'nullable|string',
                'featured_image' => 'nullable|image|max:5120',
                'og_image' => 'nullable|image|max:5120',
                'twitter_image' => 'nullable|image|max:5120',
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

            if ($request->filled('image_url')) {
                $data['image'] = $request->image_url;
            }

            if (isset($data['is_active'])) {
                $data['is_active'] = $data['is_active'] === '1' || $data['is_active'] === 'true' || $data['is_active'] === true;
            }

            $product->update($data);

            // File Uploads
            if ($request->hasFile('featured_image')) {
                if ($product->image && !str_starts_with($product->image, 'http')) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->image = $request->file('featured_image')->store('products', 'public');
            }
            if ($request->hasFile('og_image')) {
                if ($product->og_image && !str_starts_with($product->og_image, 'http')) {
                    Storage::disk('public')->delete($product->og_image);
                }
                $product->og_image = $request->file('og_image')->store('seo', 'public');
            }
            if ($request->hasFile('twitter_image')) {
                if ($product->twitter_image && !str_starts_with($product->twitter_image, 'http')) {
                    Storage::disk('public')->delete($product->twitter_image);
                }
                $product->twitter_image = $request->file('twitter_image')->store('seo', 'public');
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
            if ($product->image && !str_starts_with($product->image, 'http')) {
                Storage::disk('public')->delete($product->image);
            }
            if ($product->og_image && !str_starts_with($product->og_image, 'http')) {
                Storage::disk('public')->delete($product->og_image);
            }
            if ($product->twitter_image && !str_starts_with($product->twitter_image, 'http')) {
                Storage::disk('public')->delete($product->twitter_image);
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
