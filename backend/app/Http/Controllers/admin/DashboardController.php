<?php
// app/Http/Controllers/admin/DashboardController.php

namespace App\Http\Controllers\admin;

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
use App\Models\Service;  // Service model (Services nahi)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Main Dashboard API
     */
    public function index()
    {
        try {
        

            $data = [
                'stats_cards' => $this->getStatsCards(),
                'recent_activities' => $this->getRecentActivities(),
                'chart_data' => $this->getChartData(),
                //'quick_stats' => $this->getQuickStats(),
                //'sidebar_counts' => $this->getSidebarCounts()
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
     * Stats Cards
     */
    public function getStatsCards()
    {
        // Users stats
        $totalUsers = User::count();
        $currentMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $userGrowth = $this->calculateGrowth($currentMonthUsers, $lastMonthUsers);

        // Activity logs
        $todayActivity = 0;
        $yesterdayActivity = 0;
        
        if (Schema::hasTable('sessions')) {
            $todayActivity = DB::table('sessions')->whereDate('last_activity', today())->count();
            $yesterdayActivity = DB::table('sessions')->whereDate('last_activity', now()->subDay())->count();
        }
        $activityGrowth = $this->calculateGrowth($todayActivity, $yesterdayActivity);

        // Products stats
        $totalProducts = Product::count();
        $currentMonthProducts = Product::whereMonth('created_at', now()->month)->count();
        $lastMonthProducts = Product::whereMonth('created_at', now()->subMonth()->month)->count();
        $productGrowth = $this->calculateGrowth($currentMonthProducts, $lastMonthProducts);

        return [
            [
                'title' => 'Total Users',
                'value' => $totalUsers,
                'growth' => $this->formatGrowth($userGrowth),
                'subtext' => 'vs last month',
                'icon' => 'users',
                'color' => 'primary'
            ],
            [
                'title' => 'Activity Logs',
                'value' => $todayActivity,
                'growth' => $this->formatGrowth($activityGrowth),
                'subtext' => 'today',
                'icon' => 'activity',
                'color' => 'success'
            ],
            [
                'title' => 'Total Products',
                'value' => $totalProducts,
                'growth' => $this->formatGrowth($productGrowth),
                'subtext' => 'vs last month',
                'icon' => 'products',
                'color' => 'warning'
            ],
            [
                'title' => 'Active Sessions',
                'value' => Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0,
                'growth' => '+15%',
                'subtext' => 'current',
                'icon' => 'sessions',
                'color' => 'info'
            ]
        ];
    }

    /**
     * Sidebar Counts
     */
    public function getSidebarCounts()
    {
        return [
            'dashboard' => [
                'total' => 1,
                'active' => 1
            ],
            'users' => [
                'total' => User::count(),
                'new_today' => User::whereDate('created_at', today())->count()
            ],
            'products' => [
                'total' => Product::count(),
                'with_features' => ProductFeature::distinct('product_id')->count('product_id')
            ],
            'category' => [
                'total' => Category::count(),
                'used' => Product::distinct('category_id')->count('category_id')
            ],
            'product_details' => [
                'total_features' => ProductFeature::count(),
                'total_perks' => Perk::count()
            ],
            'services' => [
                'total' => Service::count(),
                'features' => ServiceFeature::count()
            ],
            'faq' => [
                'total' => Faq::count(),
                'published' => Faq::where('is_active', true)->count()
            ],
            'gallery' => [
                'total' => GalleryImage::count(),
                'recent' => GalleryImage::whereDate('created_at', today())->count()
            ],
            'blogs' => [
                'total' => Blog::count(),
                'this_month' => Blog::whereMonth('created_at', now()->month)->count()
            ],
            'career' => [
                'total_openings' => JobOpening::count(),
                'total_applications' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count()
            ]
        ];
    }

    /**
     * Recent Activities
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = [];

        // Recent Users
        $recentUsers = User::latest()->take(3)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'action' => 'New user registered',
                'details' => ($user->name ?? 'Unknown') . ' (' . ($user->email ?? 'no-email') . ')',
                'time' => $user->created_at ? $user->created_at->diffForHumans() : 'N/A',
                'icon' => 'user-plus',
                'color' => 'success'
            ];
        }

        // Recent Blogs
        $recentBlogs = Blog::latest()->take(3)->get();
        foreach ($recentBlogs as $blog) {
            $activities[] = [
                'type' => 'blog',
                'action' => 'New blog created',
                'details' => ($blog->title ?? 'Untitled'),
                'time' => $blog->created_at ? $blog->created_at->diffForHumans() : 'N/A',
                'icon' => 'file-text',
                'color' => 'primary'
            ];
        }

        // Recent Products
        $recentProducts = Product::latest()->take(3)->get();
        foreach ($recentProducts as $product) {
            $activities[] = [
                'type' => 'product',
                'action' => 'New product added',
                'details' => ($product->name ?? 'Unknown') . ' (₹' . ($product->price ?? 0) . ')',
                'time' => $product->created_at ? $product->created_at->diffForHumans() : 'N/A',
                'icon' => 'package',
                'color' => 'warning'
            ];
        }

        // Recent Job Applications
        if (class_exists('App\Models\JobApplication')) {
            $recentJobs = JobApplication::latest()->take(3)->get();
            foreach ($recentJobs as $application) {
                $activities[] = [
                    'type' => 'job',
                    'action' => 'New job application',
                    'details' => ($application->name ?? 'Unknown'),
                    'time' => $application->created_at ? $application->created_at->diffForHumans() : 'N/A',
                    'icon' => 'briefcase',
                    'color' => 'info'
                ];
            }
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, $limit);
    }

    /**
     * Chart Data
     */
    private function getChartData()
    {
        $days = 7;
        $labels = [];
        $usersData = [];
        $productsData = [];
        $blogsData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D, M d');
            
            $usersData[] = User::whereDate('created_at', $date)->count();
            $productsData[] = Product::whereDate('created_at', $date)->count();
            $blogsData[] = Blog::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $usersData,
                    'borderColor' => '#3b82f6'
                ],
                [
                    'label' => 'Products',
                    'data' => $productsData,
                    'borderColor' => '#f59e0b'
                ],
                [
                    'label' => 'Blogs',
                    'data' => $blogsData,
                    'borderColor' => '#10b981'
                ]
            ]
        ];
    }

    /**
     * Quick Stats
     */
    private function getQuickStats()
    {
        return [
            'total_revenue' => Product::sum('price') ?? 0,
            'total_orders' => 0,
            'avg_products_per_category' => $this->getAvgProductsPerCategory(),
            'completion_rate' => $this->getCompletionRate(),
            'storage_used' => $this->getStorageUsed(),
            'last_backup' => '2024-01-15',
            'php_version' => phpversion(),
            'laravel_version' => app()->version()
        ];
    }

    /**
     * Calculate Growth
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Format Growth
     */
    private function formatGrowth($percentage)
    {
        $rounded = round($percentage);
        return ($rounded > 0 ? '+' : '') . $rounded . '%';
    }

    /**
     * Average Products per Category
     */
    private function getAvgProductsPerCategory()
    {
        $categories = Category::count();
        if ($categories == 0) return 0;
        
        $products = Product::count();
        return round($products / $categories, 1);
    }

    /**
     * Completion Rate
     */
    private function getCompletionRate()
    {
        $total = User::count();
        if ($total == 0) return '0%';
        
        $completed = User::whereNotNull('email_verified_at')->count();
        return round(($completed / $total) * 100) . '%';
    }

    /**
     * Storage Used
     */
    private function getStorageUsed()
    {
        $totalSize = GalleryImage::sum('file_size') ?? 0;
        
        if ($totalSize < 1024) {
            return $totalSize . ' B';
        } elseif ($totalSize < 1048576) {
            return round($totalSize / 1024, 2) . ' KB';
        } elseif ($totalSize < 1073741824) {
            return round($totalSize / 1048576, 2) . ' MB';
        } else {
            return round($totalSize / 1073741824, 2) . ' GB';
        }
    }

    /**
     * Get Module Counts
     */
    public function getModuleCounts(Request $request)
    {
        $module = $request->module;
        
        switch($module) {
            case 'users':
                return response()->json($this->getUserStats());
            case 'products':
                return response()->json($this->getProductStats());
            case 'blogs':
                return response()->json($this->getBlogStats());
            case 'career':
                return response()->json($this->getCareerStats());
            default:
                return response()->json($this->getSidebarCounts());
        }
    }

    /**
     * User Stats
     */
    public function getUserStats()
    {
        return [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'online_now' => Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0,
            'by_role' => User::select('role', DB::raw('count(*) as total'))
                            ->groupBy('role')
                            ->get()
        ];
    }

    /**
     * Product Stats
     */
    public function getProductStats()
    {
        return [
            'total' => Product::count(),
            'with_features' => ProductFeature::distinct('product_id')->count('product_id'),
            'with_perks' => Perk::distinct('product_id')->count('product_id'),
            'categories' => Category::withCount('products')->get(),
            'price_range' => [
                'min' => Product::min('price'),
                'max' => Product::max('price'),
                'avg' => Product::avg('price')
            ]
        ];
    }

    /**
     * Blog Stats
     */
    public function getBlogStats()
    {
        return [
            'total' => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'drafts' => Blog::where('status', 'draft')->count(),
            'most_viewed' => Blog::orderBy('views', 'desc')->first(),
            'recent_comments' => 0,
            'authors' => Blog::select('user_id', DB::raw('count(*) as total'))
                            ->with('user')
                            ->groupBy('user_id')
                            ->get()
        ];
    }

    /**
     * Career Stats
     */
    public function getCareerStats()
    {
        return [
            'openings' => [
                'total' => JobOpening::count(),
                'active' => JobOpening::where('status', 'active')->count(),
                'closed' => JobOpening::where('status', 'closed')->count()
            ],
            'applications' => [
                'total' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count(),
                'reviewed' => JobApplication::where('status', 'reviewed')->count(),
                'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
                'rejected' => JobApplication::where('status', 'rejected')->count(),
                'hired' => JobApplication::where('status', 'hired')->count()
            ],
            'by_job' => JobOpening::withCount('applications')->get()
        ];
    }
}