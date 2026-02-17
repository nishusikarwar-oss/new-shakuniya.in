<?php
// app/Http/Controllers/API/ServiceFeatureController.php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ServiceFeatureController extends Controller
{
    /**
     * GET /api/service-features
     * GET /api/services/{serviceId}/features
     * List all features with optional service filter
     */
    public function index(Request $request)
    {
        $query = ServiceFeature::with('service');
        
        // Filter by service_id if provided
        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        
        // Filter by multiple service IDs
        if ($request->has('service_ids')) {
            $serviceIds = explode(',', $request->service_ids);
            $query->whereIn('service_id', $serviceIds);
        }
        
        $features = $query->get();
        
        // Group by service if requested
        if ($request->has('group_by_service') && $request->group_by_service == true) {
            $grouped = $features->groupBy('service_id');
            return response()->json([
                'success' => true,
                'data' => $grouped,
                'total_services' => $grouped->count(),
                'total_features' => $features->count()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $features,
            'total' => $features->count()
        ]);
    }

    /**
     * POST /api/service-features
     * POST /api/services/{serviceId}/features
     * Create a new service feature
     */
    public function store(Request $request, $serviceId = null)
    {
        // If serviceId is provided in URL, use it
        $service_id = $serviceId ?? $request->service_id;
        
        $validator = Validator::make(array_merge($request->all(), [
            'service_id' => $service_id
        ]), [
            'service_id' => 'required|exists:services,id',
            'feature' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $feature = ServiceFeature::create([
            'service_id' => $service_id,
            'feature' => $request->feature
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service feature created successfully',
            'data' => $feature->load('service')
        ], 201);
    }

    /**
     * GET /api/service-features/{id}
     * Get single service feature
     */
    public function show($id)
    {
        $feature = ServiceFeature::with('service')->find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Service feature not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * PUT /api/service-features/{id}
     * Update service feature
     */
    public function update(Request $request, $id)
    {
        $feature = ServiceFeature::find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Service feature not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'sometimes|required|exists:services,id',
            'feature' => 'sometimes|required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $feature->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service feature updated successfully',
            'data' => $feature->load('service')
        ]);
    }

    /**
     * DELETE /api/service-features/{id}
     * Delete service feature
     */
    public function destroy($id)
    {
        $feature = ServiceFeature::find($id);
        
        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Service feature not found'
            ], 404);
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service feature deleted successfully'
        ]);
    }

    /**
     * POST /api/services/{serviceId}/features/bulk
     * Bulk create features for a service
     */
    public function bulkStore(Request $request, $serviceId)
    {
        // Verify service exists
        $service = Service::find($serviceId);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'features' => 'required|array|min:1',
            'features.*' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $createdFeatures = [];
        foreach ($request->features as $featureText) {
            $createdFeatures[] = ServiceFeature::create([
                'service_id' => $serviceId,
                'feature' => $featureText
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($createdFeatures) . ' features created successfully',
            'data' => $createdFeatures
        ], 201);
    }

    /**
     * PUT /api/services/{serviceId}/features
     * Sync features (replace all features for a service)
     */
    public function sync(Request $request, $serviceId)
    {
        // Verify service exists
        $service = Service::find($serviceId);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'features' => 'required|array',
            'features.*' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete existing features
        $service->features()->delete();

        // Create new features
        $createdFeatures = [];
        foreach ($request->features as $featureText) {
            $createdFeatures[] = ServiceFeature::create([
                'service_id' => $serviceId,
                'feature' => $featureText
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Features synced successfully',
            'data' => $createdFeatures
        ]);
    }

    /**
     * DELETE /api/services/{serviceId}/features
     * Delete all features for a service
     */
    public function destroyAll($serviceId)
    {
        $service = Service::find($serviceId);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $count = $service->features()->count();
        $service->features()->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' features deleted successfully'
        ]);
    }
}