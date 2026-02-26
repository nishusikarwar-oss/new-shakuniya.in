<?php

namespace App\Http\Controllers\API;

use App\Models\WhyChooseUsPoint;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WhyChooseUsPointController extends Controller
{
    /**
     * Display a listing of why choose us points.
     */
    public function index(Request $request)
    {
        $query = WhyChooseUsPoint::with('company');

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
            $query->where('point_text', 'LIKE', "%{$search}%");
        }

        // Order by display order
        $query->orderBy('display_order', 'asc');

        $points = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $points
        ]);
    }

    /**
     * Store a newly created why choose us point.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'point_text' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        $point = WhyChooseUsPoint::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Why choose us point created successfully',
            'data' => $point->load('company')
        ], 201);
    }

    /**
     * Display the specified why choose us point.
     */
    public function show($id)
    {
        $point = WhyChooseUsPoint::with('company')->find($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Why choose us point not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $point
        ]);
    }

    /**
     * Update the specified why choose us point.
     */
    public function update(Request $request, $id)
    {
        $point = WhyChooseUsPoint::find($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Why choose us point not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'point_text' => 'sometimes|required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $point->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Why choose us point updated successfully',
            'data' => $point->load('company')
        ]);
    }

    /**
     * Remove the specified why choose us point.
     */
    public function destroy($id)
    {
        $point = WhyChooseUsPoint::find($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Why choose us point not found'
            ], 404);
        }

        $point->delete();

        return response()->json([
            'success' => true,
            'message' => 'Why choose us point deleted successfully'
        ]);
    }

    /**
     * Reorder why choose us points
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.point_id' => 'required|integer|exists:why_choose_us_points,point_id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            WhyChooseUsPoint::where('point_id', $order['point_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Why choose us points reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $point = WhyChooseUsPoint::find($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Why choose us point not found'
            ], 404);
        }

        $point->is_active = !$point->is_active;
        $point->save();

        return response()->json([
            'success' => true,
            'message' => 'Why choose us point active status updated',
            'is_active' => $point->is_active
        ]);
    }

    /**
     * Bulk delete why choose us points
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:why_choose_us_points,point_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Delete points
            WhyChooseUsPoint::whereIn('point_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' why choose us points deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting points: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get random points (for frontend display)
     */
    public function getRandom(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $limit = $request->get('limit', 5);

        $points = WhyChooseUsPoint::where('company_id', $companyId)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $points
        ]);
    }
}