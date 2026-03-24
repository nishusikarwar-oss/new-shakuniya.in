<?php

namespace App\Http\Controllers\API;

use App\Models\Statistic;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StatisticController extends Controller
{
    /**
     * Display a listing of statistics.
     */
    public function index(Request $request)
    {
        $query = Statistic::with('company');

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
                $q->where('label', 'LIKE', "%{$search}%");
            });
        }

        // Order by display order
        $query->orderBy('display_order', 'asc');

        $statistics = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Store a newly created statistic.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'label' => 'required|string|max:255',
            'value' => 'required|integer|min:0',
            'suffix' => 'nullable|string|max:20',
            'prefix' => 'nullable|string|max:20',
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
$data['service_id'] = $request->input('service_id');
        $statistic = Statistic::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Statistic created successfully',
            'data' => $statistic->load('company')
        ], 201);
    }

    /**
     * Display the specified statistic.
     */
    public function show($id)
    {
        $statistic = Statistic::with('company')->find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $statistic
        ]);
    }

    /**
     * Update the specified statistic.
     */
    public function update(Request $request, $id)
    {
        $statistic = Statistic::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'label' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|integer|min:0',
            'suffix' => 'nullable|string|max:20',
            'prefix' => 'nullable|string|max:20',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $statistic->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Statistic updated successfully',
            'data' => $statistic->load('company')
        ]);
    }

    /**
     * Remove the specified statistic.
     */
    public function destroy($id)
    {
        $statistic = Statistic::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        $statistic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Statistic deleted successfully'
        ]);
    }

    /**
     * Reorder statistics
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.stat_id' => 'required|integer|exists:statistics,stat_id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            Statistic::where('stat_id', $order['stat_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Statistics reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $statistic = Statistic::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        $statistic->is_active = !$statistic->is_active;
        $statistic->save();

        return response()->json([
            'success' => true,
            'message' => 'Statistic active status updated',
            'is_active' => $statistic->is_active
        ]);
    }

    /**
     * Bulk delete statistics
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:statistics,stat_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Delete statistics
            Statistic::whereIn('stat_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' statistics deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics summary
     */
    public function summary(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        
        $statistics = Statistic::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $summary = [
            'total_count' => $statistics->count(),
            'total_value' => $statistics->sum('value'),
            'average_value' => $statistics->avg('value'),
            'max_value' => $statistics->max('value'),
            'min_value' => $statistics->min('value'),
            'statistics' => $statistics->map(function($stat) {
                return [
                    'id' => $stat->stat_id,
                    'label' => $stat->label,
                    'value' => $stat->value,
                    'formatted' => $stat->formatted_value,
                    'short' => $stat->formatted_short_value
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Increment statistic value
     */
    public function increment($id, Request $request)
    {
        $statistic = Statistic::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        $amount = $request->get('amount', 1);
        $statistic->value += $amount;
        $statistic->save();

        return response()->json([
            'success' => true,
            'message' => "Statistic incremented by {$amount}",
            'data' => $statistic
        ]);
    }

    /**
     * Decrement statistic value
     */
    public function decrement($id, Request $request)
    {
        $statistic = Statistic::find($id);

        if (!$statistic) {
            return response()->json([
                'success' => false,
                'message' => 'Statistic not found'
            ], 404);
        }

        $amount = $request->get('amount', 1);
        $statistic->value = max(0, $statistic->value - $amount); // Prevent negative values
        $statistic->save();

        return response()->json([
            'success' => true,
            'message' => "Statistic decremented by {$amount}",
            'data' => $statistic
        ]);
    }
}