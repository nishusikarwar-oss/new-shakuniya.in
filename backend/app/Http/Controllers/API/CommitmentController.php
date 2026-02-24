<?php

namespace App\Http\Controllers\API;

use App\Models\Commitment;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CommitmentController extends Controller
{
    /**
     * Display a listing of commitments.
     */
    public function index(Request $request)
    {
        $query = Commitment::with('company');

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

        // Order by display order
        $query->orderBy('display_order', 'asc');

        $commitments = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $commitments
        ]);
    }

    /**
     * Store a newly created commitment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'display_order' => 'nullable|integer',
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
            $path = $request->file('icon')->store('commitments/icons', 'public');
            $data['icon_url'] = $path;
        }

        $commitment = Commitment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Commitment created successfully',
            'data' => $commitment->load('company')
        ], 201);
    }

    /**
     * Display the specified commitment.
     */
    public function show($id)
    {
        $commitment = Commitment::with('company')->find($id);

        if (!$commitment) {
            return response()->json([
                'success' => false,
                'message' => 'Commitment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $commitment
        ]);
    }

    /**
     * Update the specified commitment.
     */
    public function update(Request $request, $id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json([
                'success' => false,
                'message' => 'Commitment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'display_order' => 'nullable|integer',
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
            if ($commitment->icon_url) {
                Storage::disk('public')->delete($commitment->icon_url);
            }
            $path = $request->file('icon')->store('commitments/icons', 'public');
            $data['icon_url'] = $path;
        }

        $commitment->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Commitment updated successfully',
            'data' => $commitment->load('company')
        ]);
    }

    /**
     * Remove the specified commitment.
     */
    public function destroy($id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json([
                'success' => false,
                'message' => 'Commitment not found'
            ], 404);
        }

        // Delete associated icon
        if ($commitment->icon_url) {
            Storage::disk('public')->delete($commitment->icon_url);
        }

        $commitment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commitment deleted successfully'
        ]);
    }

    /**
     * Reorder commitments
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.commitment_id' => 'required|integer|exists:commitments,commitment_id',
            'orders.*.display_order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            Commitment::where('commitment_id', $order['commitment_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commitments reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $commitment = Commitment::find($id);

        if (!$commitment) {
            return response()->json([
                'success' => false,
                'message' => 'Commitment not found'
            ], 404);
        }

        $commitment->is_active = !$commitment->is_active;
        $commitment->save();

        return response()->json([
            'success' => true,
            'message' => 'Commitment active status updated',
            'is_active' => $commitment->is_active
        ]);
    }

    /**
     * Bulk delete commitments
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:commitments,commitment_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get commitments to delete their icons
            $commitments = Commitment::whereIn('commitment_id', $ids)->get();
            
            // Delete associated icons
            foreach ($commitments as $commitment) {
                if ($commitment->icon_url) {
                    Storage::disk('public')->delete($commitment->icon_url);
                }
            }
            
            // Delete commitments
            Commitment::whereIn('commitment_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' commitments deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting commitments: ' . $e->getMessage()
            ], 500);
        }
    }
}