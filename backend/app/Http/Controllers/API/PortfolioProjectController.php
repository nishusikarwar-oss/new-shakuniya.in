<?php

namespace App\Http\Controllers\API;

use App\Models\PortfolioProject;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioProjectController extends Controller
{
    /**
     * Display a listing of portfolio projects.
     */
    public function index(Request $request)
    {
        $query = PortfolioProject::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter featured only
        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->inCategory($request->category);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->fromYear($request->year);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Get distinct categories
        if ($request->boolean('get_categories')) {
            $categories = (clone $query)
                ->whereNotNull('category')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        }

        // Get distinct years
        if ($request->boolean('get_years')) {
            $years = (clone $query)
                ->whereNotNull('completion_date')
                ->selectRaw('YEAR(completion_date) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            return response()->json([
                'success' => true,
                'data' => $years
            ]);
        }

        // Order by display order
        $query->ordered();

        $projects = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get project by slug.
     */
    public function findBySlug($slug)
    {
        $project = PortfolioProject::with('company')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_projects',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'project_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'technologies' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['featured_image']);

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('portfolio', 'public');
            $data['featured_image'] = $path;
        }

        // If technologies is JSON string, validate it
        if (isset($data['technologies']) && $data['technologies'][0] === '[') {
            $techArray = json_decode($data['technologies'], true);
            if (is_array($techArray)) {
                $data['technologies'] = json_encode($techArray);
            }
        }

        $project = PortfolioProject::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio project created successfully',
            'data' => $project->load('company')
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show($id)
    {
        $project = PortfolioProject::with('company')->find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $project = PortfolioProject::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_projects,slug,' . $id . ',project_id',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'project_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'technologies' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['featured_image']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($project->featured_image) {
                $oldPath = str_replace(asset('storage/'), '', $project->featured_image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('featured_image')->store('portfolio', 'public');
            $data['featured_image'] = $path;
        }

        $project->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Portfolio project updated successfully',
            'data' => $project->load('company')
        ]);
    }

    /**
     * Remove the specified project.
     */
    public function destroy($id)
    {
        $project = PortfolioProject::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        // Delete featured image
        if ($project->featured_image) {
            $path = str_replace(asset('storage/'), '', $project->featured_image);
            Storage::disk('public')->delete($path);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio project deleted successfully'
        ]);
    }

    /**
     * Reorder projects
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.project_id' => 'required|integer|exists:portfolio_projects,project_id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            PortfolioProject::where('project_id', $order['project_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Projects reordered successfully'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $project = PortfolioProject::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $project->is_featured = !$project->is_featured;
        $project->save();

        return response()->json([
            'success' => true,
            'message' => 'Project featured status updated',
            'is_featured' => $project->is_featured
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $project = PortfolioProject::find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        $project->is_active = !$project->is_active;
        $project->save();

        return response()->json([
            'success' => true,
            'message' => 'Project active status updated',
            'is_active' => $project->is_active
        ]);
    }

    /**
     * Bulk delete projects
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:portfolio_projects,project_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get projects to delete their images
            $projects = PortfolioProject::whereIn('project_id', $ids)->get();
            
            // Delete associated images
            foreach ($projects as $project) {
                if ($project->featured_image) {
                    $path = str_replace(asset('storage/'), '', $project->featured_image);
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Delete projects
            PortfolioProject::whereIn('project_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' projects deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get projects by category
     */
    public function getByCategory($category, Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $projects = PortfolioProject::where('company_id', $companyId)
            ->where('category', $category)
            ->where('is_active', true)
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get featured projects
     */
    public function getFeatured(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $limit = $request->get('limit', 6);

        $projects = PortfolioProject::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->ordered()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get recent projects
     */
    public function getRecent(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $limit = $request->get('limit', 6);

        $projects = PortfolioProject::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('completion_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }
}