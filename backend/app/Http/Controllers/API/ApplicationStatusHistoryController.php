<?php

namespace App\Http\Controllers\API;

use App\Models\ApplicationStatusHistory;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApplicationStatusHistoryController extends Controller
{
    /**
     * Display a listing of status history (Admin only).
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = ApplicationStatusHistory::with('application')
            ->latest();

        // Filter by application
        if ($request->has('application_id')) {
            $query->forApplication($request->application_id);
        }

        // Filter by new status
        if ($request->has('status')) {
            $query->withNewStatus($request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->changedBetween($request->date_from, $request->date_to);
        }

        // Filter by changed by
        if ($request->has('changed_by')) {
            $query->changedBy($request->changed_by);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('notes', 'LIKE', "%{$request->search}%")
                  ->orWhere('changed_by', 'LIKE', "%{$request->search}%");
            });
        }

        $history = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Status history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Get status history for a specific application.
     * 
     * @param int $applicationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function forApplication($applicationId)
    {
        $application = JobApplication::find($applicationId);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $history = ApplicationStatusHistory::forApplication($applicationId)
            ->with('application')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Status history retrieved successfully',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->full_name,
                    'current_status' => $application->status,
                    'current_status_label' => $application->status_label,
                    'current_status_color' => $application->status_color
                ],
                'history' => $history
            ]
        ]);
    }

    /**
     * Get status timeline for an application (formatted for timeline view).
     * 
     * @param int $applicationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function timeline($applicationId)
    {
        $application = JobApplication::find($applicationId);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $history = ApplicationStatusHistory::forApplication($applicationId)
            ->latest()
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'old_status' => $item->old_status,
                    'old_status_label' => $item->old_status_label,
                    'old_status_color' => $item->old_status_color,
                    'new_status' => $item->new_status,
                    'new_status_label' => $item->new_status_label,
                    'new_status_color' => $item->new_status_color,
                    'notes' => $item->notes,
                    'changed_by' => $item->changed_by_name,
                    'changed_at' => $item->changed_at,
                    'formatted_changed_at' => $item->formatted_changed_at,
                    'time_ago' => $item->time_ago
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Timeline retrieved successfully',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'job_title' => $application->job?->title
                ],
                'timeline' => $history
            ]
        ]);
    }

    /**
     * Store a newly created status history.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer|exists:job_applications,id',
            'new_status' => 'required|string|in:pending,reviewed,shortlisted,interviewed,offered,hired,rejected',
            'old_status' => 'nullable|string|in:pending,reviewed,shortlisted,interviewed,offered,hired,rejected',
            'notes' => 'nullable|string',
            'changed_by' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = JobApplication::find($request->application_id);
            
            // If old_status not provided, use current application status
            $oldStatus = $request->old_status ?? $application->status;

            $history = ApplicationStatusHistory::create([
                'application_id' => $request->application_id,
                'old_status' => $oldStatus,
                'new_status' => $request->new_status,
                'notes' => $request->notes,
                'changed_by' => $request->changed_by ??  'system',
                'changed_at' => now()
            ]);

            // Update application status if different
            if ($application->status !== $request->new_status) {
                $application->status = $request->new_status;
                $application->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status history created successfully',
                'data' => $history->load('application')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create status history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified status history.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $history = ApplicationStatusHistory::with('application')->find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Status history not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status history retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Update the specified status history (usually just notes).
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $history = ApplicationStatusHistory::find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Status history not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $history->notes = $request->notes ?? $history->notes;
            $history->save();

            return response()->json([
                'success' => true,
                'message' => 'Status history updated successfully',
                'data' => $history->load('application')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified status history.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $history = ApplicationStatusHistory::find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Status history not found'
            ], 404);
        }

        try {
            $history->delete();

            return response()->json([
                'success' => true,
                'message' => 'Status history deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete status history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status change statistics.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        $query = ApplicationStatusHistory::query();

        // Filter by date range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->changedBetween($request->date_from, $request->date_to);
        }

        // Filter by application
        if ($request->has('application_id')) {
            $query->forApplication($request->application_id);
        }

        $totalChanges = $query->count();

        // Changes by status
        $changesByStatus = $query->select('new_status')
            ->selectRaw('count(*) as total')
            ->groupBy('new_status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $item->new_status,
                    'label' => JobApplication::STATUSES[$item->new_status] ?? ucfirst($item->new_status),
                    'color' => JobApplication::STATUS_COLORS[$item->new_status] ?? 'gray',
                    'count' => $item->total
                ];
            });

        // Changes by day (last 30 days)
        $changesByDay = ApplicationStatusHistory::selectRaw('DATE(changed_at) as date, count(*) as total')
            ->when($request->has('application_id'), function($q) use ($request) {
                return $q->where('application_id', $request->application_id);
            })
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Top changers
        $topChangedBy = ApplicationStatusHistory::select('changed_by')
            ->selectRaw('count(*) as total')
            ->whereNotNull('changed_by')
            ->when($request->has('application_id'), function($q) use ($request) {
                return $q->where('application_id', $request->application_id);
            })
            ->groupBy('changed_by')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'changed_by' => $item->changed_by,
                    'name' => $this->maskEmail($item->changed_by),
                    'total' => $item->total
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [
                'total_changes' => $totalChanges,
                'by_status' => $changesByStatus,
                'by_day' => $changesByDay,
                'top_changed_by' => $topChangedBy
            ]
        ]);
    }

    /**
     * Bulk delete history records.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:application_status_history,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = count($request->ids);
            ApplicationStatusHistory::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} history records deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete history records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest status changes.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);

        $history = ApplicationStatusHistory::with('application')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'application_id' => $item->application_id,
                    'applicant_name' => $item->application->full_name ?? 'Unknown',
                    'old_status' => $item->old_status_label,
                    'new_status' => $item->new_status_label,
                    'changed_by' => $item->changed_by_name,
                    'time_ago' => $item->time_ago,
                    'changed_at' => $item->formatted_changed_at
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Latest changes retrieved successfully',
            'data' => $history
        ]);
    }

    /**
     * Helper function to mask email
     */
    private function maskEmail($email)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
        
        return $maskedName . '@' . $domain;
    }
}