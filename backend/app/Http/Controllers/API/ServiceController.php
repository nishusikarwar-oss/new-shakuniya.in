<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $query = Service::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter active services only (for frontend)
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Filter featured services
        if ($request->boolean('featured_only')) {
            $query->where('is_featured', true);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%")
                  ->orWhere('full_description', 'LIKE', "%{$search}%");
            });
        }

        // Order by display order
        $query->orderBy('display_order', 'asc');

        $services = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get service by slug
     */
    public function findBySlug($slug)
    {
        $service = Service::with('company')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

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
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services',
            'short_description' => 'nullable|string|max:300',
            'full_description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gradient_from' => 'nullable|string|max:50',
            'gradient_to' => 'nullable|string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon', 'featured_image']);

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = \App\Models\Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->title);
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('services/icons', 'public');
            $data['icon_url'] = $path;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('services/featured', 'public');
            $data['featured_image'] = $path;
        }

        $service = Service::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service->load('company')
        ], 201);
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $service = Service::with('company')->find($id);

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
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $id . ',service_id',
            'short_description' => 'nullable|string|max:300',
            'full_description' => 'nullable|string',
            'icon_name' => 'nullable|string|max:100',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gradient_from' => 'nullable|string|max:50',
            'gradient_to' => 'nullable|string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon', 'featured_image']);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($service->icon_url) {
                Storage::disk('public')->delete($service->icon_url);
            }
            $path = $request->file('icon')->store('services/icons', 'public');
            $data['icon_url'] = $path;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old featured image
            if ($service->featured_image) {
                Storage::disk('public')->delete($service->featured_image);
            }
            $path = $request->file('featured_image')->store('services/featured', 'public');
            $data['featured_image'] = $path;
        }

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service->load('company')
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

        // Delete associated files
        if ($service->icon_url) {
            Storage::disk('public')->delete($service->icon_url);
        }
        if ($service->featured_image) {
            Storage::disk('public')->delete($service->featured_image);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Reorder services
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.service_id' => 'required|integer|exists:services,service_id',
            'orders.*.display_order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            Service::where('service_id', $order['service_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Services reordered successfully'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $service->is_featured = !$service->is_featured;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service featured status updated',
            'is_featured' => $service->is_featured
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service active status updated',
            'is_active' => $service->is_active
        ]);
    }

    /**
     * Bulk delete services
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:services,service_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get services to delete their files
            $services = Service::whereIn('service_id', $ids)->get();
            
            // Delete associated files
            foreach ($services as $service) {
                if ($service->icon_url) {
                    Storage::disk('public')->delete($service->icon_url);
                }
                if ($service->featured_image) {
                    Storage::disk('public')->delete($service->featured_image);
                }
            }
            
            // Delete services
            Service::whereIn('service_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' services deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting services: ' . $e->getMessage()
            ], 500);
        }
    }
}
