<?php

namespace App\Http\Controllers\API;

use App\Models\TeamMember;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of team members.
     */
    public function index(Request $request)
    {
        $query = TeamMember::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter active only (for frontend)
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', 'LIKE', "%{$request->position}%");
        }

        // Order by display order
        $query->ordered();

        $members = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    /**
     * Store a newly created team member.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['profile_image']);

        // Set default company_id if not provided
        if (!isset($data['company_id'])) {
            $company = Company::first();
            $data['company_id'] = $company?->company_id ?? 1;
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('team-members', 'public');
            $data['profile_image'] = $path;
        }

        $member = TeamMember::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully',
            'data' => $member->load('company')
        ], 201);
    }

    /**
     * Display the specified team member.
     */
    public function show($id)
    {
        $member = TeamMember::with('company')->find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }

    /**
     * Update the specified team member.
     */
    public function update(Request $request, $id)
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'nullable|integer|exists:companies,company_id',
            'name' => 'sometimes|required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['profile_image']);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($member->profile_image) {
                Storage::disk('public')->delete($member->profile_image);
            }
            $path = $request->file('profile_image')->store('team-members', 'public');
            $data['profile_image'] = $path;
        }

        $member->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully',
            'data' => $member->load('company')
        ]);
    }

    /**
     * Remove the specified team member.
     */
    public function destroy($id)
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        // Delete profile image
        if ($member->profile_image) {
            Storage::disk('public')->delete($member->profile_image);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully'
        ]);
    }

    /**
     * Reorder team members
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required|array',
            'orders.*.member_id' => 'required|integer|exists:team_members,member_id',
            'orders.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->orders as $order) {
            TeamMember::where('member_id', $order['member_id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Team members reordered successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found'
            ], 404);
        }

        $member->is_active = !$member->is_active;
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Team member active status updated',
            'is_active' => $member->is_active
        ]);
    }

    /**
     * Bulk delete team members
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:team_members,member_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            
            // Get members to delete their images
            $members = TeamMember::whereIn('member_id', $ids)->get();
            
            // Delete associated images
            foreach ($members as $member) {
                if ($member->profile_image) {
                    Storage::disk('public')->delete($member->profile_image);
                }
            }
            
            // Delete members
            TeamMember::whereIn('member_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' team members deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting team members: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team members by position
     */
    public function getByPosition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|string|max:255',
            'company_id' => 'nullable|integer|exists:companies,company_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $companyId = $request->get('company_id', 1);

        $members = TeamMember::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('position', 'LIKE', "%{$request->position}%")
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    /**
     * Get team members with social links
     */
    public function getWithSocialLinks(Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $members = TeamMember::where('company_id', $companyId)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNotNull('linkedin_url')
                  ->orWhereNotNull('twitter_url');
            })
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }
}