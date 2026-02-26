<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;  // ✅ Sahi: "Controller" (no extra 'l')
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActivityLogController extends Controller
{
    /**
     * Get dashboard statistics with percentage changes
     * This will give you the "0 ↑+15%" style metrics
     */
    public function dashboard()
    {
        try {
            // Today's stats
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            
            // Get today's count
            $todayResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) = ?", [$today]);
            $todayCount = !empty($todayResult) ? $todayResult[0]->total : 0;
            
            // Get yesterday's count
            $yesterdayResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) = ?", [$yesterday]);
            $yesterdayCount = !empty($yesterdayResult) ? $yesterdayResult[0]->total : 0;
            
            // Calculate percentage change for today
            $todayPercentage = 0;
            if ($yesterdayCount > 0) {
                $todayPercentage = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 2);
            } elseif ($todayCount > 0) {
                $todayPercentage = 100;
            }

            // This week vs last week
            $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
            $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));
            $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
            $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));
            
            $thisWeekResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) BETWEEN ? AND ?", [$thisWeekStart, $thisWeekEnd]);
            $thisWeekCount = !empty($thisWeekResult) ? $thisWeekResult[0]->total : 0;
            
            $lastWeekResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) BETWEEN ? AND ?", [$lastWeekStart, $lastWeekEnd]);
            $lastWeekCount = !empty($lastWeekResult) ? $lastWeekResult[0]->total : 0;
            
            // Calculate weekly percentage change
            $weeklyPercentage = 0;
            if ($lastWeekCount > 0) {
                $weeklyPercentage = round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100, 2);
            } elseif ($thisWeekCount > 0) {
                $weeklyPercentage = 100;
            }

            // Error rate for last 7 days
            $last7DaysTotalResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
            $last7DaysTotal = !empty($last7DaysTotalResult) ? $last7DaysTotalResult[0]->total : 0;
            
            $last7DaysErrorsResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND log_type = 'error'");
            $last7DaysErrors = !empty($last7DaysErrorsResult) ? $last7DaysErrorsResult[0]->total : 0;
            
            $errorRate = $last7DaysTotal > 0 ? round(($last7DaysErrors / $last7DaysTotal) * 100, 2) : 0;

            // Activity breakdown by action for last 30 days
            $activityBreakdown = DB::select("
                SELECT action, COUNT(*) as total 
                FROM activity_logs 
                WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY action 
                ORDER BY total DESC
            ");

            // Daily trend for last 30 days with percentage change
            $dailyTrend = DB::select("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as count,
                    COUNT(CASE WHEN log_type = 'error' THEN 1 END) as errors
                FROM activity_logs 
                WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date
            ");

            // Add percentage change to daily trend
            $dailyTrendWithPercentage = [];
            foreach ($dailyTrend as $index => $day) {
                $previousCount = $index > 0 ? $dailyTrend[$index - 1]->count : 0;
                $percentageChange = $previousCount > 0 
                    ? round((($day->count - $previousCount) / $previousCount) * 100, 2)
                    : 0;
                
                $dailyTrendWithPercentage[] = [
                    'date' => $day->date,
                    'count' => $day->count,
                    'errors' => $day->errors,
                    'percentage_change' => $percentageChange,
                    'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                    'display' => $day->count . ' ' . ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . abs($percentageChange) . '%'
                ];
            }

            // Recent activities
            $recentActivities = DB::select("
                SELECT 
                    al.*,
                    u.name as user_name
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT 10
            ");

            // Format recent activities
            $formattedRecent = [];
            foreach ($recentActivities as $activity) {
                $createdAt = new \DateTime($activity->created_at);
                $now = new \DateTime();
                $diff = $now->diff($createdAt);
                
                $timeAgo = '';
                if ($diff->d > 0) {
                    $timeAgo = $diff->d . ' days ago';
                } elseif ($diff->h > 0) {
                    $timeAgo = $diff->h . ' hours ago';
                } elseif ($diff->i > 0) {
                    $timeAgo = $diff->i . ' minutes ago';
                } else {
                    $timeAgo = 'just now';
                }
                
                $formattedRecent[] = [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'user_name' => $activity->user_name ?? 'System',
                    'log_type' => $activity->log_type,
                    'time_ago' => $timeAgo,
                    'created_at' => $activity->created_at
                ];
            }

            // Summary counts
            $loginResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE action = 'login' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $loginCount = !empty($loginResult) ? $loginResult[0]->total : 0;
            
            $createResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE action = 'create' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $createCount = !empty($createResult) ? $createResult[0]->total : 0;
            
            $updateResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE action = 'update' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $updateCount = !empty($updateResult) ? $updateResult[0]->total : 0;
            
            $deleteResult = DB::select("SELECT COUNT(*) as total FROM activity_logs WHERE action = 'delete' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $deleteCount = !empty($deleteResult) ? $deleteResult[0]->total : 0;
            
            $totalResult = DB::select("SELECT COUNT(*) as total FROM activity_logs");
            $totalActivities = !empty($totalResult) ? $totalResult[0]->total : 0;

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'today' => [
                        'count' => $todayCount,
                        'percentage_change' => $todayPercentage,
                        'trend' => $todayPercentage > 0 ? 'up' : ($todayPercentage < 0 ? 'down' : 'neutral'),
                        'display' => $todayCount . ' ' . ($todayPercentage > 0 ? '↑+' : ($todayPercentage < 0 ? '↓' : '')) . abs($todayPercentage) . '%'
                    ],
                    'this_week' => [
                        'count' => $thisWeekCount,
                        'percentage_change' => $weeklyPercentage,
                        'trend' => $weeklyPercentage > 0 ? 'up' : ($weeklyPercentage < 0 ? 'down' : 'neutral'),
                        'display' => $thisWeekCount . ' ' . ($weeklyPercentage > 0 ? '↑+' : ($weeklyPercentage < 0 ? '↓' : '')) . abs($weeklyPercentage) . '%'
                    ],
                    'error_rate' => [
                        'value' => $errorRate,
                        'count' => $last7DaysErrors,
                        'total' => $last7DaysTotal,
                        'display' => $errorRate . '% error rate'
                    ],
                    'total_activities' => $totalActivities,
                    'activity_breakdown' => $activityBreakdown,
                    'daily_trend' => $dailyTrendWithPercentage,
                    'recent_activities' => $formattedRecent,
                    'summary' => [
                        'logins' => $loginCount,
                        'creates' => $createCount,
                        'updates' => $updateCount,
                        'deletes' => $deleteCount
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard data',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get activity logs with filters
     */
    public function index(Request $request)
    {
        try {
            $query = "SELECT al.*, u.name as user_name FROM activity_logs al 
                      LEFT JOIN users u ON al.user_id = u.id WHERE 1=1";
            $countQuery = "SELECT COUNT(*) as total FROM activity_logs WHERE 1=1";
            $params = [];
            $countParams = [];

            // Apply filters
            if ($request->has('action') && !empty($request->action)) {
                $query .= " AND al.action = ?";
                $countQuery .= " AND action = ?";
                $params[] = $request->action;
                $countParams[] = $request->action;
            }

            if ($request->has('log_type') && !empty($request->log_type)) {
                $query .= " AND al.log_type = ?";
                $countQuery .= " AND log_type = ?";
                $params[] = $request->log_type;
                $countParams[] = $request->log_type;
            }

            if ($request->has('user_id') && !empty($request->user_id)) {
                $query .= " AND al.user_id = ?";
                $countQuery .= " AND user_id = ?";
                $params[] = $request->user_id;
                $countParams[] = $request->user_id;
            }

            if ($request->has('from_date') && !empty($request->from_date)) {
                $query .= " AND DATE(al.created_at) >= ?";
                $countQuery .= " AND DATE(created_at) >= ?";
                $params[] = $request->from_date;
                $countParams[] = $request->from_date;
            }

            if ($request->has('to_date') && !empty($request->to_date)) {
                $query .= " AND DATE(al.created_at) <= ?";
                $countQuery .= " AND DATE(created_at) <= ?";
                $params[] = $request->to_date;
                $countParams[] = $request->to_date;
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = "%{$request->search}%";
                $query .= " AND (al.description LIKE ? OR al.user_name LIKE ? OR al.action LIKE ?)";
                $countQuery .= " AND (description LIKE ? OR user_name LIKE ? OR action LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
                $countParams[] = $search;
                $countParams[] = $search;
                $countParams[] = $search;
            }

            // Get total count for pagination
            $totalResult = DB::select($countQuery, $countParams);
            $total = !empty($totalResult) ? $totalResult[0]->total : 0;

            // Pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            
            $query .= " ORDER BY al.created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int)$perPage;
            $params[] = (int)$offset;

            // Execute query
            $logs = DB::select($query, $params);

            return response()->json([
                'success' => true,
                'message' => 'Activity logs retrieved successfully',
                'data' => [
                    'current_page' => (int)$page,
                    'data' => $logs,
                    'per_page' => (int)$perPage,
                    'total' => (int)$total,
                    'last_page' => (int)ceil($total / $perPage)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving activity logs',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Store a new activity log
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'log_type' => 'required|in:info,success,warning,error',
            'user_id' => 'nullable|integer|exists:users,id',
            'user_name' => 'nullable|string|max:255',
            'subject_type' => 'nullable|string',
            'subject_id' => 'nullable|integer',
            'properties' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $properties = $request->properties ? json_encode($request->properties) : null;
            
            DB::insert("
                INSERT INTO activity_logs 
                (action, description, subject_type, subject_id, user_id, user_name, ip_address, user_agent, properties, log_type, count, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->action,
                $request->description,
                $request->subject_type,
                $request->subject_id,
               // $request->user_id ?? auth()->id(),
                //$request->user_name ?? (auth()->check() ? auth()->user()->name : 'System'),
                $request->ip(),
                $request->userAgent(),
                $properties,
                $request->log_type,
                1
            ]);

            $id = DB::getPdo()->lastInsertId();
            
            // Fetch the inserted record
            $logResult = DB::select("SELECT * FROM activity_logs WHERE id = ?", [$id]);
            $log = !empty($logResult) ? $logResult[0] : null;

            return response()->json([
                'success' => true,
                'message' => 'Activity log created successfully',
                'data' => $log
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating activity log',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get single activity log
     */
    public function show($id)
    {
        try {
            $logResult = DB::select("
                SELECT al.*, u.name as user_name 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE al.id = ?
            ", [$id]);

            if (empty($logResult)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity log not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Activity log retrieved successfully',
                'data' => $logResult[0]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving activity log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function stats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'required|in:day,week,month,year',
            'action' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $actionFilter = $request->action ? "AND action = '{$request->action}'" : "";
            
            switch ($request->period) {
                case 'day':
                    $stats = DB::select("
                        SELECT 
                            DATE(created_at) as period,
                            COUNT(*) as total,
                            COUNT(CASE WHEN log_type = 'error' THEN 1 END) as errors,
                            COUNT(CASE WHEN log_type = 'warning' THEN 1 END) as warnings,
                            COUNT(DISTINCT user_id) as unique_users
                        FROM activity_logs
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                        {$actionFilter}
                        GROUP BY DATE(created_at)
                        ORDER BY period
                    ");
                    break;
                    
                case 'week':
                    $stats = DB::select("
                        SELECT 
                            CONCAT(YEAR(created_at), '-W', LPAD(WEEK(created_at), 2, '0')) as period,
                            COUNT(*) as total,
                            COUNT(CASE WHEN log_type = 'error' THEN 1 END) as errors,
                            COUNT(CASE WHEN log_type = 'warning' THEN 1 END) as warnings,
                            COUNT(DISTINCT user_id) as unique_users
                        FROM activity_logs
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
                        {$actionFilter}
                        GROUP BY YEAR(created_at), WEEK(created_at)
                        ORDER BY period
                    ");
                    break;
                    
                case 'month':
                    $stats = DB::select("
                        SELECT 
                            DATE_FORMAT(created_at, '%Y-%m') as period,
                            COUNT(*) as total,
                            COUNT(CASE WHEN log_type = 'error' THEN 1 END) as errors,
                            COUNT(CASE WHEN log_type = 'warning' THEN 1 END) as warnings,
                            COUNT(DISTINCT user_id) as unique_users
                        FROM activity_logs
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                        {$actionFilter}
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                        ORDER BY period
                    ");
                    break;
                    
                case 'year':
                    $stats = DB::select("
                        SELECT 
                            YEAR(created_at) as period,
                            COUNT(*) as total,
                            COUNT(CASE WHEN log_type = 'error' THEN 1 END) as errors,
                            COUNT(CASE WHEN log_type = 'warning' THEN 1 END) as warnings,
                            COUNT(DISTINCT user_id) as unique_users
                        FROM activity_logs
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 YEAR)
                        {$actionFilter}
                        GROUP BY YEAR(created_at)
                        ORDER BY period
                    ");
                    break;
            }

            // Calculate percentage changes
            $statsWithPercentage = [];
            foreach ($stats as $index => $stat) {
                $previousTotal = $index > 0 ? $stats[$index - 1]->total : 0;
                $percentageChange = $previousTotal > 0 
                    ? round((($stat->total - $previousTotal) / $previousTotal) * 100, 2)
                    : 0;
                
                $statsWithPercentage[] = [
                    'period' => $stat->period,
                    'total' => $stat->total,
                    'errors' => $stat->errors,
                    'warnings' => $stat->warnings,
                    'unique_users' => $stat->unique_users,
                    'percentage_change' => $percentageChange,
                    'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral'),
                    'display' => $stat->total . ' ' . ($percentageChange > 0 ? '↑+' : ($percentageChange < 0 ? '↓' : '')) . abs($percentageChange) . '%'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statsWithPercentage
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
     * Clear old logs
     */
    public function clear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deleted = DB::delete("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$request->days]);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$deleted} old activity logs",
                'data' => [
                    'deleted_count' => $deleted
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing activity logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}