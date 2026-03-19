<?php

// ✅ FIX: Namespace changed from App\Http\Controllers\Api to App\Http\Controllers\API
// The file was named usercontroller.php (lowercase) - renamed to UserController.php
// Also fixed updateProfile() which incorrectly called User::create() instead of $user->save()

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     * GET /api/users
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
            }

            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $users   = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data'    => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     * POST /api/users
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'is_admin' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->is_admin ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     * GET /api/users/{id}
     */
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data'    => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified user.
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name'     => 'sometimes|required|string|max:255',
                'email'    => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'is_admin' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            if ($request->has('name'))     $user->name     = $request->name;
            if ($request->has('email'))    $user->email    = $request->email;
            if ($request->has('password')) $user->password = Hash::make($request->password);
            if ($request->has('is_admin')) $user->is_admin = $request->is_admin;

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data'    => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user.
     * DELETE /api/users/{id}
     */
    public function destroy($id)
    {
        try {
            if (Auth::id() == $id) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account'], 403);
            }

            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete users.
     * POST /api/users/bulk-delete
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids'   => 'required|array',
                'ids.*' => 'integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            if (in_array(Auth::id(), $request->ids)) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account'], 403);
            }

            User::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' users deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle user active status.
     * PATCH /api/users/{id}/toggle-status
     */
    public function toggleStatus($id)
    {
        try {
            if (Auth::id() == $id) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own status'], 403);
            }

            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $user->is_active = !$user->is_active;
            $user->save();

            return response()->json([
                'success'   => true,
                'message'   => 'User status updated successfully',
                'is_active' => $user->is_active,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user statistics.
     * GET /api/users/stats
     */
    public function stats()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => [
                    'total_users'           => User::count(),
                    'new_users_today'       => User::whereDate('created_at', today())->count(),
                    'new_users_this_week'   => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'new_users_this_month'  => User::whereMonth('created_at', now()->month)->count(),
                    'active_users'          => User::where('is_active', true)->count(),
                    'inactive_users'        => User::where('is_active', false)->count(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
