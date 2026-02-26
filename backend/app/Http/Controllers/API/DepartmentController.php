<?php

namespace App\Http\Controllers\API;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $query = Department::query();

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Include employee counts
        if ($request->boolean('with_counts')) {
            $query->withCount('employees');
        }

        $departments = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Get department by slug.
     */
    public function findBySlug($slug)
    {
        $department = Department::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        // Load employees count
        $department->loadCount('employees');

        return response()->json([
            'success' => true,
            'data' => $department
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:departments',
            'slug' => 'nullable|string|max:100|unique:departments',
            'description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:50',
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

        $department = Department::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'data' => $department
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show($id)
    {
        $department = Department::withCount('employees')->find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $department
        ]);
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:departments,name,' . $id,
            'slug' => 'nullable|string|max:100|unique:departments,slug,' . $id,
            'description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $department->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'data' => $department
        ]);
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department with associated employees'
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $department->is_active = !$department->is_active;
        $department->save();

        return response()->json([
            'success' => true,
            'message' => 'Department status updated successfully',
            'is_active' => $department->is_active
        ]);
    }

    /**
     * Bulk delete departments
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:departments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if any department has employees
        $departmentsWithEmployees = Department::whereIn('id', $request->ids)
            ->withCount('employees')
            ->having('employees_count', '>', 0)
            ->get();

        if ($departmentsWithEmployees->count() > 0) {
            $names = $departmentsWithEmployees->pluck('name')->implode(', ');
            return response()->json([
                'success' => false,
                'message' => "Cannot delete departments with employees: {$names}"
            ], 400);
        }

        Department::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' departments deleted successfully'
        ]);
    }

    /**
     * Get departments as options (for dropdowns)
     */
    public function getOptions(Request $request)
    {
        $query = Department::query();

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $departments = $query->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Get department statistics
     */
    public function stats()
    {
        $total = Department::count();
        $active = Department::active()->count();
        $inactive = $total - $active;

        $departmentsWithMostEmployees = Department::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'employees_count']);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'top_departments' => $departmentsWithMostEmployees
            ]
        ]);
    }
}