<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GalleryImageController extends Controller
{
    /**
     * Display a listing of gallery images.
     */
    public function index(Request $request)
    {
        try {
            $query = GalleryImage::query();

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $perPage = min(max(1, $perPage), 100);

            $images = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => GalleryImageResource::collection($images),
                'meta' => [
                    'total' => $images->total(),
                    'current_page' => $images->currentPage(),
                    'last_page' => $images->lastPage(),
                    'per_page' => $images->perPage(),
                    'from' => $images->firstItem(),
                    'to' => $images->lastItem(),
                ],
                'message' => 'Gallery images retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery images.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created gallery image.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer',
                'image_name' => 'required|string|max:255',
                'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['category_id', 'image_name']);

            // Handle image upload
            if ($request->hasFile('image_url')) {
                $image = $request->file('image_url');
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('gallery', $filename, 'public');
                $data['image_url'] = $path;
            }

            $galleryImage = GalleryImage::create($data);

            return response()->json([
                'success' => true,
                'data' => new GalleryImageResource($galleryImage),
                'message' => 'Gallery image created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create gallery image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified gallery image.
     */
    public function show($id)
    {
        try {
            $image = GalleryImage::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new GalleryImageResource($image),
                'message' => 'Gallery image retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery image not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified gallery image.
     */
    public function update(Request $request, $id)
    {
        try {
            $image = GalleryImage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'category_id' => 'sometimes|required|integer',
                'image_name' => 'sometimes|required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['category_id', 'image_name']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($image->image_url) {
                    Storage::disk('public')->delete($image->image_url);
                }

                $newImage = $request->file('image');
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $newImage->getClientOriginalExtension();
                $path = $newImage->storeAs('gallery', $filename, 'public');
                $data['image_url'] = $path;
            }

            $image->update($data);

            return response()->json([
                'success' => true,
                'data' => new GalleryImageResource($image),
                'message' => 'Gallery image updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery image not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update gallery image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified gallery image.
     */
    public function destroy($id)
    {
        try {
            $image = GalleryImage::findOrFail($id);

            // Delete image file
            if ($image->image_url) {
                Storage::disk('public')->delete($image->image_url);
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gallery image deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery image not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete gallery image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get images by category.
     */
    public function byCategory($categoryId)
    {
        try {
            $images = GalleryImage::where('category_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => GalleryImageResource::collection($images),
                'total' => $images->count(),
                'message' => 'Gallery images by category retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gallery images by category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}