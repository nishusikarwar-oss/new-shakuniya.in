<?php

namespace App\Http\Controllers\API;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index(Request $request)
    {
        $query = Location::query();

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter by country
        if ($request->has('country')) {
            $query->inCountry($request->country);
        }

        // Filter by state
        if ($request->has('state')) {
            $query->inState($request->state);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Get distinct countries
        if ($request->boolean('countries_only')) {
            $countries = Location::active()
                ->select('country')
                ->distinct()
                ->orderBy('country')
                ->pluck('country');

            return response()->json([
                'success' => true,
                'data' => $countries
            ]);
        }

        // Get distinct states
        if ($request->boolean('states_only')) {
            $states = Location::active()
                ->select('state')
                ->whereNotNull('state')
                ->distinct()
                ->orderBy('state')
                ->pluck('state');

            return response()->json([
                'success' => true,
                'data' => $states
            ]);
        }

        // Get grouped by country
        if ($request->boolean('grouped')) {
            return response()->json([
                'success' => true,
                'data' => Location::getGroupedByCountry()
            ]);
        }

        // Include job counts
        if ($request->boolean('with_counts')) {
            $query->withCount('jobs');
        }

        $locations = $query->orderBy('country')
            ->orderBy('state')
            ->orderBy('city')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate
        $exists = Location::where('city', $request->city)
            ->where('state', $request->state ?? null)
            ->where('country', $request->country ?? 'India')
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This location already exists'
            ], 400);
        }

        $location = Location::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data' => $location
        ], 201);
    }

    /**
     * Display the specified location.
     */
    public function show($id)
    {
        $location = Location::withCount('jobs')->find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, $id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'city' => 'sometimes|required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate (excluding current)
        $exists = Location::where('city', $request->city ?? $location->city)
            ->where('state', $request->state ?? $location->state)
            ->where('country', $request->country ?? $location->country)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This location already exists'
            ], 400);
        }

        $location->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => $location
        ]);
    }

    /**
     * Remove the specified location.
     */
    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Check if location has jobs
        if ($location->jobs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete location with associated jobs'
            ], 400);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        $location->is_active = !$location->is_active;
        $location->save();

        return response()->json([
            'success' => true,
            'message' => 'Location status updated successfully',
            'is_active' => $location->is_active
        ]);
    }

    /**
     * Bulk delete locations
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:locations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if any location has jobs
        $locationsWithJobs = Location::whereIn('id', $request->ids)
            ->withCount('jobs')
            ->having('jobs_count', '>', 0)
            ->get();

        if ($locationsWithJobs->count() > 0) {
            $names = $locationsWithJobs->pluck('city')->implode(', ');
            return response()->json([
                'success' => false,
                'message' => "Cannot delete locations with jobs: {$names}"
            ], 400);
        }

        Location::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' locations deleted successfully'
        ]);
    }

    /**
     * Get locations as options (for dropdowns)
     */
    public function getOptions(Request $request)
    {
        $query = Location::active();

        if ($request->has('country')) {
            $query->inCountry($request->country);
        }

        $locations = $query->orderBy('city')
            ->get(['id', 'city', 'state', 'country']);

        $formatted = $locations->map(function($location) {
            return [
                'id' => $location->id,
                'name' => $location->full_name,
                'city' => $location->city,
                'state' => $location->state,
                'country' => $location->country
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    /**
     * Get locations statistics
     */
    public function stats()
    {
        $total = Location::count();
        $active = Location::active()->count();
        $inactive = $total - $active;

        $byCountry = Location::select('country')
            ->selectRaw('count(*) as total')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->get();

        $byState = Location::select('state')
            ->selectRaw('count(*) as total')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $topLocationsWithJobs = Location::withCount('jobs')
            ->having('jobs_count', '>', 0)
            ->orderBy('jobs_count', 'desc')
            ->limit(5)
            ->get(['id', 'city', 'state', 'jobs_count']);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'by_country' => $byCountry,
                'top_states' => $byState,
                'top_locations' => $topLocationsWithJobs
            ]
        ]);
    }

    /**
     * Import locations from array
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locations' => 'required|array',
            'locations.*.city' => 'required|string|max:100',
            'locations.*.state' => 'nullable|string|max:100',
            'locations.*.country' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imported = 0;
        $skipped = 0;

        foreach ($request->locations as $locationData) {
            $exists = Location::where('city', $locationData['city'])
                ->where('state', $locationData['state'] ?? null)
                ->where('country', $locationData['country'] ?? 'India')
                ->exists();

            if (!$exists) {
                Location::create([
                    'city' => $locationData['city'],
                    'state' => $locationData['state'] ?? null,
                    'country' => $locationData['country'] ?? 'India',
                    'is_active' => true
                ]);
                $imported++;
            } else {
                $skipped++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} locations, skipped {$skipped} duplicates"
        ]);
    }
}