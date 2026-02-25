<?php

namespace App\Http\Controllers\API;

use App\Models\JobAlert;
use App\Models\JobOpening;
use App\Models\Location;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class JobAlertController extends Controller
{
    /**
     * Display a listing of job alerts (Admin only).
     */
    public function index(Request $request)
    {
        $query = JobAlert::query();

        // Filter by frequency
        if ($request->has('frequency')) {
            $query->ofFrequency($request->frequency);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by location
        if ($request->has('location')) {
            $query->withLocation($request->location);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->withCategory($request->category);
        }

        // Filter by experience
        if ($request->has('min_experience')) {
            $query->minExperience($request->min_experience);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        $alerts = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Store a newly created job alert (Public endpoint).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:job_alerts',
            'name' => 'nullable|string|max:255',
            'preferred_location' => 'nullable|string|max:255',
            'preferred_category' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'frequency' => 'nullable|in:daily,weekly,instant'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alert = JobAlert::create($request->all());

        // Send confirmation email (optional)
        // $this->sendConfirmationEmail($alert);

        return response()->json([
            'success' => true,
            'message' => 'Job alert created successfully! You will receive job notifications based on your preferences.',
            'data' => [
                'id' => $alert->id,
                'email' => $alert->email,
                'frequency' => $alert->frequency_label,
                'created_at' => $alert->created_at
            ]
        ], 201);
    }

    /**
     * Display the specified job alert.
     */
    public function show($id)
    {
        $alert = JobAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
    }

    /**
     * Get alert by email.
     */
    public function findByEmail($email)
    {
        $alert = JobAlert::where('email', $email)->first();

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
    }

    /**
     * Update the specified job alert.
     */
    public function update(Request $request, $id)
    {
        $alert = JobAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|max:255|unique:job_alerts,email,' . $id,
            'name' => 'nullable|string|max:255',
            'preferred_location' => 'nullable|string|max:255',
            'preferred_category' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'frequency' => 'nullable|in:daily,weekly,instant',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alert->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Job alert updated successfully',
            'data' => $alert
        ]);
    }

    /**
     * Unsubscribe from job alert (Public endpoint).
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:job_alerts,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $alert = JobAlert::where('email', $request->email)->first();

        if (!$alert->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already unsubscribed.'
            ], 400);
        }

        $alert->is_active = false;
        $alert->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from job alerts.'
        ]);
    }

    /**
     * Unsubscribe by ID (admin or authenticated).
     */
    public function unsubscribeById($id)
    {
        $alert = JobAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        if (!$alert->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This alert is already inactive.'
            ], 400);
        }

        $alert->is_active = false;
        $alert->save();

        return response()->json([
            'success' => true,
            'message' => 'Job alert deactivated successfully',
            'data' => $alert
        ]);
    }

    /**
     * Remove the specified job alert.
     */
    public function destroy($id)
    {
        $alert = JobAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job alert deleted successfully'
        ]);
    }

    /**
     * Bulk delete job alerts.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:job_alerts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        JobAlert::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' job alerts deleted successfully'
        ]);
    }

    /**
     * Get job alerts statistics.
     */
    public function stats()
    {
        $total = JobAlert::count();
        $active = JobAlert::active()->count();
        $inactive = $total - $active;

        $byFrequency = [
            'daily' => JobAlert::ofFrequency('daily')->count(),
            'weekly' => JobAlert::ofFrequency('weekly')->count(),
            'instant' => JobAlert::ofFrequency('instant')->count()
        ];

        $byLocation = JobAlert::select('preferred_location')
            ->selectRaw('count(*) as total')
            ->whereNotNull('preferred_location')
            ->groupBy('preferred_location')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $byCategory = JobAlert::select('preferred_category')
            ->selectRaw('count(*) as total')
            ->whereNotNull('preferred_category')
            ->groupBy('preferred_category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $averageExperience = JobAlert::whereNotNull('experience_years')
            ->avg('experience_years');

        $recentSignups = JobAlert::whereDate('created_at', '>=', now()->subDays(7))
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'by_frequency' => $byFrequency,
                'top_locations' => $byLocation,
                'top_categories' => $byCategory,
                'average_experience' => round($averageExperience ?? 0, 1),
                'recent_signups_7days' => $recentSignups
            ]
        ]);
    }

    /**
     * Get matching jobs for an alert.
     */
    public function getMatchingJobs($id)
    {
        $alert = JobAlert::find($id);

        if (!$alert) {
            return response()->json([
                'success' => false,
                'message' => 'Job alert not found'
            ], 404);
        }

        if (!$alert->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This alert is inactive'
            ], 400);
        }

        $jobs = $alert->getMatchingJobs();

        return response()->json([
            'success' => true,
            'data' => [
                'alert' => $alert,
                'total_matching' => $jobs->count(),
                'jobs' => $jobs
            ]
        ]);
    }

    /**
     * Send job alerts for a specific frequency (Admin/Cron job).
     */
    public function sendAlerts($frequency)
    {
        if (!in_array($frequency, ['daily', 'weekly', 'instant'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid frequency'
            ], 400);
        }

        $alerts = JobAlert::getAlertsForFrequency($frequency);
        $sent = 0;

        foreach ($alerts as $alert) {
            $jobs = $alert->getMatchingJobs();
            
            if ($jobs->count() > 0) {
                // Send email with jobs
                // Mail::to($alert->email)->send(new JobAlertMail($alert, $jobs));
                $sent++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Job alerts sent to {$sent} subscribers",
            'data' => [
                'frequency' => $frequency,
                'total_alerts' => $alerts->count(),
                'sent_count' => $sent
            ]
        ]);
    }

    /**
     * Get available options for job alerts (locations, categories, etc.)
     */
    public function getOptions()
    {
        $locations = Location::active()
            ->orderBy('city')
            ->get()
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->full_name,
                    'value' => $location->city . ($location->state ? ', ' . $location->state : '')
                ];
            });

        $categories = JobCategory::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $experienceLevels = [
            ['value' => 0, 'label' => 'Fresher (0 years)'],
            ['value' => 1, 'label' => '1 year'],
            ['value' => 2, 'label' => '2 years'],
            ['value' => 3, 'label' => '3 years'],
            ['value' => 4, 'label' => '4 years'],
            ['value' => 5, 'label' => '5 years'],
            ['value' => 6, 'label' => '6 years'],
            ['value' => 7, 'label' => '7 years'],
            ['value' => 8, 'label' => '8 years'],
            ['value' => 9, 'label' => '9 years'],
            ['value' => 10, 'label' => '10+ years']
        ];

        $frequencies = collect(JobAlert::FREQUENCIES)
            ->map(function($label, $value) {
                return ['value' => $value, 'label' => $label];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'locations' => $locations,
                'categories' => $categories,
                'experience_levels' => $experienceLevels,
                'frequencies' => $frequencies
            ]
        ]);
    }
}