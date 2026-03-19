<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index(Request $request)
    {
        try {
            $query = Blog::query();

            // Search functionality
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Filter by status
            // if ($request->has('status')) {
            //     if ($request->status === 'published') {
            //         $query->published();
            //     } elseif ($request->status === 'draft') {
            //         $query->draft();
            //     }
            // } else {
            //     // Default: show only published for public
            //     $query->published();
            // }

            // // Filter by category
            // if ($request->has('category_id')) {
            //     $query->where('category_id', $request->category_id);
            // }

            // // Filter by author
            // if ($request->has('author_id')) {
            //     $query->where('author_id', $request->author_id);
            // }

            // Sort functionality
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Allow only specific fields to sort
            $allowedSortFields = ['title', 'created_at', 'published_at', 'status'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max(1, $perPage), 100); // Limit between 1-100

            $blogs = $query->paginate($perPage);
         


            return response()->json([
                'success' => true,
                'data' => $blogs->items(),
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
     * Store a newly created blog.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                // 'thumbnail' => 'nullable|string|max:255',
                'published_at' => 'nullable|date',
                'category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                // 'status' => 'nullable|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create slug from title
            $slug = Str::slug($request->title);
            
            // Check if slug exists and make it unique
            $count = Blog::where('slug', 'LIKE', $slug . '%')->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
            //upload thumbnail
            if ($request->hasFile('image')) {
                $thumbnail = $request->file('image');
              
                $thumbnailPath = $thumbnail->store('images', 'public');
            } else {
                $thumbnailPath = null;
            }

            // Create blog
            $blog = Blog::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'featured_image' =>  $thumbnailPath,
                'category_id' => $request->category_id,
                'tags' => $request->tags,
                'status' => $request->status ?? 'draft',
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'author_id' => 1, // Default author ID (change as needed)
                'published_at' => $request->status === 'published' ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $blog,
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
     */
    public function show($identifier)
    {
        try {
            $blog = Blog::where('id', $identifier)
                        ->orWhere('slug', $identifier)
                        ->firstOrFail();

            // If blog is draft, only authenticated users can view
            if ($blog->status === 'draft' && !Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $blog,
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
     * Update the specified blog.
     */
    public function update(Request $request, $id)
    {
        try {
            // Find blog
            $blog = Blog::findOrFail($id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'excerpt' => 'nullable|string|max:500',
                'featured_image' => 'nullable|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'status' => 'nullable|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update slug if title is changed
            if ($request->has('title') && $request->title !== $blog->title) {
                $slug = Str::slug($request->title);
                
                // Check if slug exists for other blogs
                $count = Blog::where('slug', 'LIKE', $slug . '%')
                            ->where('id', '!=', $blog->id)
                            ->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }
                $blog->slug = $slug;
            }

            // Update other fields
            $blog->fill($request->except(['slug', 'author_id']));
            
            // Handle published_at based on status
            if ($request->has('status')) {
                if ($request->status === 'published' && $blog->status !== 'published') {
                    $blog->published_at = now();
                } elseif ($request->status !== 'published') {
                    $blog->published_at = null;
                }
            }

            $blog->save();

            return response()->json([
                'success' => true,
                'data' => $blog,
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
     * Remove the specified blog.
     */
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully.'
            ], 200);
            
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
     * Search blogs.
     */
    public function search(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            if (strlen($search) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Search query too short.'
                ]);
            }

            $blogs = Blog::published()
                        ->search($search)
                        ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at'])
                        ->limit(10)
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $blogs,
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
     */
    public function latest(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $limit = min(max(1, $limit), 20);

            $blogs = Blog::published()
                        ->orderBy('published_at', 'desc')
                        ->limit($limit)
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $blogs,
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
}