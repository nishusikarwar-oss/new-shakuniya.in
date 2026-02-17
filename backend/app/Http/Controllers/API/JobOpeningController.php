<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOpeningResource;
use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobOpeningController extends Controller
{
    /**
     * Display a listing of job openings.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        try {
            $query = JobOpening::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('qualification', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            }

            // Filter by location
            if ($request->has('location')) {
                $query->where('location', 'like', "%{$request->location}%");
            }

            // Filter by experience
            if ($request->has('experience')) {
                $query->where('experience', 'like', "%{$request->experience}%");
            }

            // Filter by qualification
            if ($request->has('qualification')) {
                $query->where('qualification', 'like', "%{$request->qualification}%");
            }

            // Sort functionality
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max(1, $perPage), 50);

            $jobs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => JobOpeningResource::collection($jobs),
                'meta' => [
                    'total' => $jobs->total(),
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'from' => $jobs->firstItem(),
                    'to' => $jobs->lastItem(),
                ],
                'message' => 'Job openings retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job openings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created job opening.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'experience' => 'required|string|max:100',
                'positions' => 'required|integer|min:1',
                'qualification' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $job = JobOpening::create([
                'title' => $request->title,
                'description' => $request->description,
                'experience' => $request->experience,
                'positions' => $request->positions,
                'qualification' => $request->qualification,
                'location' => $request->location,
            ]);

            return response()->json([
                'success' => true,
                'data' => new JobOpeningResource($job),
                'message' => 'Job opening created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job opening.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified job opening.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $job = JobOpening::with('applications')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new JobOpeningResource($job),
                'message' => 'Job opening retrieved successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job opening.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified job opening.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $job = JobOpening::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'experience' => 'sometimes|required|string|max:100',
                'positions' => 'sometimes|required|integer|min:1',
                'qualification' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed.'
                ], 422);
            }

            $job->update($request->only([
                'title', 'description', 'experience', 
                'positions', 'qualification', 'location'
            ]));

            return response()->json([
                'success' => true,
                'data' => new JobOpeningResource($job),
                'message' => 'Job opening updated successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update job opening.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified job opening.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $job = JobOpening::findOrFail($id);
            
            // Check if there are applications
            if ($job->applications()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete job opening with existing applications.'
                ], 400);
            }

            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job opening deleted successfully.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job opening not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete job opening.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent job openings.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recent(Request $request)
    {
        try {
            $limit = min($request->get('limit', 5), 20);
            
            $jobs = JobOpening::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => JobOpeningResource::collection($jobs),
                'message' => 'Recent job openings retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent job openings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique locations.
     * 
     * @return \Illuminate\Http\Response
     */
    public function locations()
    {
        try {
            $locations = JobOpening::distinct()
                ->pluck('location')
                ->map(function($location) {
                    return trim($location);
                })
                ->unique()
                ->values();

            return response()->json([
                'success' => true,
                'data' => $locations,
                'message' => 'Job locations retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job locations.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}