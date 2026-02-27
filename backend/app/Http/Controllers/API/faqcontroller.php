<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs.
     * GET /api/faqs
     */
    public function index(Request $request)
    {
        try {
            $query = Faq::query();
            
            // Filter by active status
            if ($request->has('active')) {
                if ($request->active === 'true' || $request->active === '1') {
                    $query->active();
                } elseif ($request->active === 'false' || $request->active === '0') {
                    $query->inactive();
                }
            } else {
                // Default: show active FAQs only for public
                $query->active();
            }
            
            // Search functionality
            if ($request->has('search')) {
                $query->search($request->search);
            }
            
            // Sort functionality
            $sortField = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $faqs = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $faqs->items(),
                'meta' => [
                    'total' => $faqs->total(),
                    'current_page' => $faqs->currentPage(),
                    'last_page' => $faqs->lastPage(),
                    'per_page' => $faqs->perPage(),
                ],
                'message' => 'FAQs retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FAQs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created FAQ.
     * POST /api/faqs
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'status' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'question' => $request->question,
                'answer' => $request->answer,
            ];

            // If status is true or not provided, set current timestamp
            if ($request->has('status') ? $request->status : true) {
                $data['status'] = Carbon::now();
            }

            $faq = Faq::create($data);

            return response()->json([
                'success' => true,
                'message' => 'FAQ created successfully',
                'data' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'status' => $faq->status,
                    'is_active' => $faq->is_active,
                    'created_at' => $faq->created_at,
                    'updated_at' => $faq->updated_at
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create FAQ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified FAQ.
     * GET /api/faqs/{id}
     */
    public function show($id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'status' => $faq->status,
                    'is_active' => $faq->is_active,
                    'created_at' => $faq->created_at,
                    'updated_at' => $faq->updated_at
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve FAQ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified FAQ.
     * PUT /api/faqs/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'question' => 'sometimes|required|string|max:255',
                'answer' => 'sometimes|required|string',
                'status' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update fields
            if ($request->has('question')) {
                $faq->question = $request->question;
            }
            
            if ($request->has('answer')) {
                $faq->answer = $request->answer;
            }
            
            // Handle status update
            if ($request->has('status')) {
                if ($request->status) {
                    // If activating and was inactive, set current timestamp
                    if (!$faq->status) {
                        $faq->status = Carbon::now();
                    }
                } else {
                    // If deactivating
                    $faq->status = null;
                }
            }

            $faq->save();

            return response()->json([
                'success' => true,
                'message' => 'FAQ updated successfully',
                'data' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'status' => $faq->status,
                    'is_active' => $faq->is_active,
                    'created_at' => $faq->created_at,
                    'updated_at' => $faq->updated_at
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FAQ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle FAQ status (Activate/Deactivate).
     * PATCH /api/faqs/{id}/toggle-status
     */
    public function toggleStatus($id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            if ($faq->status) {
                // Deactivate
                $faq->status = null;
                $message = 'FAQ deactivated successfully';
            } else {
                // Activate
                $faq->status = Carbon::now();
                $message = 'FAQ activated successfully';
            }
            
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $faq->is_active,
                'status' => $faq->status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle FAQ status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified FAQ.
     * DELETE /api/faqs/{id}
     */
    public function destroy($id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FAQ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete FAQs.
     * POST /api/faqs/bulk-delete
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:faqs,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            Faq::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQs deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FAQs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active FAQs only (for public frontend).
     * GET /api/faqs/active
     */
    public function getActive()
    {
        try {
            $faqs = Faq::active()
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'Active FAQs retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active FAQs.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}