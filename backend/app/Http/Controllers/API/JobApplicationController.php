<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = JobApplication::query();

            // Filter by job_id
            if ($request->has('job_id')) {
                $query->where('job_id', $request->job_id);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max(1, $perPage), 100);

            $applications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => JobApplicationResource::collection($applications),
                'meta' => [
                    'total' => $applications->total(),
                    'current_page' => $applications->currentPage(),
                    'last_page' => $applications->lastPage(),
                    'per_page' => $applications->perPage(),
                    'from' => $applications->firstItem(),
                    'to' => $applications->lastItem(),
                ],
                'message' => 'Job applications retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job applications.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created job application.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_id' => 'required|integer',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'gender' => 'required|in:male,female,other',
                'message' => 'nullable|string',
                'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['job_id', 'full_name', 'email', 'phone', 'gender', 'message']);

            // Handle CV file upload
            if ($request->hasFile('cv_file')) {
                $file = $request->file('cv_file');
                $filename = 'cv_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('job-applications/cv', $filename, 'public');
                $data['cv_file'] = $path;
            }

            $application = JobApplication::create($data);

            return response()->json([
                'success' => true,
                'data' => new JobApplicationResource($application),
                'message' => 'Job application submitted successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit job application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified job application.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $application = JobApplication::with('job')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new JobApplicationResource($application),
                'message' => 'Job application retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job application not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified job application.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $application = JobApplication::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'job_id' => 'sometimes|required|integer',
                'full_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'gender' => 'sometimes|required|in:male,female,other',
                'message' => 'nullable|string',
                'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $data = $request->only(['job_id', 'full_name', 'email', 'phone', 'gender', 'message']);

            // Handle CV file upload
            if ($request->hasFile('cv_file')) {
                // Delete old CV
                if ($application->cv_file) {
                    Storage::disk('public')->delete($application->cv_file);
                }

                $file = $request->file('cv_file');
                $filename = 'cv_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('job-applications/cv', $filename, 'public');
                $data['cv_file'] = $path;
            }

            $application->update($data);

            return response()->json([
                'success' => true,
                'data' => new JobApplicationResource($application),
                'message' => 'Job application updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job application not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified job application.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $application = JobApplication::findOrFail($id);

            // Delete CV file
            if ($application->cv_file) {
                Storage::disk('public')->delete($application->cv_file);
            }

            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job application deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job application not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download CV file.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadCV($id)
    {
        try {
            $application = JobApplication::findOrFail($id);

            if (!$application->cv_file) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found.'
                ], 404);
            }

            $path = storage_path('app/public/' . $application->cv_file);

            if (!file_exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file does not exist.'
                ], 404);
            }

            return response()->download($path, $application->full_name . '_CV.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download CV.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get applications by job ID.
     * 
     * @param  int  $jobId
     * @return \Illuminate\Http\Response
     */
    public function byJob($jobId)
    {
        try {
            $applications = JobApplication::where('job_id', $jobId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => JobApplicationResource::collection($applications),
                'total' => $applications->count(),
                'message' => 'Job applications by job retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job applications by job.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}