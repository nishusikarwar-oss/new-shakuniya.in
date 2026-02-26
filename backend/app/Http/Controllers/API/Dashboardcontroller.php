<?php
// app/Http/Controllers/admin/DashboardController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\JobOpening;
use App\Models\JobApplication;
use App\Models\Perk;
use App\Models\ProductFeature;
use App\Models\ServiceFeature;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        // Dashboard data with all calculations
        try{
        $data = [
            'stats_cards' => [
                'total_users' => $this->getTotalUsersWithGrowth(),
                'activity_logs' => $this->getActivityLogsWithGrowth(),
                'gallery' => $this->getGalleryWithGrowth(),
                'blogs' => $this->getBlogsWithGrowth(),
                'career' => $this->getCareerStats()
            ],
            'recent_activities' => $this->getRecentActivities(),
            'chart_data' => $this->getUserGrowthChartData()
        ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data fetched successfully',
                'data' => $data
            ], 200);

          } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Total Users with Daily Growth Percentage
     */
    private function getTotalUsersWithGrowth()
    {
        // Total users count
        $totalUsers = User::count();
        
        // Today's new users
        $todayUsers = User::whereDate('created_at', Carbon::today())->count();
        
        // Yesterday's new users
        $yesterdayUsers = User::whereDate('created_at', Carbon::yesterday())->count();
        
        // Calculate growth percentage
        $growthPercentage = $this->calculateDailyGrowth($todayUsers, $yesterdayUsers);
        
        return [
            'title' => 'Total Users',
            'value' => $totalUsers,
            'today_new' => $todayUsers,
            'growth' => $this->formatGrowth($growthPercentage),
            'trend' => $growthPercentage >= 0 ? 'up' : 'down',
            'icon' => 'users',
            'color' => 'primary'
        ];
    }

    /**
     * Get Activity Logs with Growth
     */
    private function getActivityLogsWithGrowth()
    {
        // Check if sessions table exists
        if (!Schema::hasTable('sessions')) {
            return [
                'title' => 'Activity Logs',
                'value' => 0,
                'today' => 0,
                'growth' => '0%',
                'trend' => 'neutral',
                'icon' => 'activity',
                'color' => 'info'
            ];
        }
        
        // Today's activities (active sessions)
        $todayActivities = DB::table('sessions')
            ->whereDate('last_activity', Carbon::today())
            ->count();
        
        // Yesterday's activities
        $yesterdayActivities = DB::table('sessions')
            ->whereDate('last_activity', Carbon::yesterday())
            ->count();
        
        // Total activities (last 7 days)
        $weeklyActivities = DB::table('sessions')
            ->where('last_activity', '>=', Carbon::now()->subDays(7)->timestamp)
            ->count();
        
        // Calculate growth
        $growthPercentage = $this->calculateDailyGrowth($todayActivities, $yesterdayActivities);
        
        return [
            'title' => 'Activity Logs',
            'value' => $weeklyActivities,
            'today' => $todayActivities,
            'growth' => $this->formatGrowth($growthPercentage),
            'trend' => $growthPercentage >= 0 ? 'up' : 'down',
            'icon' => 'graph-up',
            'color' => 'success'
        ];
    }

    /**
     * Get Gallery Stats with Growth
     */
    private function getGalleryWithGrowth()
    {
        $totalImages = GalleryImage::count();
        
        // This week uploads
        $thisWeekUploads = GalleryImage::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();
        
        // Last week uploads
        $lastWeekUploads = GalleryImage::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->count();
        
        // Calculate weekly growth
        $growthPercentage = $this->calculateGrowth($thisWeekUploads, $lastWeekUploads);
        
        return [
            'title' => 'Gallery',
            'value' => $totalImages,
            'this_week' => $thisWeekUploads,
            'growth' => $this->formatGrowth($growthPercentage),
            'trend' => $growthPercentage >= 0 ? 'up' : 'down',
            'icon' => 'images',
            'color' => 'warning'
        ];
    }

    /**
     * Get Blogs Stats with Growth
     */
    private function getBlogsWithGrowth()
    {
        $totalBlogs = Blog::count();
        
        // This month blogs
        $thisMonthBlogs = Blog::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Last month blogs
        $lastMonthBlogs = Blog::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        
        // Calculate monthly growth
        $growthPercentage = $this->calculateGrowth($thisMonthBlogs, $lastMonthBlogs);
        
    //     // Published vs Draft
    // $publishedBlogs = Blog::where('is_published', true)->count(); // if you have is_published column
    // $publishedBlogs = Blog::where('published_at', '<=', now())->count(); // if you have published_at column
        return [
            'title' => 'Blogs',
            'value' => $totalBlogs,
          //  'published' => $publishedBlogs,
            // 'drafts' => $draftBlogs,
            'growth' => $this->formatGrowth($growthPercentage),
            'trend' => $growthPercentage >= 0 ? 'up' : 'down',
            'icon' => 'file-text',
            'color' => 'danger'
        ];
    }

    /**
     * Get Career Stats
     */
    private function getCareerStats()
    {
        $totalJobs = JobOpening::count();
        $activeJobs = JobOpening::count();
        $totalApplications = JobApplication::count();
        
        // This week applications
        $weeklyApplications = JobApplication::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();
        
        // Pending applications
        $pendingApplications = JobApplication::count();
        
        return [
            'title' => 'Career',
            'total_openings' => $totalJobs,
            'active_openings' => $activeJobs,
            'total_applications' => $totalApplications,
            'weekly_applications' => $weeklyApplications,
            'pending' => $pendingApplications,
            'icon' => 'briefcase',
            'color' => 'dark'
        ];
    }

    /**
     * Calculate Daily Growth Percentage
     */
    private function calculateDailyGrowth($today, $yesterday)
    {
        if ($yesterday == 0) {
            return $today > 0 ? 100 : 0;
        }
        
        return (($today - $yesterday) / $yesterday) * 100;
    }

    /**
     * Calculate General Growth Percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Format Growth with + or - sign
     */
    private function formatGrowth($percentage)
    {
        $rounded = round(abs($percentage));
        
        if ($percentage > 0) {
            return '+' . $rounded . '%';
        } elseif ($percentage < 0) {
            return '-' . $rounded . '%';
        } else {
            return '0%';
        }
    }

    /**
     * Get User Growth Chart Data (Last 30 days)
     */
    private function getUserGrowthChartData()
    {
        $days = 30;
        $labels = [];
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        // Calculate cumulative users
        $cumulativeData = [];
        $runningTotal = 0;
        foreach ($data as $daily) {
            $runningTotal += $daily;
            $cumulativeData[] = $runningTotal;
        }
        
        return [
            'labels' => $labels,
            'daily' => $data,
            'cumulative' => $cumulativeData,
            'total' => array_sum($data)
        ];
    }

    /**
     * Get Recent Activities from All Modules
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = [];

        // Recent User Registrations
        $recentUsers = User::latest()->take(3)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'message' => 'New user registered: ' . ($user->name ?? 'Unknown'),
                'time' => $user->created_at ? $user->created_at->diffForHumans() : 'N/A',
                'icon' => 'person-plus'
            ];
        }

        // Recent Blog Posts
        $recentBlogs = Blog::latest()->take(3)->get();
        foreach ($recentBlogs as $blog) {
            $activities[] = [
                'type' => 'blog',
                'message' => 'New blog post: ' . ($blog->title ?? 'Untitled'),
                'time' => $blog->created_at ? $blog->created_at->diffForHumans() : 'N/A',
                'icon' => 'file-text'
            ];
        }

        // Recent Gallery Uploads
        $recentImages = GalleryImage::latest()->take(3)->get();
        foreach ($recentImages as $image) {
            $activities[] = [
                'type' => 'gallery',
                'message' => 'New image uploaded: ' . ($image->title ?? 'Untitled'),
                'time' => $image->created_at ? $image->created_at->diffForHumans() : 'N/A',
                'icon' => 'image'
            ];
        }

        // Recent Job Applications
        $recentApplications = JobApplication::with('job')->latest()->take(3)->get();
        foreach ($recentApplications as $application) {
            $activities[] = [
                'type' => 'career',
                'message' => 'New application for ' . ($application->job->title ?? 'position'),
                'time' => $application->created_at ? $application->created_at->diffForHumans() : 'N/A',
                'icon' => 'briefcase'
            ];
        }

        // Sort by time (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, $limit);
    }

    /**
     * Get Dashboard Stats for AJAX Refresh
     */
    public function getFreshStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $this->getTotalUsersWithGrowth(),
                'activity_logs' => $this->getActivityLogsWithGrowth(),
                'gallery' => $this->getGalleryWithGrowth(),
                'blogs' => $this->getBlogsWithGrowth(),
                'career' => $this->getCareerStats()
            ]
        ]);
    }
}