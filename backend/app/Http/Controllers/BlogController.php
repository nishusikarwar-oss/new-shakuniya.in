<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
        try {
            $query = Blog::query();

            // Search functionality
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max(1, $perPage), 100); // Limit between 1-100

            $blogs = $query->paginate($perPage);
           

            return response()->json([
                'success' => true,
                'data' => BlogResource::collection($blogs),
                'meta' => [
                    'total' => $blogs->total(),
                    'current_page' => $blogs->currentPage(),
                    'last_page' => $blogs->lastPage(),
                    'per_page' => $blogs->perPage(),
                    'from' => $blogs->firstItem(),
                    'to' => $blogs->lastItem(),
                ],
                'message' => 'Blogs retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blogs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created blog in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blogs,slug',
                'excerpt' => 'nullable|string',
                'content' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'author' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['title', 'slug', 'excerpt', 'content', 'author']);
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
                
                // Ensure slug is unique
                $count = 1;
                $originalSlug = $data['slug'];
                while (Blog::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $filename = 'blog_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $path = $thumbnail->storeAs('blogs/thumbnails', $filename, 'public');
                $data['thumbnail'] = $path;
            }

            $blog = Blog::create($data);

            return response()->json([
                'success' => true,
                'data' => new BlogResource($blog),
                'message' => 'Blog created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified blog.
     *
     * @param  string  $identifier
     * @return \Illuminate\Http\Response
     */
    public function show($identifier)
    {
        try {
            $blog = Blog::where('id', $identifier)
                        ->orWhere('slug', $identifier)
                        ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new BlogResource($blog),
                'message' => 'Blog retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified blog in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:blogs,slug,' . $id,
                'excerpt' => 'nullable|string',
                'content' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'author' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['title', 'slug', 'excerpt', 'content', 'author']);
            
            // Handle slug update
            if ($request->has('title') && !$request->has('slug')) {
                $data['slug'] = Str::slug($data['title']);
                
                // Ensure slug is unique
                $count = 1;
                $originalSlug = $data['slug'];
                while (Blog::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($blog->thumbnail) {
                    Storage::disk('public')->delete($blog->thumbnail);
                }

                $thumbnail = $request->file('thumbnail');
                $filename = 'blog_' . time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $path = $thumbnail->storeAs('blogs/thumbnails', $filename, 'public');
                $data['thumbnail'] = $path;
            }

            // Handle thumbnail removal
            if ($request->has('remove_thumbnail') && $request->remove_thumbnail == true) {
                if ($blog->thumbnail) {
                    Storage::disk('public')->delete($blog->thumbnail);
                    $data['thumbnail'] = null;
                }
            }

            $blog->update($data);

            return response()->json([
                'success' => true,
                'data' => new BlogResource($blog),
                'message' => 'Blog updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified blog from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Delete thumbnail if exists
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search blogs by keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $blogs = Blog::search($request->keyword)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => BlogResource::collection($blogs),
                'meta' => [
                    'total' => $blogs->total(),
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                ],
                'message' => 'Search results retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search blogs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest blogs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function latest(Request $request)
    {
        try {
            $limit = min($request->get('limit', 5), 20); // Max 20 blogs
            $blogs = Blog::orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();

            return response()->json([
                'success' => true,
                'data' => BlogResource::collection($blogs),
                'message' => 'Latest blogs retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve latest blogs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload thumbnail for blog.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadThumbnail(Request $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            // Delete old thumbnail if exists
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }

            $thumbnail = $request->file('thumbnail');
            $filename = 'blog_' . $id . '_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->storeAs('blogs/thumbnails', $filename, 'public');

            $blog->update(['thumbnail' => $path]);

            return response()->json([
                'success' => true,
                'data' => new BlogResource($blog),
                'message' => 'Thumbnail uploaded successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload thumbnail.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}