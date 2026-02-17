<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
   public function index(Request $request)
{  
    $query = Category::query();
   
    // Search by category name (this column exists)
    if ($request->has('search')) {
        $query->where('category_name', 'like', '%' . $request->search . '%');
    }
    
    // Simple ordering by category name (since no sort_order column)
    $query->orderBy('category_name');
    
    $categories = $query->paginate($request->get('per_page', 15));
    
    return response()->json([
        'success' => true,
        'data' => $categories
    ]);
}
    /**
     * Get parent categories only.
     */
    public function getParents()
    {
        $parents = Category::parents()
            ->withCount('children')
            ->ordered()
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $parents
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category->load('parent')
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($identifier)
    {
        $category = is_numeric($identifier) 
            ? Category::with('parent', 'children')->find($identifier)
            : Category::with('parent', 'children')->where('slug', $identifier)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category->load('parent')
        ]);
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category status toggled successfully',
            'is_active' => $category->is_active
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Bulk delete categories.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if any category has children
        $categoriesWithChildren = Category::whereIn('id', $request->ids)
            ->has('children')
            ->count();

        if ($categoriesWithChildren > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete categories that have subcategories'
            ], 422);
        }

        Category::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categories deleted successfully'
        ]);
    }
}