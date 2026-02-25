<?php

namespace App\Http\Controllers\API;

use App\Models\JobOpening;
use App\Models\JobCategory;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobOpeningController extends Controller
{
    /**
     * Display a listing of job openings.
     */
    public function index(Request $request)
    {
        $query = JobOpening::with('department', 'categories');

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter featured only
        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Filter by employment type
        if ($request->has('employment_type')) {
            $query->ofEmploymentType($request->employment_type);
        }

        // Filter by work type
        if ($request->has('work_type')) {
            $query->OfWorkType($request->work_type);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->inLocation($request->location);
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->inDepartment($request->department_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('job_categories.id', $request->category_id);
            });
        }

        // Filter by category slug
        if ($request->has('category_slug')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('job_categories.slug', $request->category_slug);
            });
        }

        // Filter by experience
        if ($request->has('exp_min') && $request->has('exp_max')) {
            $query->experienceBetween($request->exp_min, $request->exp_max);
        } elseif ($request->has('exp_min')) {
            $query->minExperience($request->exp_min);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Get filter options
        if ($request->boolean('get_filters')) {
            return $this->getFilterOptions();
        }

        // Order by priority
        $query->byPriority();

        $jobs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Get filter options (locations, employment types, etc.)
     */
    private function getFilterOptions()
    {
        $locations = JobOpening::active()
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $employmentTypes = collect(JobOpening::EMPLOYMENT_TYPES)
            ->map(function($label, $value) {
                return ['value' => $value, 'label' => $label];
            })->values();

        $workTypes = collect(JobOpening::WORK_TYPES)
            ->map(function($label, $value) {
                return ['value' => $value, 'label' => $label];
            })->values();

        $departments = Department::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = JobCategory::active()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $experienceLevels = [
            ['min' => 0, 'max' => 1, 'label' => 'Fresher (0-1 years)'],
            ['min' => 1, 'max' => 3, 'label' => 'Junior (1-3 years)'],
            ['min' => 3, 'max' => 5, 'label' => 'Mid (3-5 years)'],
            ['min' => 5, 'max' => 8, 'label' => 'Senior (5-8 years)'],
            ['min' => 8, 'max' => 99, 'label' => 'Expert (8+ years)']
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'locations' => $locations,
                'employment_types' => $employmentTypes,
                'work_types' => $workTypes,
                'departments' => $departments,
                'categories' => $categories,
                'experience_levels' => $experienceLevels
            ]
        ]);
    }

    /**
     * Get job by slug.
     */
    public function findBySlug($slug)
    {
        $job = JobOpening::with('department', 'categories')
            ->where('slug', $slug)
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Increment view count
        $job->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * Store a newly created job opening with categories.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:job_openings',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:300',
            'experience_required' => 'required|string|max:100',
            'experience_min' => 'nullable|integer|min:0',
            'experience_max' => 'nullable|integer|min:0|gte:experience_min',
            'positions_available' => 'nullable|integer|min:1',
            'qualification' => 'nullable|string',
            'location' => 'required|string|max:255',
            'department_id' => 'nullable|integer|exists:departments,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:job_categories,id',
            'employment_type' => 'nullable|in:full-time,part-time,contract,internship',
            'work_type' => 'nullable|in:onsite,remote,hybrid',
            'salary_range' => 'nullable|string|max:100',
            'responsibilities' => 'nullable|array',
            'requirements' => 'nullable|array',
            'benefits' => 'nullable|array',
            'application_deadline' => 'nullable|date|after:today',
            'priority' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['category_ids']);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Convert arrays to JSON
        if (isset($data['responsibilities']) && is_array($data['responsibilities'])) {
            $data['responsibilities'] = json_encode($data['responsibilities']);
        }
        if (isset($data['requirements']) && is_array($data['requirements'])) {
            $data['requirements'] = json_encode($data['requirements']);
        }
        if (isset($data['benefits']) && is_array($data['benefits'])) {
            $data['benefits'] = json_encode($data['benefits']);
        }

        $job = JobOpening::create($data);

        // Attach categories if provided
        if ($request->has('category_ids')) {
            $job->categories()->attach($request->category_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job opening created successfully',
            'data' => $job->load('department', 'categories')
        ], 201);
    }

    /**
     * Display the specified job opening.
     */
    public function show($id)
    {
        $job = JobOpening::with('department', 'categories')->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    /**
     * Update the specified job opening with categories.
     */
    public function update(Request $request, $id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:job_openings,slug,' . $id,
            'description' => 'sometimes|required|string',
            'short_description' => 'nullable|string|max:300',
            'experience_required' => 'sometimes|required|string|max:100',
            'experience_min' => 'nullable|integer|min:0',
            'experience_max' => 'nullable|integer|min:0|gte:experience_min',
            'positions_available' => 'nullable|integer|min:1',
            'qualification' => 'nullable|string',
            'location' => 'sometimes|required|string|max:255',
            'department_id' => 'nullable|integer|exists:departments,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:job_categories,id',
            'employment_type' => 'nullable|in:full-time,part-time,contract,internship',
            'work_type' => 'nullable|in:onsite,remote,hybrid',
            'salary_range' => 'nullable|string|max:100',
            'responsibilities' => 'nullable|array',
            'requirements' => 'nullable|array',
            'benefits' => 'nullable|array',
            'application_deadline' => 'nullable|date',
            'priority' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['category_ids']);

        // Convert arrays to JSON
        if (isset($data['responsibilities']) && is_array($data['responsibilities'])) {
            $data['responsibilities'] = json_encode($data['responsibilities']);
        }
        if (isset($data['requirements']) && is_array($data['requirements'])) {
            $data['requirements'] = json_encode($data['requirements']);
        }
        if (isset($data['benefits']) && is_array($data['benefits'])) {
            $data['benefits'] = json_encode($data['benefits']);
        }

        $job->update($data);

        // Sync categories if provided
        if ($request->has('category_ids')) {
            $job->categories()->sync($request->category_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job opening updated successfully',
            'data' => $job->load('department', 'categories')
        ]);
    }

    /**
     * Remove the specified job opening.
     */
    public function destroy($id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Detach all categories first
        $job->categories()->detach();
        
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job opening deleted successfully'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $job->is_featured = !$job->is_featured;
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job featured status updated',
            'is_featured' => $job->is_featured
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $job->is_active = !$job->is_active;
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job active status updated',
            'is_active' => $job->is_active
        ]);
    }

    /**
     * Bulk delete job openings
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:job_openings,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Detach all categories for these jobs
        foreach ($request->ids as $jobId) {
            $job = JobOpening::find($jobId);
            if ($job) {
                $job->categories()->detach();
            }
        }

        JobOpening::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' jobs deleted successfully'
        ]);
    }

    /**
     * Get job statistics
     */
    public function stats()
    {
        $total = JobOpening::count();
        $active = JobOpening::active()->count();
        $featured = JobOpening::featured()->count();
        $expired = JobOpening::where('is_active', true)
            ->whereNotNull('application_deadline')
            ->where('application_deadline', '<', now())
            ->count();

        $byEmploymentType = JobOpening::select('employment_type')
            ->selectRaw('count(*) as total')
            ->groupBy('employment_type')
            ->get();

        $byWorkType = JobOpening::select('work_type')
            ->selectRaw('count(*) as total')
            ->groupBy('work_type')
            ->get();

        $byCategory = JobCategory::withCount(['jobs' => function($query) {
                $query->where('is_active', true);
            }])
            ->having('jobs_count', '>', 0)
            ->orderBy('jobs_count', 'desc')
            ->limit(5)
            ->get();

        $topLocations = JobOpening::active()
            ->select('location')
            ->selectRaw('count(*) as total')
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'featured' => $featured,
                'expired' => $expired,
                'by_employment_type' => $byEmploymentType,
                'by_work_type' => $byWorkType,
                'by_category' => $byCategory,
                'top_locations' => $topLocations
            ]
        ]);
    }

    /**
     * Duplicate a job opening
     */
    public function duplicate($id)
    {
        $job = JobOpening::with('categories')->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $newJob = $job->replicate();
        $newJob->title = $job->title . ' (Copy)';
        $newJob->slug = Str::slug($newJob->title) . '-' . uniqid();
        $newJob->is_active = false;
        $newJob->view_count = 0;
        $newJob->application_count = 0;
        $newJob->created_at = now();
        $newJob->updated_at = now();
        $newJob->save();

        // Copy categories
        if ($job->categories->count() > 0) {
            $categoryIds = $job->categories->pluck('id')->toArray();
            $newJob->categories()->attach($categoryIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job duplicated successfully',
            'data' => $newJob->load('department', 'categories')
        ]);
    }

    /**
     * Get jobs by multiple categories
     */
    public function getByCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:job_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jobs = JobOpening::whereHas('categories', function($query) use ($request) {
            $query->whereIn('job_categories.id', $request->category_ids);
        })
        ->where('is_active', true)
        ->with('categories', 'department')
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Add category to job
     */
    public function addCategory(Request $request, $id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:job_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if already attached
        if ($job->categories()->where('category_id', $request->category_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Category already attached to this job'
            ], 400);
        }

        $job->categories()->attach($request->category_id);

        return response()->json([
            'success' => true,
            'message' => 'Category added to job successfully',
            'data' => $job->load('categories')
        ]);
    }

    /**
     * Remove category from job
     */
    public function removeCategory(Request $request, $id)
    {
        $job = JobOpening::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:job_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $job->categories()->detach($request->category_id);

        return response()->json([
            'success' => true,
            'message' => 'Category removed from job successfully',
            'data' => $job->load('categories')
        ]);
    }

    /**
     * Get jobs count by category
     */
    public function getJobsCountByCategory()
    {
        $categories = JobCategory::withCount(['jobs' => function($query) {
            $query->where('is_active', true);
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}