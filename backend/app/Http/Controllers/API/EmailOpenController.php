<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailOpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailOpenController extends Controller
{
    public function index(Request $request, $emailId = null)
    {
        try {
            $query = EmailOpen::with('emailMessage');
            
            if ($emailId) {
                $query->where('email_message_id', $emailId);
            }
            
            if ($request->has('device_type') && !empty($request->device_type)) {
                $query->where('device_type', $request->device_type);
            }
            
            if ($request->has('is_unique') && $request->is_unique !== '') {
                $query->where('is_unique', $request->is_unique);
            }
            
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('opened_at', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('opened_at', '<=', $request->to_date);
            }
            
            $perPage = $request->get('per_page', 15);
            $opens = $query->orderBy('opened_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Opens retrieved successfully',
                'data' => $opens
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving opens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deviceBreakdown(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            
            $breakdown = EmailOpen::select('device_type', DB::raw('count(*) as total'))
                ->where('opened_at', '>=', now()->subDays($days))
                ->groupBy('device_type')
                ->get();
            
            $total = $breakdown->sum('total');
            
            $breakdown = $breakdown->map(function($item) use ($total) {
                $item->percentage = $total > 0 ? round(($item->total / $total) * 100, 2) : 0;
                return $item;
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Device breakdown retrieved successfully',
                'data' => $breakdown
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving device breakdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function hourlyDistribution(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            
            $distribution = EmailOpen::select(DB::raw('HOUR(opened_at) as hour'), DB::raw('count(*) as total'))
                ->where('opened_at', '>=', now()->subDays($days))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Hourly distribution retrieved successfully',
                'data' => $distribution
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving hourly distribution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uniqueStats(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            
            $total = EmailOpen::where('opened_at', '>=', now()->subDays($days))->count();
            $unique = EmailOpen::where('opened_at', '>=', now()->subDays($days))
                ->where('is_unique', true)
                ->count();
            
            $repeat = $total - $unique;
            
            return response()->json([
                'success' => true,
                'message' => 'Unique stats retrieved successfully',
                'data' => [
                    'total' => $total,
                    'unique' => $unique,
                    'repeat' => $repeat,
                    'unique_percentage' => $total > 0 ? round(($unique / $total) * 100, 2) : 0,
                    'repeat_percentage' => $total > 0 ? round(($repeat / $total) * 100, 2) : 0
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving unique stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}