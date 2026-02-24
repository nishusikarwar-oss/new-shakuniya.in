<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceFeatureController extends Controller
{
    /**
     * Display features for a specific service
     */
    public function index(Request $request, $serviceId = null)
    {
        if ($serviceId) {
            // Get features for a specific service
            $query = ServiceFeature::with('service')
                ->where('service_id', $serviceId);
        } else {
            // Get all features
            $query = ServiceFeature::with('service');
        }

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Order by display order
        $query->orderBy('display_order', 'asc');

        $features = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Store a newly created feature.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer|exists:services,service_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon', 'image']);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('service-features/icons', 'public');
            $data['icon_url'] = $path;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('service-features/images', 'public');
            $data['image_url'] = $path;
        }

        $feature = ServiceFeature::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully',
            'data' => $feature->load('service')
        ], 201);
    }

    /**
     * Display the specified feature.
     */
    public function show($id)
    {
        $feature = ServiceFeature::with('service')->find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feature
        ]);
    }

    /**
     * Update the specified feature.
     */
    public function update(Request $request, $id)
    {
        $feature = ServiceFeature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'sometimes|required|integer|exists:services,service_id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon', 'image']);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($feature->icon_url) {
                Storage::disk('public')->delete($feature->icon_url);
            }
            $path = $request->file('icon')->store('service-features/icons', 'public');
            $data['icon_url'] = $path;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($feature->image_url) {
                Storage::disk('public')->delete($feature->image_url);
            }
            $path = $request->file('image')->store('service-features/images', 'public');
            $data['image_url'] = $path;
        }

        $feature->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Feature updated successfully',
            'data' => $feature->load('service')
        ]);
    }

    /**
     * Remove the specified feature.
     */
    public function destroy($id)
    {
        $feature = ServiceFeature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        // Delete associated files
        if ($feature->icon_url) {
            Storage::disk('public')->delete($feature->icon_url);
        }
        if ($feature->image_url) {
            Storage::disk('public')->delete($feature->image_url);
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully'
        ]);
    }

    /**
     * Reorder features
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.feature_id' => 'required|integer|exists:service_features,feature_id',
            'orders.*.display_order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            ServiceFeature::where('feature_id', $order['feature_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Features reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $feature = ServiceFeature::find($id);

        if (!$feature) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        $feature->is_active = !$feature->is_active;
        $feature->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature active status updated',
            'is_active' => $feature->is_active
        ]);
    }

    /**
     * Bulk delete features
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:service_features,feature_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get features to delete their files
            $features = ServiceFeature::whereIn('feature_id', $ids)->get();
            
            // Delete associated files
            foreach ($features as $feature) {
                if ($feature->icon_url) {
                    Storage::disk('public')->delete($feature->icon_url);
                }
                if ($feature->image_url) {
                    Storage::disk('public')->delete($feature->image_url);
                }
            }
            
            // Delete features
            ServiceFeature::whereIn('feature_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' features deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting features: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clone features from one service to another
     */
    public function clone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_service_id' => 'required|integer|exists:services,service_id',
            'to_service_id' => 'required|integer|exists:services,service_id|different:from_service_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get features from source service
        $sourceFeatures = ServiceFeature::where('service_id', $request->from_service_id)->get();
        
        if ($sourceFeatures->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No features found in source service'
            ], 404);
        }

        $clonedCount = 0;
        foreach ($sourceFeatures as $feature) {
            $newFeature = $feature->replicate();
            $newFeature->service_id = $request->to_service_id;
            $newFeature->created_at = now();
            $newFeature->save();
            $clonedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$clonedCount} features cloned successfully"
        ]);
    }
}