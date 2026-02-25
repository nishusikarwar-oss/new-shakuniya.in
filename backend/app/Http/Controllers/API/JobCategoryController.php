<?php

namespace App\Http\Controllers\API;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of job categories.
     */
    public function index(Request $request)
    {
        $query = JobCategory::query();

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Include job counts
        if ($request->boolean('with_counts')) {
            $query->withJobCounts();
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        $categories = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get category by slug.
     */
    public function findBySlug($slug)
    {
        $category = JobCategory::where('slug', $slug)
            ->where('is_active', true)
            ->withJobCounts()
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Get active jobs in this category
        $category->jobs = $category->jobs()
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get(['id', 'title', 'slug', 'location', 'employment_type', 'salary_range']);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:job_categories',
            'slug' => 'nullable|string|max:100|unique:job_categories',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->name);
        }

        $category = JobCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Job category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = JobCategory::withJobCounts()->find($id);

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
        $category = JobCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:job_categories,name,' . $id,
            'slug' => 'nullable|string|max:100|unique:job_categories,slug,' . $id,
            'is_active' => 'nullable|boolean'
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
            'message' => 'Job category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = JobCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has jobs
        if ($category->jobs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated jobs'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job category deleted successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $category = JobCategory::find($id);

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
            'message' => 'Category status updated successfully',
            'is_active' => $category->is_active
        ]);
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:job_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if any category has jobs
        $categoriesWithJobs = JobCategory::whereIn('id', $request->ids)
            ->withCount('jobs')
            ->having('jobs_count', '>', 0)
            ->get();

        if ($categoriesWithJobs->count() > 0) {
            $names = $categoriesWithJobs->pluck('name')->implode(', ');
            return response()->json([
                'success' => false,
                'message' => "Cannot delete categories with jobs: {$names}"
            ], 400);
        }

        JobCategory::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' categories deleted successfully'
        ]);
    }

    /**
     * Get categories as options (for dropdowns)
     */
    public function getOptions(Request $request)
    {
        $query = JobCategory::query();

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $categories = $query->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get popular categories (with most jobs)
     */
    public function getPopular(Request $request)
    {
        $limit = $request->get('limit', 10);

        $categories = JobCategory::active()
            ->withJobCounts()
            ->having('jobs_count', '>', 0)
            ->orderBy('jobs_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get category statistics
     */
    public function stats()
    {
        $total = JobCategory::count();
        $active = JobCategory::active()->count();
        $inactive = $total - $active;

        $categoriesWithJobs = JobCategory::withJobCounts()
            ->having('jobs_count', '>', 0)
            ->count();

        $popularCategories = JobCategory::active()
            ->withJobCounts()
            ->having('jobs_count', '>', 0)
            ->orderBy('jobs_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'jobs_count']);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'categories_with_jobs' => $categoriesWithJobs,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'popular_categories' => $popularCategories
            ]
        ]);
    }
}