<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailStatisticController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = EmailStatistic::query();
            
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->where('stat_date', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->where('stat_date', '<=', $request->to_date);
            }
            
            $limit = $request->get('limit', 30);
            $stats = $query->orderBy('stat_date', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'nullable|integer|min:1|max:90'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $days = $request->get('days', 30);
            $trendData = EmailStatistic::getTrendData($days);

            return response()->json([
                'success' => true,
                'message' => 'Trend data retrieved successfully',
                'data' => $trendData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving trend data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary()
    {
        try {
            $today = EmailStatistic::where('stat_date', now()->toDateString())->first();
            $yesterday = EmailStatistic::where('stat_date', now()->subDay()->toDateString())->first();
            
            $thisWeek = EmailStatistic::whereBetween('stat_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])->sum('views_count');
            
            $lastWeek = EmailStatistic::whereBetween('stat_date', [
                now()->subWeek()->startOfWeek()->toDateString(),
                now()->subWeek()->endOfWeek()->toDateString()
            ])->sum('views_count');

            $totalAllTime = EmailStatistic::sum('views_count');
            $averageDaily = EmailStatistic::avg('views_count');

            $weeklyPercentage = 0;
            if ($lastWeek > 0) {
                $weeklyPercentage = round((($thisWeek - $lastWeek) / $lastWeek) * 100, 2);
            }

            return response()->json([
                'success' => true,
                'message' => 'Summary retrieved successfully',
                'data' => [
                    'today' => [
                        'views' => $today->views_count ?? 0,
                        'percentage_change' => $today->percentage_change ?? 0,
                        'display' => ($today->views_count ?? 0) . ' ' . 
                            (($today->percentage_change ?? 0) > 0 ? '↑+' : (($today->percentage_change ?? 0) < 0 ? '↓' : '')) . 
                            abs($today->percentage_change ?? 0) . '%'
                    ],
                    'this_week' => [
                        'views' => $thisWeek,
                        'percentage_change' => $weeklyPercentage,
                        'display' => $thisWeek . ' ' . 
                            ($weeklyPercentage > 0 ? '↑+' : ($weeklyPercentage < 0 ? '↓' : '')) . 
                            abs($weeklyPercentage) . '%'
                    ],
                    'total_all_time' => $totalAllTime,
                    'average_daily' => round($averageDaily ?? 0, 2)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}