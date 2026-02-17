<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index()
    {
        $services = Service::all();
        return response()->json([
            'success' => true,
            'data' => $services,
            'total' => $services->count()
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'icon' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        
        $service = Service::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service
        ], 201);
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Display service by slug.
     */
    public function showBySlug($slug)
    {
        $service = Service::where('slug', $slug)->first();
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'short_description' => 'sometimes|required|string',
            'long_description' => 'sometimes|required|string',
            'icon' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title);
        }

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Search services by title or description.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $services = Service::where('title', 'LIKE', "%{$query}%")
            ->orWhere('short_description', 'LIKE', "%{$query}%")
            ->orWhere('long_description', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services,
            'total' => $services->count()
        ]);
    }
}