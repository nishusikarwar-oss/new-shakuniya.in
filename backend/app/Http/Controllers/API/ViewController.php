<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\View;
use App\Models\ViewStatistic;
use App\Models\ViewsByType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    /**
     * Get dashboard with +8% metric
     */
    public function dashboard()
    {
        try {
            // Get today's stats
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            
            $todayStats = ViewStatistic::where('stat_date', $today)->first();
            $yesterdayStats = ViewStatistic::where('stat_date', $yesterday)->first();
            
            $todayViews = $todayStats->total_views ?? 0;
            $percentageChange = $todayStats->percentage_change ?? 0;
            
            // Get this week stats
            $thisWeekViews = ViewStatistic::whereBetween('stat_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])->sum('total_views');
            
            $lastWeekViews = ViewStatistic::whereBetween('stat_date', [
                now()->subWeek()->startOfWeek()->toDateString(),
                now()->subWeek()->endOfWeek()->toDateString()
            ])->sum('total_views');
            
            $weeklyPercentage = 0;
            if ($lastWeekViews > 0) {
                $weeklyPercentage = round((($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100, 2);
            }
            
            // Get device breakdown
            $deviceBreakdown = [
                'desktop' => $todayStats->desktop_views ?? 0,
                'mobile' => $todayStats->mobile_views ?? 0,
                'tablet' => $todayStats->tablet_views ?? 0
            ];
            
            // Get views by type
            $viewsByType = ViewsByType::where('stat_date', $today)
                ->orderBy('total_views', 'desc')
                ->get();
            
            // Get recent views
            $recentViews = View::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Get trend data
            $trendData = ViewStatistic::getTrendData(30);
            
            // Calculate totals
            $totalAllTime = ViewStatistic::sum('total_views');
            $uniqueAllTime = ViewStatistic::sum('unique_views');
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'today' => [
                        'views' => $todayViews,
                        'percentage_change' => $percentageChange,
                        'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                        'display' => 'Views ' . ($percentageChange > 0 ? '+' : '') . $percentageChange . '%',
                        'full_display' => $todayViews . ' ' . 
                            ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . 
                            abs($percentageChange) . '%'
                    ],
                    'this_week' => [
                        'views' => $thisWeekViews,
                        'percentage_change' => $weeklyPercentage,
                        'display' => $thisWeekViews . ' ' . 
                            ($weeklyPercentage > 0 ? '↑+' : ($weeklyPercentage < 0 ? '↓' : '')) . 
                            abs($weeklyPercentage) . '%'
                    ],
                    'device_breakdown' => $deviceBreakdown,
                    'views_by_type' => $viewsByType,
                    'recent_views' => $recentViews,
                    'trend_data' => $trendData,
                    'totals' => [
                        'all_time' => $totalAllTime,
                        'unique_all_time' => $uniqueAllTime
                    ]
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

    /**
     * Get all views with filters
     */
    public function index(Request $request)
    {
        try {
            $query = View::with('user');
            
            // Filter by viewable type
            if ($request->has('viewable_type') && !empty($request->viewable_type)) {
                $query->where('viewable_type', $request->viewable_type);
            }
            
            // Filter by viewable ID
            if ($request->has('viewable_id') && !empty($request->viewable_id)) {
                $query->where('viewable_id', $request->viewable_id);
            }
            
            // Filter by device type
            if ($request->has('device_type') && !empty($request->device_type)) {
                $query->where('device_type', $request->device_type);
            }
            
            // Filter by unique
            if ($request->has('is_unique') && $request->is_unique !== '') {
                $query->where('is_unique', $request->is_unique);
            }
            
            // Filter by user
            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('user_id', $request->user_id);
            }
            
            // Date range filter
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $views = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Views retrieved successfully',
                'data' => $views
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving views',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track a new view
     */
    public function track(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'viewable_type' => 'required|string',
            'viewable_id' => 'required|integer',
            'view_duration' => 'nullable|integer|min:0',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            $sessionId = $request->session()->getId() ?? uniqid('sess_');
            
            // Check if this is a unique view
            $existingView = View::where('viewable_type', $request->viewable_type)
                ->where('viewable_id', $request->viewable_id)
                ->where('ip_address', $ip)
                ->whereDate('created_at', now()->toDateString())
                ->exists();
            
            $isUnique = !$existingView;
            
            // Detect device, browser, OS
            $deviceInfo = $this->detectDeviceInfo($userAgent);
            
            // Create view record
            $view = View::create([
                'viewable_type' => $request->viewable_type,
                'viewable_id' => $request->viewable_id,
               // 'user_id' => auth()->check() ? auth()->id() : null,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'is_unique' => $isUnique,
                'session_id' => $sessionId,
                'referer_url' => $request->header('referer'),
                'view_duration' => $request->view_duration ?? 0,
                'metadata' => $request->metadata,
                'created_at' => now()
            ]);
            
            // Update statistics
            $this->updateViewStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'View tracked successfully',
                'data' => [
                    'id' => $view->id,
                    'is_unique' => $isUnique
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error tracking view',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single view details
     */
    public function show($id)
    {
        try {
            $view = View::with('user')->find($id);
            
            if (!$view) {
                return response()->json([
                    'success' => false,
                    'message' => 'View not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'View retrieved successfully',
                'data' => $view
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving view',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get view statistics
     */
    public function statistics(Request $request)
    {
        try {
            $query = ViewStatistic::query();
            
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

    /**
     * Get device breakdown
     */
    public function deviceBreakdown(Request $request)
    {
        try {
            $date = $request->get('date', now()->toDateString());
            
            $stats = ViewStatistic::where('stat_date', $date)->first();
            
            if (!$stats) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data for this date'
                ], 404);
            }
            
            $total = $stats->total_views;
            $breakdown = [
                'desktop' => [
                    'count' => $stats->desktop_views,
                    'percentage' => $total > 0 ? round(($stats->desktop_views / $total) * 100, 2) : 0
                ],
                'mobile' => [
                    'count' => $stats->mobile_views,
                    'percentage' => $total > 0 ? round(($stats->mobile_views / $total) * 100, 2) : 0
                ],
                'tablet' => [
                    'count' => $stats->tablet_views,
                    'percentage' => $total > 0 ? round(($stats->tablet_views / $total) * 100, 2) : 0
                ]
            ];
            
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

    /**
     * Get trend data
     */
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
            $trendData = ViewStatistic::getTrendData($days);
            
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

    /**
     * Get summary statistics
     */
    public function summary()
    {
        try {
            $today = ViewStatistic::where('stat_date', now()->toDateString())->first();
            $yesterday = ViewStatistic::where('stat_date', now()->subDay()->toDateString())->first();
            
            $totalAllTime = ViewStatistic::sum('total_views');
            $uniqueAllTime = ViewStatistic::sum('unique_views');
            $averageDaily = ViewStatistic::avg('total_views');
            
            $peakDay = ViewStatistic::orderBy('total_views', 'desc')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Summary retrieved successfully',
                'data' => [
                    'today' => [
                        'views' => $today->total_views ?? 0,
                        'unique' => $today->unique_views ?? 0,
                        'percentage_change' => $today->percentage_change ?? 0,
                        'display' => 'Views ' . (($today->percentage_change ?? 0) > 0 ? '+' : '') . ($today->percentage_change ?? 0) . '%'
                    ],
                    'yesterday' => [
                        'views' => $yesterday->total_views ?? 0,
                        'unique' => $yesterday->unique_views ?? 0
                    ],
                    'all_time' => [
                        'total_views' => $totalAllTime,
                        'unique_views' => $uniqueAllTime,
                        'average_daily' => round($averageDaily ?? 0, 2),
                        'peak_day' => $peakDay ? [
                            'date' => $peakDay->stat_date->format('Y-m-d'),
                            'views' => $peakDay->total_views
                        ] : null
                    ]
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

    /**
     * Get views by type
     */
    public function viewsByType(Request $request)
    {
        try {
            $date = $request->get('date', now()->toDateString());
            
            $viewsByType = ViewsByType::where('stat_date', $date)
                ->orderBy('total_views', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Views by type retrieved successfully',
                'data' => $viewsByType
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving views by type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update view statistics (call this after tracking views)
     */
    private function updateViewStatistics()
    {
        $today = now()->toDateString();
        
        // Calculate today's stats
        $totalViews = View::whereDate('created_at', $today)->count();
        $uniqueViews = View::whereDate('created_at', $today)->where('is_unique', true)->count();
        $desktopViews = View::whereDate('created_at', $today)->where('device_type', 'desktop')->count();
        $mobileViews = View::whereDate('created_at', $today)->where('device_type', 'mobile')->count();
        $tabletViews = View::whereDate('created_at', $today)->where('device_type', 'tablet')->count();
        $guestViews = View::whereDate('created_at', $today)->whereNull('user_id')->count();
        $loggedInViews = View::whereDate('created_at', $today)->whereNotNull('user_id')->count();
        $avgDuration = View::whereDate('created_at', $today)->avg('view_duration') ?? 0;
        
        // Get yesterday's views for percentage change
        $yesterdayViews = View::whereDate('created_at', now()->subDay()->toDateString())->count();
        
        $percentageChange = 0;
        if ($yesterdayViews > 0) {
            $percentageChange = round((($totalViews - $yesterdayViews) / $yesterdayViews) * 100, 2);
        } elseif ($totalViews > 0) {
            $percentageChange = 100;
        }
        
        // Update or create statistics
        ViewStatistic::updateOrCreate(
            ['stat_date' => $today],
            [
                'total_views' => $totalViews,
                'unique_views' => $uniqueViews,
                'desktop_views' => $desktopViews,
                'mobile_views' => $mobileViews,
                'tablet_views' => $tabletViews,
                'guest_views' => $guestViews,
                'logged_in_views' => $loggedInViews,
                'average_duration' => $avgDuration,
                'percentage_change' => $percentageChange
            ]
        );
        
        // Update views by type
        $viewTypes = View::whereDate('created_at', $today)
            ->select('viewable_type', DB::raw('count(*) as total'), DB::raw('count(CASE WHEN is_unique = 1 THEN 1 END) as unique_count'))
            ->groupBy('viewable_type')
            ->get();
        
        foreach ($viewTypes as $type) {
            ViewsByType::updateOrCreate(
                [
                    'stat_date' => $today,
                    'viewable_type' => $type->viewable_type
                ],
                [
                    'total_views' => $type->total,
                    'unique_views' => $type->unique_count,
                    'percentage_of_total' => $totalViews > 0 ? round(($type->total / $totalViews) * 100, 2) : 0
                ]
            );
        }
    }

    /**
     * Detect device info from user agent
     */
    private function detectDeviceInfo($userAgent)
    {
        $userAgent = strtolower($userAgent ?? '');
        
        // Detect device type
        if (preg_match('/(mobile|iphone|ipod|android)/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/(tablet|ipad)/i', $userAgent)) {
            $deviceType = 'tablet';
        } else {
            $deviceType = 'desktop';
        }
        
        // Detect browser
        if (strpos($userAgent, 'chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'msie') !== false || strpos($userAgent, 'trident') !== false) {
            $browser = 'Internet Explorer';
        } else {
            $browser = 'Unknown';
        }
        
        // Detect OS
        if (strpos($userAgent, 'windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'mac') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'ios') !== false || strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
            $os = 'iOS';
        } else {
            $os = 'Unknown';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os
        ];
    }
}