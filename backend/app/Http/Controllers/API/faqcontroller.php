<?php
// app/Http/Controllers/API/FaqController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;  // Make sure this line exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs.
     */
    public function index(Request $request)
    {
        $query = Faq::query();
        
        // Filter by active status
        if ($request->has('active')) {
            if ($request->active === 'true' || $request->active === '1') {
                $query->whereNotNull('status');
            } elseif ($request->active === 'false' || $request->active === '0') {
                $query->whereNull('status');
            }
        } else {
            // Default: show active FAQs only for public
            $query->whereNotNull('status');
        }
        
        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        // Sort by latest first
        $query->orderBy('id', 'desc');
        
        $faqs = $query->paginate($request->get('per_page', 10));
        
        // Transform data to include is_active flag
        $faqs->getCollection()->transform(function ($faq) {
            return [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'status' => $faq->status,
                'is_active' => $faq->is_active,
                'created_at' => $faq->created_at,
                'updated_at' => $faq->updated_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'status' => 'nullable|boolean' // We'll convert to timestamp if true
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
    }

    /**
     * Display the specified FAQ.
     */
    public function show($id)
    {
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
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, $id)
    {
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

        if ($request->has('question')) {
            $faq->question = $request->question;
        }
        
        if ($request->has('answer')) {
            $faq->answer = $request->answer;
        }
        
        // Handle status update
        if ($request->has('status')) {
            if ($request->status) {
                $faq->status = $faq->status ?? Carbon::now();
            } else {
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
    }

    /**
     * Toggle FAQ status.
     */
    public function toggleStatus($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ not found'
            ], 404);
        }

        if ($faq->status) {
            $faq->status = null; // Deactivate
        } else {
            $faq->status = Carbon::now(); // Activate with current timestamp
        }
        
        $faq->save();

        return response()->json([
            'success' => true,
            'message' => 'FAQ status toggled successfully',
            'is_active' => $faq->is_active,
            'status' => $faq->status
        ]);
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy($id)
    {
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
    }

    /**
     * Bulk delete FAQs.
     */
    public function bulkDelete(Request $request)
    {
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
    }

    /**
     * Get active FAQs only (for public frontend).
     */
    public function getActive()
    {
        $faqs = Faq::active()
            ->orderBy('id', 'desc')
            ->get()
            ->transform(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'is_active' => true
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }
}