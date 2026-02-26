<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailMessage;
use App\Models\EmailStatistic;
use App\Models\EmailOpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmailMessageController extends Controller
{
    public function dashboard()
    {
        try {
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            
            $todayViews = EmailMessage::whereDate('created_at', $today)->sum('views_count');
            $yesterdayViews = EmailMessage::whereDate('created_at', $yesterday)->sum('views_count');
            
            $percentageChange = 0;
            if ($yesterdayViews > 0) {
                $percentageChange = round((($todayViews - $yesterdayViews) / $yesterdayViews) * 100, 2);
            } elseif ($todayViews > 0) {
                $percentageChange = 100;
            }
            
            $thisWeekViews = EmailMessage::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->sum('views_count');
            
            $lastWeekViews = EmailMessage::whereBetween('created_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ])->sum('views_count');
            
            $weeklyPercentage = 0;
            if ($lastWeekViews > 0) {
                $weeklyPercentage = round((($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100, 2);
            }
            
            $deviceBreakdown = EmailOpen::select('device_type', DB::raw('count(*) as total'))
                ->where('opened_at', '>=', now()->subDays(30))
                ->groupBy('device_type')
                ->get();
            
            $recentEmails = EmailMessage::orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'subject', 'recipient_email', 'status', 'views_count', 'created_at']);
            
            $trendData = EmailStatistic::getTrendData(30);
            
            $summary = [
                'total_emails' => EmailMessage::count(),
                'total_views' => EmailMessage::sum('views_count'),
                'unique_opens' => EmailMessage::sum('unique_opens'),
                'total_opens' => EmailMessage::sum('opens_count'),
                'average_open_rate' => round(EmailMessage::avg('opens_count') ?? 0, 2),
                'success_rate' => EmailMessage::count() > 0 
                    ? round((EmailMessage::where('status', 'delivered')->count() / EmailMessage::count()) * 100, 2)
                    : 0
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'today' => [
                        'views' => $todayViews,
                        'percentage_change' => $percentageChange,
                        'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                        'display' => $todayViews . ' ' . 
                            ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . 
                            abs($percentageChange) . '%'
                    ],
                    'this_week' => [
                        'views' => $thisWeekViews,
                        'percentage_change' => $weeklyPercentage,
                        'trend' => $weeklyPercentage > 0 ? 'up' : ($weeklyPercentage < 0 ? 'down' : 'neutral'),
                        'display' => $thisWeekViews . ' ' . 
                            ($weeklyPercentage > 0 ? '↑+' : ($weeklyPercentage < 0 ? '↓' : '')) . 
                            abs($weeklyPercentage) . '%'
                    ],
                    'device_breakdown' => $deviceBreakdown,
                    'recent_emails' => $recentEmails,
                    'trend_data' => $trendData,
                    'summary' => $summary
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = EmailMessage::query();
            
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('recipient_email', 'like', "%{$search}%");
                });
            }
            
            $sortField = $request->get('sort_field', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);
            
            $perPage = $request->get('per_page', 15);
            $emails = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Emails retrieved successfully',
                'data' => $emails
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving emails',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message_content' => 'required|string',
            'recipient_email' => 'required|email',
            'recipient_name' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email',
            'sender_name' => 'nullable|string|max:255',
            'status' => 'nullable|in:sent,delivered,opened,clicked,failed,draft'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = EmailMessage::create([
                'subject' => $request->subject,
                'message_content' => $request->message_content,
                //'sender_email' => $request->sender_email ?? (auth()->check() ? auth()->user()->email : 'system@example.com'),
                //'sender_name' => $request->sender_name ?? (auth()->check() ? auth()->user()->name : 'System'),
                'recipient_email' => $request->recipient_email,
                'recipient_name' => $request->recipient_name,
                'status' => $request->status ?? 'draft',
                'opens_count' => 0,
                'unique_opens' => 0,
                'clicks_count' => 0,
                'views_count' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email created successfully',
                'data' => $email
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $email = EmailMessage::with(['opens' => function($query) {
                $query->orderBy('opened_at', 'desc');
            }])->find($id);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found'
                ], 404);
            }

            $openStats = [
                'total' => $email->opens_count,
                'unique' => $email->unique_opens,
                'by_device' => $email->opens()
                    ->select('device_type', DB::raw('count(*) as total'))
                    ->groupBy('device_type')
                    ->get(),
                'by_date' => $email->opens()
                    ->select(DB::raw('DATE(opened_at) as date'), DB::raw('count(*) as total'))
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Email retrieved successfully',
                'data' => [
                    'email' => $email,
                    'open_statistics' => $openStats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $email = EmailMessage::find($id);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'subject' => 'sometimes|string|max:255',
            'message_content' => 'sometimes|string',
            'status' => 'sometimes|in:sent,delivered,opened,clicked,failed,draft'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email->update($request->only([
                'subject', 'message_content', 'status'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Email updated successfully',
                'data' => $email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $email = EmailMessage::find($id);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found'
                ], 404);
            }

            $email->delete();

            return response()->json([
                'success' => true,
                'message' => 'Email deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trackOpen($id)
    {
        try {
            $email = EmailMessage::findOrFail($id);
            $email->trackOpen();

            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            
            return response($pixel, 200)
                ->header('Content-Type', 'image/gif')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response('', 200)
                ->header('Content-Type', 'image/gif');
        }
    }

    public function markAsSent($id)
    {
        try {
            $email = EmailMessage::findOrFail($id);
            
            $email->status = 'sent';
            $email->sent_at = now();
            $email->save();

            return response()->json([
                'success' => true,
                'message' => 'Email marked as sent',
                'data' => $email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating email status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsDelivered($id)
    {
        try {
            $email = EmailMessage::findOrFail($id);
            
            $email->status = 'delivered';
            $email->delivered_at = now();
            $email->save();

            return response()->json([
                'success' => true,
                'message' => 'Email marked as delivered',
                'data' => $email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating email status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}