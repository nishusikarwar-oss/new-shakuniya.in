<?php

namespace App\Http\Controllers\API;

use App\Models\PerkBenefit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PerkBenefitController extends Controller
{
    /**
     * Display a listing of perks & benefits.
     */
    public function index(Request $request)
    {
        $query = PerkBenefit::query();

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->inCategory($request->category);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Get grouped by category
        if ($request->boolean('grouped')) {
            return response()->json([
                'success' => true,
                'data' => PerkBenefit::getGroupedByCategory()
            ]);
        }

        // Order by display order
        $query->ordered();

        $perks = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $perks
        ]);
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        $categories = collect(PerkBenefit::CATEGORIES)->map(function($label, $value) {
            return [
                'value' => $value,
                'label' => $label,
                'color' => PerkBenefit::CATEGORY_COLORS[$value] ?? 'gray',
                'icon' => PerkBenefit::CATEGORY_ICONS[$value] ?? 'gift',
                'count' => PerkBenefit::active()->inCategory($value)->count()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created perk.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon_name' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'category' => 'nullable|in:health,financial,work_life,growth,culture',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon_image']);
        
        // Ensure description is set (even if empty string)
        $data['description'] = $request->description ?? null;

        // Handle icon image upload
        if ($request->hasFile('icon_image')) {
            $path = $request->file('icon_image')->store('perks-benefits', 'public');
            $data['icon_image'] = $path;
        }

        $perk = PerkBenefit::create($data);

        // Return the created perk with all fields
        return response()->json([
            'success' => true,
            'message' => 'Perk/Benefit created successfully',
            'data' => $perk
        ], 201);
    }

    /**
     * Display the specified perk.
     */
    public function show($id)
    {
        $perk = PerkBenefit::find($id);

        if (!$perk) {
            return response()->json([
                'success' => false,
                'message' => 'Perk/Benefit not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $perk
        ]);
    }

    /**
     * Update the specified perk.
     */
    public function update(Request $request, $id)
    {
        $perk = PerkBenefit::find($id);

        if (!$perk) {
            return response()->json([
                'success' => false,
                'message' => 'Perk/Benefit not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon_name' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0',
            'category' => 'nullable|in:health,financial,work_life,growth,culture',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['icon_image']);
        
        // Ensure description is properly handled (allow empty string)
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }

        // Handle icon image upload
        if ($request->hasFile('icon_image')) {
            // Delete old image
            if ($perk->icon_image) {
                $oldPath = str_replace(asset('storage/'), '', $perk->icon_image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('icon_image')->store('perks-benefits', 'public');
            $data['icon_image'] = $path;
        }

        $perk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Perk/Benefit updated successfully',
            'data' => $perk
        ]);
    }

    /**
     * Remove the specified perk.
     */
    public function destroy($id)
    {
        $perk = PerkBenefit::find($id);

        if (!$perk) {
            return response()->json([
                'success' => false,
                'message' => 'Perk/Benefit not found'
            ], 404);
        }

        // Delete icon image
        if ($perk->icon_image) {
            $path = str_replace(asset('storage/'), '', $perk->icon_image);
            Storage::disk('public')->delete($path);
        }

        $perk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perk/Benefit deleted successfully'
        ]);
    }

    /**
     * Reorder perks
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:perks_benefits,id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            PerkBenefit::where('id', $order['id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perks reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $perk = PerkBenefit::find($id);

        if (!$perk) {
            return response()->json([
                'success' => false,
                'message' => 'Perk/Benefit not found'
            ], 404);
        }

        $perk->is_active = !$perk->is_active;
        $perk->save();

        return response()->json([
            'success' => true,
            'message' => 'Perk status updated successfully',
            'is_active' => $perk->is_active
        ]);
    }

    /**
     * Bulk delete perks
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:perks_benefits,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete images
        $perks = PerkBenefit::whereIn('id', $request->ids)->get();
        
        foreach ($perks as $perk) {
            if ($perk->icon_image) {
                $path = str_replace(asset('storage/'), '', $perk->icon_image);
                Storage::disk('public')->delete($path);
            }
        }

        PerkBenefit::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' perks deleted successfully'
        ]);
    }

    /**
     * Get perks statistics
     */
    public function stats()
    {
        $total = PerkBenefit::count();
        $active = PerkBenefit::active()->count();
        $inactive = $total - $active;

        $byCategory = [];
        foreach (array_keys(PerkBenefit::CATEGORIES) as $category) {
            $count = PerkBenefit::active()->inCategory($category)->count();
            if ($count > 0) {
                $byCategory[$category] = [
                    'name' => PerkBenefit::CATEGORIES[$category],
                    'count' => $count,
                    'color' => PerkBenefit::CATEGORY_COLORS[$category]
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
                'by_category' => $byCategory
            ]
        ]);
    }

    /**
     * Bulk update category for perks
     */
    public function bulkUpdateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:perks_benefits,id',
            'category' => 'required|in:health,financial,work_life,growth,culture'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        PerkBenefit::whereIn('id', $request->ids)
            ->update(['category' => $request->category]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated for ' . count($request->ids) . ' perks'
        ]);
    }
}