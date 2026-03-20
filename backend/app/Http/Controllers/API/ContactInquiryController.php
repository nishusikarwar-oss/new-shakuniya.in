<?php

namespace App\Http\Controllers\API;

use App\Models\ContactInquiry;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;
class ContactInquiryController extends Controller
{
    /**
     * Display a listing of contact inquiries.
     */
    public function index(Request $request)
    {
        $query = ContactInquiry::with('company');

        // Filter by company
        if ($request->has('company_name')) {
            $query->where('company_name', $request->company_name);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->ofStatus($request->status);
        }

        // Filter by service interest
        if ($request->has('service')) {
            $query->where('service_interest', 'LIKE', "%{$request->service}%");
        }

        // Date range filter
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->dateBetween($request->date_from, $request->date_to);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        $inquiries = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inquiries
        ]);
    }

    /**
     * Store a newly created contact inquiry (public endpoint).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'service_interest' => 'nullable|string|max:255',
            'message' => 'required|string',
            'company_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Set default company_name if not provided
        if (!isset($data['company_name'])) {
            $company = Company::first();
            $data['company_name'] = $company?->company_name ?? 1;
        }

        // Add IP and user agent
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $inquiry = ContactInquiry::create($data);

        // Send email notification (optional)
        // $this->sendNotificationEmail($inquiry);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your inquiry. We will contact you soon!',
            'data' => $inquiry
        ], 201);
    }

    /**
     * Display the specified contact inquiry.
     */
    public function show($id)
    {
        $inquiry = ContactInquiry::with('company')->find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $inquiry
        ]);
    }

    /**
     * Update the specified contact inquiry status.
     */
    public function update(Request $request, $id)
    {
        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:pending,contacted,resolved',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'service_interest' => 'nullable|string|max:255',
            'message' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $inquiry->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data' => $inquiry->load('company')
        ]);
    }

    /**
     * Update inquiry status only.
     */
    public function updateStatus(Request $request, $id)
    {
        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,contacted,resolved'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $inquiry->status = $request->status;
        $inquiry->save();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry status updated successfully',
            'data' => [
                'inquiry_id' => $inquiry->inquiry_id,
                'status' => $inquiry->status,
                'status_label' => ContactInquiry::STATUSES[$inquiry->status]
            ]
        ]);
    }

    /**
     * Remove the specified contact inquiry.
     */
    public function destroy($id)
    {
        $inquiry = ContactInquiry::find($id);

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found'
            ], 404);
        }

        $inquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully'
        ]);
    }

    /**
     * Bulk delete inquiries
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:contact_inquiries,inquiry_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ids = $request->ids;
            ContactInquiry::whereIn('inquiry_id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' inquiries deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting inquiries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inquiries statistics
     */
    public function stats(Request $request)
    {
        $companyId = $request->get('company_name', 1);
        
        $query = ContactInquiry::where('company_name', $companyId);
        
        $total = $query->count();
        $pending = (clone $query)->pending()->count();
        $contacted = (clone $query)->contacted()->count();
        $resolved = (clone $query)->resolved()->count();
        
        // Get today's count
        $today = (clone $query)->whereDate('created_at', now()->toDateString())->count();
        
        // Get this week's count
        $thisWeek = (clone $query)->whereBetween('created_at', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ])->count();
        
        // Get this month's count
        $thisMonth = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get service interests breakdown
        $serviceInterests = (clone $query)
            ->select('service_interest', DB::raw('count(*) as total'))
            ->whereNotNull('service_interest')
            ->groupBy('service_interest')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'contacted' => $contacted,
                'resolved' => $resolved,
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
                'pending_percentage' => $total > 0 ? round(($pending / $total) * 100) : 0,
                'contacted_percentage' => $total > 0 ? round(($contacted / $total) * 100) : 0,
                'resolved_percentage' => $total > 0 ? round(($resolved / $total) * 100) : 0,
                'service_interests' => $serviceInterests
            ]
        ]);
    }

    /**
     * Export inquiries to CSV
     */
    public function export(Request $request)
    {
        $query = ContactInquiry::with('company');

        if ($request->has('company_name')) {
            $query->where('company_name', $request->company_name);
        }

        if ($request->has('status')) {
            $query->ofStatus($request->status);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->dateBetween($request->date_from, $request->date_to);
        }

        $inquiries = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Email', 'Phone', 'Service Interest', 'Message', 'Status', 'IP Address', 'Created At'];

        foreach ($inquiries as $inquiry) {
            $csvData[] = [
                $inquiry->inquiry_id,
                $inquiry->name,
                $inquiry->email,
                $inquiry->phone,
                $inquiry->service_interest,
                $inquiry->message,
                $inquiry->status,
                $inquiry->ip_address,
                $inquiry->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Create CSV response
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inquiries_' . now()->format('Y-m-d') . '.csv"'
        ]);
    }
}