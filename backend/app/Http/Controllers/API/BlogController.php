<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
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
        // print_r($blogs);

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($identifier)
    {
        try {
            $blog = Blog::where('id', $identifier)
                        ->orWhere('slug', $identifier)
                        ->firstOrFail();

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        //
    }
}
