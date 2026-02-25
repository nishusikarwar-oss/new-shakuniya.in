<?php

namespace App\Http\Controllers\API;

use App\Models\CategorysModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategorysController extends Controller
{
    // GET /api/v1/categories
    public function index(Request $request)
    {
        $query = CategorysModel::with('parent');

        if ($request->boolean('active_only')) $query->active();
        if ($request->boolean('root_only')) $query->root();
        
        if ($request->has('parent_id')) {
            $request->parent_id === 'null' 
                ? $query->whereNull('parent_id') 
                : $query->where('parent_id', $request->parent_id);
        }

        if ($request->has('search')) $query->search($request->search);
        if ($request->boolean('tree')) {
            return response()->json([
                'success' => true,
                'data' => CategorysModel::getTree()
            ]);
        }
        if ($request->boolean('for_dropdown')) {
            return response()->json([
                'success' => true,
                'data' => CategorysModel::getForDropdown($request->boolean('include_inactive'))
            ]);
        }

        $categories = $query->ordered()->paginate($request->get('per_page', 15));
        return response()->json(['success' => true, 'data' => $categories]);
    }

    // GET /api/v1/categories/slug/{slug}
    public function findBySlug($slug)
    {
        $category = CategorysModel::with('parent', 'children')
            ->where('slug', $slug)->where('is_active', true)->first();

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $category->path = $category->path;
        return response()->json(['success' => true, 'data' => $category]);
    }

    // GET /api/v1/categories/{id}/children
    public function getChildren($id)
    {
        $category = CategorysModel::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug],
                'children' => $category->children()->ordered()->get()
            ]
        ]);
    }

    // GET /api/v1/categories/{id}/path
    public function getPath($id)
    {
        $category = CategorysModel::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => ['id' => $category->id, 'name' => $category->name],
                'path' => $category->path
            ]
        ]);
    }

    // POST /api/v1/categories
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:100|unique:categorie',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'parent_id' => 'nullable|string|exists:categorie,id',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            if (empty($data['slug'])) $data['slug'] = Str::slug($request->name);
            
            // Check parent self-reference
            if (isset($data['parent_id']) && $data['parent_id'] === $data['slug']) {
                return response()->json(['success' => false, 'message' => 'Category cannot be its own parent'], 400);
            }

            $category = CategorysModel::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category->load('parent')
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create category'], 500);
        }
    }

    // GET /api/v1/categories/{id}
    public function show($id)
    {
        try {
            $category = CategorysModel::with('parent', 'children')->findOrFail($id);
            $category->path = $category->path;
            return response()->json(['success' => true, 'data' => $category]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    // PUT /api/v1/categories/{id}
    public function update(Request $request, $id)
    {
        try {
            $category = CategorysModel::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:200',
                'slug' => 'nullable|string|max:100|unique:categorie,slug,' . $id . ',id',
                'description' => 'nullable|string',
                'image_url' => 'nullable|url',
                'parent_id' => 'nullable|string|exists:categorie,id',
                'display_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Check parent relationships
            if ($request->has('parent_id') && $request->parent_id) {
                if ($request->parent_id === $category->id) {
                    return response()->json(['success' => false, 'message' => 'Category cannot be its own parent'], 400);
                }

                $childIds = $this->getAllChildIds($category);
                if (in_array($request->parent_id, $childIds)) {
                    return response()->json(['success' => false, 'message' => 'Cannot set a child category as parent'], 400);
                }
            }

            $category->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->load('parent')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    // DELETE /api/v1/categories/{id}
    public function destroy($id)
    {
        try {
            $category = CategorysModel::findOrFail($id);
            
            if ($category->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with child categories. Move or delete children first.'
                ], 400);
            }

            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    // PATCH /api/v1/categories/{id}/toggle-active
    public function toggleActive($id)
    {
        try {
            $category = CategorysModel::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();
            return response()->json([
                'success' => true,
                'message' => 'Category status updated',
                'is_active' => $category->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    // POST /api/v1/categories/reorder
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.id' => 'required|string|exists:categorie,id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            foreach ($request->orders as $order) {
                CategorysModel::where('id', $order['id'])->update(['display_order' => $order['display_order']]);
            }
            return response()->json(['success' => true, 'message' => 'Categories reordered successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to reorder categories'], 500);
        }
    }

    // POST /api/v1/categories/bulk-delete
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'string|exists:categorie,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $categoriesWithChildren = CategorysModel::whereIn('id', $request->ids)
                ->withCount('children')->having('children_count', '>', 0)->get();

            if ($categoriesWithChildren->count() > 0) {
                $names = $categoriesWithChildren->pluck('name')->implode(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete categories with children: {$names}"
                ], 400);
            }

            CategorysModel::whereIn('id', $request->ids)->delete();
            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' categories deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete categories'], 500);
        }
    }

    // PATCH /api/v1/categories/{id}/move
    public function move(Request $request, $id)
    {
        try {
            $category = CategorysModel::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'parent_id' => 'nullable|string|exists:categorie,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            if ($request->parent_id) {
                if ($request->parent_id === $category->id) {
                    return response()->json(['success' => false, 'message' => 'Category cannot be its own parent'], 400);
                }

                $childIds = $this->getAllChildIds($category);
                if (in_array($request->parent_id, $childIds)) {
                    return response()->json(['success' => false, 'message' => 'Cannot move category to its own child'], 400);
                }
            }

            $category->parent_id = $request->parent_id;
            $category->save();
            return response()->json([
                'success' => true,
                'message' => 'Category moved successfully',
                'data' => $category->load('parent')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
    }

    private function getAllChildIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildIds($child));
        }
        return $ids;
    }
}