<?php
// app/Http/Controllers/API/JobApplicationController.php

namespace App\Http\Controllers\API;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications (Admin only).
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['job' => function($q) {
            $q->select('id', 'title', 'slug');
        }]);

        // Filter by job
        if ($request->has('job_id')) {
            $query->forJob($request->job_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->ofStatus($request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->appliedBetween($request->date_from, $request->date_to);
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
        $query->orderBy('applied_at', 'desc');

        $applications = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Store a newly created job application (Public endpoint).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer|exists:job_openings,id,is_active,1',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'current_company' => 'nullable|string|max:255',
            'current_position' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'highest_qualification' => 'nullable|string|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
            'portfolio_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'expected_salary' => 'nullable|string|max:100',
            'notice_period' => 'nullable|string|max:100',
            'willing_to_relocate' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate application
        $existingApplication = JobApplication::where('job_id', $request->job_id)
            ->where('email', $request->email)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this position.'
            ], 400);
        }

        try {
            $data = $request->except(['resume']);

            // Handle resume upload
            if ($request->hasFile('resume')) {
                $path = $request->file('resume')->store('resumes', 'public');
                $data['resume_path'] = $path;
            }

            // Set applied_at
            $data['applied_at'] = now();

            // Add IP and user agent
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = $request->userAgent();

            $application = JobApplication::create($data);

            // Add initial status history
            $application->addStatusHistory('pending', 'Initial application submitted', $request->email);

            // Increment application count on job
            $job = JobOpening::find($request->job_id);
            $job->incrementApplicationCount();

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'data' => [
                    'id' => $application->id,
                    'job_title' => $job->title,
                    'applicant_name' => $application->full_name,
                    'applied_at' => $application->applied_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application submission failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified job application (Admin only).
     */
    public function show($id)
    {
        $application = JobApplication::with(['job', 'statusHistory'])->find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $application
        ]);
    }

    /**
     * Update the specified job application (Admin only).
     */
    public function update(Request $request, $id)
    {
        $application = JobApplication::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string',
            'expected_salary' => 'nullable|string|max:100',
            'notice_period' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $application->update($request->only([
            'admin_notes', 'expected_salary', 'notice_period'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully',
            'data' => $application->load('job')
        ]);
    }

    /**
     * Update application status with history tracking
     */
    public function updateStatus(Request $request, $id)
    {
        $application = JobApplication::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,shortlisted,interviewed,offered,hired,rejected',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Don't update if status is same
        if ($application->status === $request->status) {
            return response()->json([
                'success' => false,
                'message' => 'Application already has this status'
            ], 400);
        }

        // Update status with history
        $history = $application->updateStatusWithHistory(
            $request->status,
            $request->notes,
            auth()->user()?->email ?? 'system'
        );

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => [
                'application' => $application->fresh()->load('job'),
                'history' => $history
            ]
        ]);
    }

    /**
     * Download resume
     */
    public function downloadResume($id)
    {
        $application = JobApplication::find($id);

        if (!$application || !$application->resume_path) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found'
            ], 404);
        }

        $path = storage_path('app/public/' . $application->resume_path);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Resume file not found'
            ], 404);
        }

        $extension = pathinfo($application->resume_path, PATHINFO_EXTENSION);
        $filename = $application->first_name . '_' . $application->last_name . '_Resume.' . $extension;
        
        return response()->download($path, $filename);
    }

    /**
     * Remove the specified job application.
     */
    public function destroy($id)
    {
        $application = JobApplication::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        // Delete resume file
        if ($application->resume_path) {
            Storage::disk('public')->delete($application->resume_path);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    /**
     * Bulk delete applications
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:job_applications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete resume files
            $applications = JobApplication::whereIn('id', $request->ids)->get();
            
            foreach ($applications as $application) {
                if ($application->resume_path) {
                    Storage::disk('public')->delete($application->resume_path);
                }
            }
            
            JobApplication::whereIn('id', $request->ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' applications deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get applications statistics
     */
    public function stats(Request $request)
    {
        $query = JobApplication::query();

        if ($request->has('job_id')) {
            $query->forJob($request->job_id);
        }

        $total = $query->count();
        $pending = (clone $query)->where('status', JobApplication::STATUS_PENDING)->count();
        $reviewed = (clone $query)->where('status', JobApplication::STATUS_REVIEWED)->count();
        $shortlisted = (clone $query)->where('status', JobApplication::STATUS_SHORTLISTED)->count();
        $interviewed = (clone $query)->where('status', JobApplication::STATUS_INTERVIEWED)->count();
        $offered = (clone $query)->where('status', JobApplication::STATUS_OFFERED)->count();
        $hired = (clone $query)->where('status', JobApplication::STATUS_HIRED)->count();
        $rejected = (clone $query)->where('status', JobApplication::STATUS_REJECTED)->count();

        // Today's applications
        $today = (clone $query)->whereDate('applied_at', now()->toDateString())->count();

        // This week's applications
        $thisWeek = (clone $query)->whereBetween('applied_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        // This month's applications
        $thisMonth = (clone $query)->whereMonth('applied_at', now()->month)
            ->whereYear('applied_at', now()->year)
            ->count();

        // Applications by job
        $byJob = (clone $query)->select('job_id')
            ->with('job:id,title')
            ->selectRaw('count(*) as total')
            ->groupBy('job_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'by_status' => [
                    'pending' => $pending,
                    'reviewed' => $reviewed,
                    'shortlisted' => $shortlisted,
                    'interviewed' => $interviewed,
                    'offered' => $offered,
                    'hired' => $hired,
                    'rejected' => $rejected
                ],
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
                'by_job' => $byJob
            ]
        ]);
    }

    /**
     * Export applications to CSV
     */
    public function export(Request $request)
    {
        $query = JobApplication::with('job');

        if ($request->has('job_id')) {
            $query->forJob($request->job_id);
        }

        if ($request->has('status')) {
            $query->ofStatus($request->status);
        }

        $applications = $query->orderBy('applied_at', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'Job Title', 'First Name', 'Last Name', 'Email', 'Phone',
            'Current Company', 'Current Position', 'Experience (Years)',
            'Qualification', 'Status', 'Applied Date'
        ];

        foreach ($applications as $app) {
            $csvData[] = [
                $app->id,
                $app->job?->title ?? 'N/A',
                $app->first_name,
                $app->last_name,
                $app->email,
                $app->phone,
                $app->current_company ?? 'N/A',
                $app->current_position ?? 'N/A',
                $app->experience_years ?? 0,
                $app->highest_qualification ?? 'N/A',
                $app->status_label,
                $app->applied_at->format('Y-m-d H:i:s')
            ];
        }

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="job_applications_' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Check if already applied
     */
    public function checkApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|integer|exists:job_openings,id',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $exists = JobApplication::where('job_id', $request->job_id)
            ->where('email', $request->email)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'already_applied' => $exists
            ]
        ]);
    }
}