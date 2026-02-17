<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max(1, $perPage), 50);

            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'meta' => [
                    'total' => $products->total(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
                'message' => 'Products retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all products without pagination.
     * 
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        try {
            $products = Product::orderBy('id', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'total' => $products->count(),
                'message' => 'All products retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:products,slug',
                'short_description' => 'nullable|string|max:500',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = [
                'title' => $request->title,
                'short_description' => $request->short_description,
            ];

            // Generate slug if not provided
            if ($request->has('slug') && !empty($request->slug)) {
                $data['slug'] = Str::slug($request->slug);
            } else {
                $data['slug'] = Str::slug($request->title);
                
                // Ensure slug is unique
                $count = 1;
                $originalSlug = $data['slug'];
                while (Product::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $filename = 'product_' . time() . '_' . uniqid() . '.' . $icon->getClientOriginalExtension();
                $icon->storeAs('products', $filename, 'public');
                $data['icon'] = $filename;
            }

            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     * 
     * @param  string  $identifier (id or slug)
     * @return \Illuminate\Http\Response
     */
    public function show($identifier)
    {
        try {
            $product = Product::where('id', $identifier)
                ->orWhere('slug', $identifier)
                ->with('features')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $id,
                'short_description' => 'nullable|string|max:500',
                'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = [];

            if ($request->has('title')) {
                $data['title'] = $request->title;
            }

            if ($request->has('short_description')) {
                $data['short_description'] = $request->short_description;
            }

            // Handle slug
            if ($request->has('slug') && !empty($request->slug)) {
                $data['slug'] = Str::slug($request->slug);
            } elseif ($request->has('title') && !$request->has('slug')) {
                $data['slug'] = Str::slug($request->title);
                
                $count = 1;
                $originalSlug = $data['slug'];
                while (Product::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle icon upload
            if ($request->hasFile('icon')) {
                // Delete old icon
                if ($product->icon) {
                    Storage::disk('public')->delete('products/' . $product->icon);
                }

                $icon = $request->file('icon');
                $filename = 'product_' . time() . '_' . uniqid() . '.' . $icon->getClientOriginalExtension();
                $icon->storeAs('products', $filename, 'public');
                $data['icon'] = $filename;
            }

            // Handle icon removal
            if ($request->has('remove_icon') && $request->remove_icon == true) {
                if ($product->icon) {
                    Storage::disk('public')->delete('products/' . $product->icon);
                    $data['icon'] = null;
                }
            }

            $product->update($data);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete icon
            if ($product->icon) {
                Storage::disk('public')->delete('products/' . $product->icon);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent products.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recent(Request $request)
    {
        try {
            $limit = min($request->get('limit', 5), 20);
            
            $products = Product::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products),
                'message' => 'Recent products retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent products.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product by slug.
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function bySlug($slug)
    {
        try {
            $product = Product::where('slug', $slug)
                ->with('features')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product),
                'message' => 'Product retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}