<?php

namespace App\Http\Controllers\API;

use App\Models\ProcessStep;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProcessStepController extends Controller
{
    /**
     * Display a listing of process steps.
     */
    public function index(Request $request)
    {
        $query = ProcessStep::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Order by step number
        $query->orderBy('step_number', 'asc');

        $steps = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $steps
        ]);
    }

    /**
     * Store a newly created process step.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'step_number' => 'required|integer|min:1|unique:process_steps,step_number,NULL,step_id,company_id,' . ($request->company_id ?? 1),
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon']);

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('process-steps/icons', 'public');
            $data['icon_url'] = $path;
        }

        $step = ProcessStep::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Process step created successfully',
            'data' => $step->load('company')
        ], 201);
    }

    /**
     * Display the specified process step.
     */
    public function show($id)
    {
        $step = ProcessStep::with('company')->find($id);

        if (!$step) {
            return response()->json([
                'success' => false,
                'message' => 'Process step not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $step
        ]);
    }

    /**
     * Update the specified process step.
     */
    public function update(Request $request, $id)
    {
        $step = ProcessStep::find($id);

        if (!$step) {
            return response()->json([
                'success' => false,
                'message' => 'Process step not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'step_number' => 'sometimes|required|integer|min:1|unique:process_steps,step_number,' . $id . ',step_id,company_id,' . ($request->company_id ?? $step->company_id),
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon']);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($step->icon_url) {
                Storage::disk('public')->delete($step->icon_url);
            }
            $path = $request->file('icon')->store('process-steps/icons', 'public');
            $data['icon_url'] = $path;
        }

        $step->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Process step updated successfully',
            'data' => $step->load('company')
        ]);
    }

    /**
     * Remove the specified process step.
     */
    public function destroy($id)
    {
        $step = ProcessStep::find($id);

        if (!$step) {
            return response()->json([
                'success' => false,
                'message' => 'Process step not found'
            ], 404);
        }

        // Delete associated icon
        if ($step->icon_url) {
            Storage::disk('public')->delete($step->icon_url);
        }

        $step->delete();

        // Reorder remaining steps
        $this->reorderAfterDelete($step->company_id);

        return response()->json([
            'success' => true,
            'message' => 'Process step deleted successfully'
        ]);
    }

    /**
     * Reorder steps after delete
     */
    private function reorderAfterDelete($companyId)
    {
        $steps = ProcessStep::where('company_id', $companyId)
            ->orderBy('step_number', 'asc')
            ->get();

        $stepNumber = 1;
        foreach ($steps as $step) {
            if ($step->step_number != $stepNumber) {
                $step->step_number = $stepNumber;
                $step->save();
            }
            $stepNumber++;
        }
    }

    /**
     * Reorder process steps
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.step_id' => 'required|integer|exists:process_steps,step_id',
            'orders.*.step_number' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            ProcessStep::where('step_id', $order['step_id'])
                ->update(['step_number' => $order['step_number']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Process steps reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $step = ProcessStep::find($id);

        if (!$step) {
            return response()->json([
                'success' => false,
                'message' => 'Process step not found'
            ], 404);
        }

        $step->is_active = !$step->is_active;
        $step->save();

        return response()->json([
            'success' => true,
            'message' => 'Process step active status updated',
            'is_active' => $step->is_active
        ]);
    }

    /**
     * Bulk delete process steps
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:process_steps,step_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get steps to delete their icons
            $steps = ProcessStep::whereIn('step_id', $ids)->get();
            $companyId = $steps->first()->company_id;
            
            // Delete associated icons
            foreach ($steps as $step) {
                if ($step->icon_url) {
                    Storage::disk('public')->delete($step->icon_url);
                }
            }
            
            // Delete steps
            ProcessStep::whereIn('step_id', $ids)->delete();
            
            // Reorder remaining steps
            $this->reorderAfterDelete($companyId);
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' process steps deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting process steps: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get steps in a specific range
     */
    public function getRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'start' => 'required|integer|min:1',
            'end' => 'required|integer|min:1|gte:start'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $request->company_id ?? 1;

        $steps = ProcessStep::with('company')
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereBetween('step_number', [$request->start, $request->end])
            ->orderBy('step_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $steps
        ]);
    }
}