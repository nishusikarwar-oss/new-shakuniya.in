<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    // GET: All FAQs
    public function index()
    {
        $faqs = Faq::where('status', 1)->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'FAQs fetched successfully',
            'data' => $faqs
        ], 200);
    }

    // POST: Create FAQ
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string'
        ]);

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FAQ created successfully',
            'data' => $faq
        ], 201);
    }

    // GET: Single FAQ
    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $faq
        ], 200);
    }

    // PUT: Update FAQ
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $faq->update([
            'question' => $request->question ?? $faq->question,
            'answer'   => $request->answer ?? $faq->answer,
            'status'   => $request->status ?? $faq->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FAQ updated successfully',
            'data' => $faq
        ], 200);
    }

    // DELETE: Delete FAQ
    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'FAQ deleted successfully'
        ], 200);
    }

    // PATCH: Status Toggle
    public function toggleStatus($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->status = !$faq->status;
        $faq->save();

        return response()->json([
            'status' => true,
            'message' => 'FAQ status updated',
            'data' => $faq
        ], 200);
    }
}