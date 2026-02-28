<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * ===========================================
     * PUBLIC METHODS (Form Submission)
     * ===========================================
     */

    /**
     * Store contact message from frontend (PUBLIC)
     * POST /api/contact
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|min:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'status' => 'unread',
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us. We will get back to you soon!',
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ===========================================
     * ADMIN METHODS (Protected by Auth)
     * ===========================================
     */

    /**
     * Display all messages (ADMIN)
     * GET /api/admin/contact-messages
     */
    public function index(Request $request)
    {
        try {
            $query = ContactMessage::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%");
                });
            }

            // Date range filter
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
            }

            // Sort
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $messages = $query->paginate($perPage);

            // Get counts
            $counts = [
                'total' => ContactMessage::count(),
                'unread' => ContactMessage::where('status', 'unread')->count(),
                'read' => ContactMessage::where('status', 'read')->count(),
                'replied' => ContactMessage::where('status', 'replied')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $messages,
                'counts' => $counts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single message (ADMIN)
     * GET /api/admin/contact-messages/{id}
     */
    public function show($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);

            // Auto mark as read when viewed
            if ($message->status === 'unread') {
                $message->markAsRead();
            }

            return response()->json([
                'success' => true,
                'data' => $message
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    /**
     * Update message status (ADMIN)
     * PATCH /api/admin/contact-messages/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:unread,read,replied'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $message->status = $request->status;
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $message
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    /**
     * Update message (ADMIN)
     * PUT /api/admin/contact-messages/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'message' => 'sometimes|required|string',
                'status' => 'sometimes|in:unread,read,replied'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $message->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Message updated successfully',
                'data' => $message
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    /**
     * Delete message (ADMIN)
     * DELETE /api/admin/contact-messages/{id}
     */
    public function destroy($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    /**
     * Bulk delete messages (ADMIN)
     * POST /api/admin/contact-messages/bulk-delete
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:contact_messages,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            ContactMessage::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' messages deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics (ADMIN)
     * GET /api/admin/contact-messages/stats
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => ContactMessage::count(),
                'unread' => ContactMessage::where('status', 'unread')->count(),
                'read' => ContactMessage::where('status', 'read')->count(),
                'replied' => ContactMessage::where('status', 'replied')->count(),
                'today' => ContactMessage::whereDate('created_at', today())->count(),
                'this_week' => ContactMessage::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => ContactMessage::whereMonth('created_at', now()->month)->count(),
                'latest' => ContactMessage::latest()->take(5)->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}