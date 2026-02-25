<?php

namespace App\Http\Controllers\API;

use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class InterviewScheduleController extends Controller
{
    /**
     * Display a listing of interview schedules.
     */
    public function index(Request $request)
    {
        $query = InterviewSchedule::with('application.job');

        // Filter by status
        if ($request->has('status')) {
            $query->withStatus($request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->onDate($request->date);
        }

        // Date range filter
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->betweenDates($request->date_from, $request->date_to);
        }

        // Filter by interviewer
        if ($request->has('interviewer')) {
            $query->withInterviewer($request->interviewer);
        }

        // Filter by application
        if ($request->has('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        // Upcoming/Past filters
        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        if ($request->boolean('past')) {
            $query->past();
        }

        // Today's interviews
        if ($request->boolean('today')) {
            $query->onDate(now());
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('interviewer_name', 'LIKE', "%{$search}%")
                  ->orWhere('interviewer_email', 'LIKE', "%{$search}%")
                  ->orWhere('additional_details', 'LIKE', "%{$search}%")
                  ->orWhereHas('application', function($q2) use ($search) {
                      $q2->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Default ordering
        if ($request->boolean('upcoming')) {
            $query->orderByUpcoming();
        } else {
            $query->orderBy('interview_date', 'desc')
                  ->orderBy('interview_time', 'desc');
        }

        $interviews = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Interview schedules retrieved successfully',
            'data' => $interviews
        ]);
    }

    /**
     * Get upcoming interviews for calendar
     */
    public function calendar(Request $request)
    {
        $query = InterviewSchedule::with('application')
            ->withStatus('scheduled');

        // Filter by date range
        if ($request->has('start') && $request->has('end')) {
            $query->betweenDates($request->start, $request->end);
        }

        $interviews = $query->get()->map(function($interview) {
            return [
                'id' => $interview->id,
                'title' => $interview->application->full_name . ' - ' . ($interview->application->job->title ?? 'Interview'),
                'start' => $interview->interview_date->format('Y-m-d') . 'T' . $interview->interview_time->format('H:i:s'),
                'end' => $interview->interview_date->format('Y-m-d') . 'T' . $interview->interview_time->addHour()->format('H:i:s'),
                'color' => $this->getCalendarColor($interview->interview_type),
                'extendedProps' => [
                    'application_id' => $interview->application_id,
                    'applicant_name' => $interview->application->full_name,
                    'type' => $interview->interview_type_label,
                    'status' => $interview->status_label,
                    'interviewer' => $interview->interviewer_name
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $interviews
        ]);
    }

    /**
     * Get calendar color based on interview type
     */
    private function getCalendarColor($type)
    {
        return [
            'online' => '#3b82f6',    // blue
            'in-person' => '#10b981',  // green
            'phone' => '#8b5cf6'       // purple
        ][$type] ?? '#6b7280';
    }

    /**
     * Store a newly created interview schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer|exists:job_applications,id',
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'interview_type' => 'nullable|in:online,in-person,phone',
            'interview_link' => 'nullable|url|max:500|required_if:interview_type,online',
            'interview_location' => 'nullable|string|max:255|required_if:interview_type,in-person',
            'interviewer_name' => 'nullable|string|max:255',
            'interviewer_email' => 'nullable|email|max:255',
            'meeting_platform' => 'nullable|in:google-meet,zoom,teams,other',
            'meeting_id' => 'nullable|string|max:255',
            'meeting_password' => 'nullable|string|max:255',
            'additional_details' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for existing scheduled interview
            $existing = InterviewSchedule::where('application_id', $request->application_id)
                ->where('status', 'scheduled')
                ->exists();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'This application already has a scheduled interview'
                ], 400);
            }

            $interview = InterviewSchedule::create($request->all());

            // Update application status to interviewed
            $application = JobApplication::find($request->application_id);
            $application->updateStatusWithHistory(
                'interviewed',
                'Interview scheduled on ' . $interview->formatted_date,
                auth()->user()?->email ?? 'system'
            );

            // Send email notification (optional)
            // $this->sendInterviewEmail($interview);

            return response()->json([
                'success' => true,
                'message' => 'Interview scheduled successfully',
                'data' => $interview->load('application.job')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule interview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified interview schedule.
     */
    public function show($id)
    {
        $interview = InterviewSchedule::with('application.job')->find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Interview schedule retrieved successfully',
            'data' => $interview
        ]);
    }

    /**
     * Get interviews for a specific application.
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

        $interviews = InterviewSchedule::where('application_id', $applicationId)
            ->with('application')
            ->orderBy('interview_date', 'desc')
            ->orderBy('interview_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Interviews retrieved successfully',
            'data' => [
                'application' => [
                    'id' => $application->id,
                    'name' => $application->full_name,
                    'job_title' => $application->job?->title
                ],
                'interviews' => $interviews
            ]
        ]);
    }

    /**
     * Update the specified interview schedule.
     */
    public function update(Request $request, $id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'interview_date' => 'nullable|date',
            'interview_time' => 'nullable',
            'interview_type' => 'nullable|in:online,in-person,phone',
            'interview_link' => 'nullable|url|max:500',
            'interview_location' => 'nullable|string|max:255',
            'interviewer_name' => 'nullable|string|max:255',
            'interviewer_email' => 'nullable|email|max:255',
            'meeting_platform' => 'nullable|in:google-meet,zoom,teams,other',
            'meeting_id' => 'nullable|string|max:255',
            'meeting_password' => 'nullable|string|max:255',
            'additional_details' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldDate = $interview->formatted_date;
            $oldStatus = $interview->status;
            
            $interview->update($request->all());

            // If status changed to completed, update application
            if ($request->has('status') && $request->status === 'completed' && $oldStatus !== 'completed') {
                $application = $interview->application;
                $application->updateStatusWithHistory(
                    $application->status, // keep current status
                    'Interview completed on ' . now()->format('M d, Y'),
                    auth()->user()?->email ?? 'system'
                );
            }

            // If date/time changed, send reschedule notification
            if ($request->has('interview_date') || $request->has('interview_time')) {
                // Send reschedule email
                // $this->sendRescheduleEmail($interview, $oldDate);
            }

            return response()->json([
                'success' => true,
                'message' => 'Interview schedule updated successfully',
                'data' => $interview->load('application.job')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update interview schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update interview status only.
     */
    public function updateStatus(Request $request, $id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldStatus = $interview->status;
            
            $interview->status = $request->status;
            if ($request->has('feedback')) {
                $interview->feedback = $request->feedback;
            }
            if ($request->has('rating')) {
                $interview->rating = $request->rating;
            }
            $interview->save();

            // If completed, update application
            if ($request->status === 'completed' && $oldStatus !== 'completed') {
                $application = $interview->application;
                $application->updateStatusWithHistory(
                    $application->status,
                    'Interview completed',
                    auth()->user()?->email ?? 'system'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Interview status updated successfully',
                'data' => $interview->load('application')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update interview status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add feedback to completed interview.
     */
    public function addFeedback(Request $request, $id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        if ($interview->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Feedback can only be added to completed interviews'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'feedback' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $interview->feedback = $request->feedback;
            if ($request->has('rating')) {
                $interview->rating = $request->rating;
            }
            $interview->save();

            return response()->json([
                'success' => true,
                'message' => 'Feedback added successfully',
                'data' => $interview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified interview schedule.
     */
    public function destroy($id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        try {
            $interview->delete();

            return response()->json([
                'success' => true,
                'message' => 'Interview schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete interview schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get interview statistics.
     */
    public function stats(Request $request)
    {
        $query = InterviewSchedule::query();

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->betweenDates($request->date_from, $request->date_to);
        }

        $total = $query->count();
        $scheduled = (clone $query)->where('status', 'scheduled')->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();
        $rescheduled = (clone $query)->where('status', 'rescheduled')->count();

        $byType = [
            'online' => (clone $query)->where('interview_type', 'online')->count(),
            'in_person' => (clone $query)->where('interview_type', 'in-person')->count(),
            'phone' => (clone $query)->where('interview_type', 'phone')->count()
        ];

        $today = (clone $query)->onDate(now())->count();
        $upcoming = (clone $query)->upcoming()->count();
        $past = (clone $query)->past()->count();

        $averageRating = (clone $query)->whereNotNull('rating')->avg('rating');

        $topInterviewers = (clone $query)->select('interviewer_name')
            ->selectRaw('count(*) as total')
            ->whereNotNull('interviewer_name')
            ->groupBy('interviewer_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [
                'total' => $total,
                'by_status' => [
                    'scheduled' => $scheduled,
                    'completed' => $completed,
                    'cancelled' => $cancelled,
                    'rescheduled' => $rescheduled
                ],
                'by_type' => $byType,
                'today' => $today,
                'upcoming' => $upcoming,
                'past' => $past,
                'average_rating' => round($averageRating ?? 0, 1),
                'top_interviewers' => $topInterviewers
            ]
        ]);
    }

    /**
     * Reschedule interview.
     */
    public function reschedule(Request $request, $id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'reason' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldDate = $interview->formatted_date;
            
            $interview->interview_date = $request->interview_date;
            $interview->interview_time = $request->interview_time;
            $interview->status = 'rescheduled';
            $interview->save();

            // Add note to additional details
            $notes = "Rescheduled from {$oldDate}";
            if ($request->reason) {
                $notes .= " - Reason: {$request->reason}";
            }
            $interview->additional_details = $interview->additional_details 
                ? $interview->additional_details . "\n" . $notes 
                : $notes;
            $interview->save();

            // Send reschedule notification
            // $this->sendRescheduleEmail($interview, $oldDate, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Interview rescheduled successfully',
                'data' => $interview->load('application')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reschedule interview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel interview.
     */
    public function cancel(Request $request, $id)
    {
        $interview = InterviewSchedule::find($id);

        if (!$interview) {
            return response()->json([
                'success' => false,
                'message' => 'Interview schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $interview->status = 'cancelled';
            
            if ($request->reason) {
                $notes = "Cancelled - Reason: {$request->reason}";
                $interview->additional_details = $interview->additional_details 
                    ? $interview->additional_details . "\n" . $notes 
                    : $notes;
            }
            
            $interview->save();

            // Send cancellation email
            // $this->sendCancellationEmail($interview, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Interview cancelled successfully',
                'data' => $interview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel interview',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}