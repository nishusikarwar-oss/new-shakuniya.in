<?php

namespace App\Http\Controllers\API;

use App\Models\Testimonial;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request)
    {
        $query = Testimonial::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Filter by exact rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Order by display order
        $query->ordered();

        $testimonials = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $testimonials
        ]);
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'testimonial_text' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['client_image']);

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        // Handle client image upload
        if ($request->hasFile('client_image')) {
            $path = $request->file('client_image')->store('testimonials', 'public');
            $data['client_image'] = $path;
        }

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial created successfully',
            'data' => $testimonial->load('company')
        ], 201);
    }

    /**
     * Display the specified testimonial.
     */
    public function show($id)
    {
        $testimonial = Testimonial::with('company')->find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $testimonial
        ]);
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'client_name' => 'sometimes|required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'testimonial_text' => 'sometimes|required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['client_image']);

        // Handle client image upload
        if ($request->hasFile('client_image')) {
            // Delete old image
            if ($testimonial->client_image && $testimonial->client_image !== asset('images/default-client.png')) {
                $oldPath = str_replace(asset('storage/'), '', $testimonial->client_image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('client_image')->store('testimonials', 'public');
            $data['client_image'] = $path;
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully',
            'data' => $testimonial->load('company')
        ]);
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        // Delete client image
        if ($testimonial->client_image && $testimonial->client_image !== asset('images/default-client.png')) {
            $path = str_replace(asset('storage/'), '', $testimonial->client_image);
            Storage::disk('public')->delete($path);
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully'
        ]);
    }

    /**
     * Reorder testimonials
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.testimonial_id' => 'required|integer|exists:testimonials,testimonial_id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            Testimonial::where('testimonial_id', $order['testimonial_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Testimonials reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        $testimonial->is_active = !$testimonial->is_active;
        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial active status updated',
            'is_active' => $testimonial->is_active
        ]);
    }

    /**
     * Bulk delete testimonials
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:testimonials,testimonial_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get testimonials to delete their images
            $testimonials = Testimonial::whereIn('testimonial_id', $ids)->get();
            
            // Delete associated images
            foreach ($testimonials as $testimonial) {
                if ($testimonial->client_image && $testimonial->client_image !== asset('images/default-client.png')) {
                    $path = str_replace(asset('storage/'), '', $testimonial->client_image);
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Delete testimonials
            Testimonial::whereIn('testimonial_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' testimonials deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting testimonials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rating statistics
     */
    public function ratingStats(Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $testimonials = Testimonial::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $stats = [
            'average' => round($testimonials->avg('rating') ?? 0, 1),
            'total' => $testimonials->count(),
            'distribution' => [
                5 => $testimonials->where('rating', 5)->count(),
                4 => $testimonials->where('rating', 4)->count(),
                3 => $testimonials->where('rating', 3)->count(),
                2 => $testimonials->where('rating', 2)->count(),
                1 => $testimonials->where('rating', 1)->count(),
            ],
            'percentage' => [
                5 => $testimonials->count() > 0 ? round(($testimonials->where('rating', 5)->count() / $testimonials->count()) * 100) : 0,
                4 => $testimonials->count() > 0 ? round(($testimonials->where('rating', 4)->count() / $testimonials->count()) * 100) : 0,
                3 => $testimonials->count() > 0 ? round(($testimonials->where('rating', 3)->count() / $testimonials->count()) * 100) : 0,
                2 => $testimonials->count() > 0 ? round(($testimonials->where('rating', 2)->count() / $testimonials->count()) * 100) : 0,
                1 => $testimonials->count() > 0 ? round(($testimonials->where('rating', 1)->count() / $testimonials->count()) * 100) : 0,
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get featured/highlighted testimonials
     */
    public function getFeatured(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $limit = $request->get('limit', 5);

        $testimonials = Testimonial::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('rating', '>=', 4)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $testimonials
        ]);
    }
}