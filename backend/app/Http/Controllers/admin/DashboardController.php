<?php
// app/Http/Controllers/admin/DashboardController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Import ALL models
use App\Models\User;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\JobOpening;
use App\Models\JobApplication;
use App\Models\PerkBenefit;
use App\Models\ProductFeature;
use App\Models\ServiceFeature;
use App\Models\Service;
use App\Models\CareerSetting;
use App\Models\Department;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\PortfolioProject;
use App\Models\WhyChooseUsPoint;
use App\Models\ProcessStep;
use App\Models\Commitment;
use App\Models\Statistic;
use App\Models\ContactInquiry;
use App\Models\NewsletterSubscriber;
use App\Models\Company;
use App\Models\SiteSetting;
use App\Models\ActivityLog;
use App\Models\EmailMessage;
use App\Models\EmailStatistic;
use App\Models\InterviewSchedule;
use App\Models\JobAlert;
use App\Models\ProductImage;
use App\Models\ProductPricingTier;
use App\Models\RelatedProduct;
use App\Models\TierFeature;
use App\Models\ApplicationStatusHistory;
use App\Models\View;
use App\Models\ViewStatistic;

class DashboardController extends Controller
{
    /**
     * Main Dashboard API - Complete Dashboard Data
     */
    public function index()
    {
        try {
            $data = [
                'stats_cards' => $this->getStatsCards(),
                'recent_activities' => $this->getRecentActivities(),
                'chart_data' => $this->getChartData(),
                'quick_stats' => $this->getQuickStats(),
                'module_counts' => $this->getSidebarCounts(),
                'system_info' => $this->getSystemInfo()
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
     * Stats Cards - All Major Models Count
     */
    public function getStatsCards()
    {
        // Users stats
        $totalUsers = User::count();
        $currentMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $userGrowth = $this->calculateGrowth($currentMonthUsers, $lastMonthUsers);

        // Products stats
        $totalProducts = Product::count();
        $currentMonthProducts = Product::whereMonth('created_at', now()->month)->count();
        $lastMonthProducts = Product::whereMonth('created_at', now()->subMonth()->month)->count();
        $productGrowth = $this->calculateGrowth($currentMonthProducts, $lastMonthProducts);

        // Blogs stats
        $totalBlogs = Blog::count();
        $currentMonthBlogs = Blog::whereMonth('created_at', now()->month)->count();
        $lastMonthBlogs = Blog::whereMonth('created_at', now()->subMonth()->month)->count();
        $blogGrowth = $this->calculateGrowth($currentMonthBlogs, $lastMonthBlogs);

        // Services stats
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();

        // Jobs stats
        $totalJobs = JobOpening::count();
        $activeJobs = JobOpening::where('is_active', true)->count();
        $totalApplications = JobApplication::count();

        // Gallery stats
        $totalImages = GalleryImage::count();

        // FAQ stats
        $totalFaqs = Faq::count();
        $activeFaqs = Faq::whereNotNull('status')->count();

        // Portfolio stats
        $totalPortfolio = PortfolioProject::count();

        // Team stats
        $totalTeam = TeamMember::count();
        $activeTeam = TeamMember::where('is_active', true)->count();

        // Testimonials stats
        $totalTestimonials = Testimonial::count();
        $activeTestimonials = Testimonial::where('is_active', true)->count();

        // Contact Inquiries stats
        $totalInquiries = ContactInquiry::count();
        $unreadInquiries = ContactInquiry::where('is_read', false)->count();

        // Newsletter stats
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('is_subscribed', true)->count();

        // Activity logs (if table exists)
        $todayActivity = 0;
        $yesterdayActivity = 0;
        if (Schema::hasTable('activity_logs')) {
            $todayActivity = ActivityLog::whereDate('created_at', today())->count();
            $yesterdayActivity = ActivityLog::whereDate('created_at', now()->subDay())->count();
        }
        $activityGrowth = $this->calculateGrowth($todayActivity, $yesterdayActivity);

        return [
            [
                'title' => 'Total Users',
                'value' => $totalUsers,
                'growth' => $this->formatGrowth($userGrowth),
                'subtext' => 'vs last month',
                'icon' => 'users',
                'color' => 'primary',
                'details' => [
                    'new_today' => User::whereDate('created_at', today())->count(),
                    'verified' => User::whereNotNull('email_verified_at')->count()
                ]
            ],
            [
                'title' => 'Products',
                'value' => $totalProducts,
                'growth' => $this->formatGrowth($productGrowth),
                'subtext' => 'total products',
                'icon' => 'package',
                'color' => 'success',
                'details' => [
                    'with_features' => ProductFeature::distinct('product_id')->count('product_id'),
                    'categories' => Product::distinct('category_id')->count('category_id')
                ]
            ],
            [
                'title' => 'Blogs',
                'value' => $totalBlogs,
                'growth' => $this->formatGrowth($blogGrowth),
                'subtext' => 'total posts',
                'icon' => 'file-text',
                'color' => 'warning',
                'details' => [
                    'published' => Blog::where('status', 'published')->count(),
                    'drafts' => Blog::where('status', 'draft')->count()
                ]
            ],
            [
                'title' => 'Services',
                'value' => $totalServices,
                'subtext' => $activeServices . ' active',
                'icon' => 'settings',
                'color' => 'info',
                'details' => [
                    'features' => ServiceFeature::count()
                ]
            ],
            [
                'title' => 'Job Openings',
                'value' => $totalJobs,
                'subtext' => $activeJobs . ' active',
                'icon' => 'briefcase',
                'color' => 'danger',
                'details' => [
                    'applications' => $totalApplications,
                    'departments' => Department::count()
                ]
            ],
            [
                'title' => 'Gallery',
                'value' => $totalImages,
                'subtext' => 'images',
                'icon' => 'image',
                'color' => 'purple',
                'details' => [
                    'categories' => GalleryImage::distinct('category_id')->count('category_id')
                ]
            ],
            [
                'title' => 'FAQ',
                'value' => $totalFaqs,
                'subtext' => $activeFaqs . ' active',
                'icon' => 'help-circle',
                'color' => 'pink',
                'details' => [
                    'inactive' => $totalFaqs - $activeFaqs
                ]
            ],
            [
                'title' => 'Portfolio',
                'value' => $totalPortfolio,
                'subtext' => 'projects',
                'icon' => 'grid',
                'color' => 'indigo',
                'details' => [
                    'categories' => PortfolioProject::distinct('category')->count()
                ]
            ],
            [
                'title' => 'Team',
                'value' => $totalTeam,
                'subtext' => $activeTeam . ' active',
                'icon' => 'users',
                'color' => 'cyan',
                'details' => [
                    'positions' => TeamMember::distinct('position')->count()
                ]
            ],
            [
                'title' => 'Testimonials',
                'value' => $totalTestimonials,
                'subtext' => $activeTestimonials . ' active',
                'icon' => 'star',
                'color' => 'amber',
                'details' => [
                    'average_rating' => Testimonial::avg('rating')
                ]
            ],
            [
                'title' => 'Inquiries',
                'value' => $totalInquiries,
                'subtext' => $unreadInquiries . ' unread',
                'icon' => 'message-circle',
                'color' => 'emerald',
                'details' => [
                    'today' => ContactInquiry::whereDate('created_at', today())->count()
                ]
            ],
            [
                'title' => 'Newsletter',
                'value' => $totalSubscribers,
                'subtext' => $activeSubscribers . ' active',
                'icon' => 'mail',
                'color' => 'rose',
                'details' => [
                    'unsubscribed' => $totalSubscribers - $activeSubscribers
                ]
            ],
            [
                'title' => 'Activity Logs',
                'value' => $todayActivity,
                'growth' => $this->formatGrowth($activityGrowth),
                'subtext' => 'today',
                'icon' => 'activity',
                'color' => 'slate'
            ]
        ];
    }

    /**
     * Sidebar/Module Counts - All Models Count
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
                'new_today' => User::whereDate('created_at', today())->count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'active' => User::where('is_active', true)->count()
            ],
            'products' => [
                'total' => Product::count(),
                'with_features' => ProductFeature::distinct('product_id')->count('product_id'),
                'with_images' => ProductImage::distinct('product_id')->count('product_id'),
                'with_pricing' => ProductPricingTier::distinct('product_id')->count('product_id'),
                'features_count' => ProductFeature::count(),
                'images_count' => ProductImage::count(),
                'pricing_tiers_count' => ProductPricingTier::count(),
                'related_count' => RelatedProduct::count()
            ],
            'categories' => [
                'total' => Category::count(),
                'used_in_products' => Product::distinct('category_id')->count('category_id')
            ],
            'services' => [
                'total' => Service::count(),
                'active' => Service::where('is_active', true)->count(),
                'features' => ServiceFeature::count(),
                'featured' => Service::where('is_featured', true)->count()
            ],
            'blogs' => [
                'total' => Blog::count(),
                'published' => Blog::where('status', 'published')->count(),
                'drafts' => Blog::where('status', 'draft')->count(),
                'this_month' => Blog::whereMonth('created_at', now()->month)->count()
            ],
            'faq' => [
                'total' => Faq::count(),
                'active' => Faq::whereNotNull('status')->count(),
                'inactive' => Faq::whereNull('status')->count()
            ],
            'gallery' => [
                'total' => GalleryImage::count(),
                'recent' => GalleryImage::whereDate('created_at', today())->count(),
                'categories' => GalleryImage::distinct('category_id')->count('category_id')
            ],
            'career' => [
                'job_openings' => [
                    'total' => JobOpening::count(),
                    'active' => JobOpening::where('is_active', true)->count(),
                    'featured' => JobOpening::where('is_featured', true)->count()
                ],
                'applications' => [
                    'total' => JobApplication::count(),
                    'pending' => JobApplication::where('status', 'pending')->count(),
                    'reviewed' => JobApplication::where('status', 'reviewed')->count(),
                    'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
                    'rejected' => JobApplication::where('status', 'rejected')->count(),
                    'hired' => JobApplication::where('status', 'hired')->count()
                ],
                'departments' => Department::count(),
                'job_categories' => JobCategory::count(),
                'locations' => Location::count(),
                'perks' => PerkBenefit::count(),
                'job_alerts' => JobAlert::count(),
                'interviews' => InterviewSchedule::count()
            ],
            'portfolio' => [
                'total' => PortfolioProject::count(),
                'featured' => PortfolioProject::where('is_featured', true)->count(),
                'categories' => PortfolioProject::distinct('category')->count('category')
            ],
            'team' => [
                'total' => TeamMember::count(),
                'active' => TeamMember::where('is_active', true)->count(),
                'positions' => TeamMember::distinct('position')->count('position')
            ],
            'testimonials' => [
                'total' => Testimonial::count(),
                'active' => Testimonial::where('is_active', true)->count(),
                'featured' => Testimonial::where('is_featured', true)->count(),
                'avg_rating' => Testimonial::avg('rating')
            ],
            'why_choose_us' => [
                'total' => WhyChooseUsPoint::count(),
                'active' => WhyChooseUsPoint::where('is_active', true)->count()
            ],
            'process_steps' => [
                'total' => ProcessStep::count(),
                'active' => ProcessStep::where('is_active', true)->count()
            ],
            'commitments' => [
                'total' => Commitment::count(),
                'active' => Commitment::where('is_active', true)->count()
            ],
            'statistics' => [
                'total' => Statistic::count(),
                'active' => Statistic::where('is_active', true)->count()
            ],
            'contact_inquiries' => [
                'total' => ContactInquiry::count(),
                'unread' => ContactInquiry::where('is_read', false)->count(),
                'today' => ContactInquiry::whereDate('created_at', today())->count()
            ],
            'newsletter' => [
                'total' => NewsletterSubscriber::count(),
                'subscribed' => NewsletterSubscriber::where('is_subscribed', true)->count(),
                'unsubscribed' => NewsletterSubscriber::where('is_subscribed', false)->count()
            ],
            'company' => [
                'total' => Company::count(),
                'active' => Company::where('is_active', true)->count()
            ],
            'settings' => [
                'total' => SiteSetting::count(),
                'career_settings' => CareerSetting::count()
            ],
            'activity_logs' => [
                'total' => ActivityLog::count(),
                'today' => ActivityLog::whereDate('created_at', today())->count()
            ],
            'emails' => [
                'total' => EmailMessage::count(),
                'sent' => EmailStatistic::sum('sent_count'),
                'opened' => EmailStatistic::sum('opens_count')
            ],
            'views' => [
                'total' => View::count(),
                'unique' => ViewStatistic::sum('unique_views')
            ]
        ];
    }

    /**
     * Recent Activities - From All Models
     */
    private function getRecentActivities($limit = 15)
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
                'details' => ($product->title ?? 'Unknown'),
                'time' => $product->created_at ? $product->created_at->diffForHumans() : 'N/A',
                'icon' => 'package',
                'color' => 'warning'
            ];
        }

        // Recent Services
        $recentServices = Service::latest()->take(2)->get();
        foreach ($recentServices as $service) {
            $activities[] = [
                'type' => 'service',
                'action' => 'New service added',
                'details' => ($service->title ?? 'Unknown'),
                'time' => $service->created_at ? $service->created_at->diffForHumans() : 'N/A',
                'icon' => 'settings',
                'color' => 'info'
            ];
        }

        // Recent Job Openings
        $recentJobs = JobOpening::latest()->take(2)->get();
        foreach ($recentJobs as $job) {
            $activities[] = [
                'type' => 'job',
                'action' => 'New job opening',
                'details' => ($job->title ?? 'Unknown'),
                'time' => $job->created_at ? $job->created_at->diffForHumans() : 'N/A',
                'icon' => 'briefcase',
                'color' => 'danger'
            ];
        }

        // Recent Job Applications
        $recentApplications = JobApplication::latest()->take(2)->get();
        foreach ($recentApplications as $application) {
            $activities[] = [
                'type' => 'application',
                'action' => 'New job application',
                'details' => ($application->name ?? 'Unknown') . ' for ' . ($application->job->title ?? 'position'),
                'time' => $application->created_at ? $application->created_at->diffForHumans() : 'N/A',
                'icon' => 'send',
                'color' => 'purple'
            ];
        }

        // Recent Gallery Images
        $recentImages = GalleryImage::latest()->take(2)->get();
        foreach ($recentImages as $image) {
            $activities[] = [
                'type' => 'gallery',
                'action' => 'New image uploaded',
                'details' => ($image->title ?? 'Untitled'),
                'time' => $image->created_at ? $image->created_at->diffForHumans() : 'N/A',
                'icon' => 'image',
                'color' => 'pink'
            ];
        }

        // Recent Portfolio Projects
        $recentPortfolio = PortfolioProject::latest()->take(2)->get();
        foreach ($recentPortfolio as $project) {
            $activities[] = [
                'type' => 'portfolio',
                'action' => 'New portfolio project',
                'details' => ($project->title ?? 'Untitled'),
                'time' => $project->created_at ? $project->created_at->diffForHumans() : 'N/A',
                'icon' => 'grid',
                'color' => 'indigo'
            ];
        }

        // Recent Team Members
        $recentTeam = TeamMember::latest()->take(2)->get();
        foreach ($recentTeam as $member) {
            $activities[] = [
                'type' => 'team',
                'action' => 'New team member added',
                'details' => ($member->name ?? 'Unknown') . ' as ' . ($member->position ?? 'member'),
                'time' => $member->created_at ? $member->created_at->diffForHumans() : 'N/A',
                'icon' => 'users',
                'color' => 'cyan'
            ];
        }

        // Recent Testimonials
        $recentTestimonials = Testimonial::latest()->take(2)->get();
        foreach ($recentTestimonials as $testimonial) {
            $activities[] = [
                'type' => 'testimonial',
                'action' => 'New testimonial added',
                'details' => ($testimonial->author_name ?? 'Anonymous'),
                'time' => $testimonial->created_at ? $testimonial->created_at->diffForHumans() : 'N/A',
                'icon' => 'star',
                'color' => 'amber'
            ];
        }

        // Recent Contact Inquiries
        $recentInquiries = ContactInquiry::latest()->take(2)->get();
        foreach ($recentInquiries as $inquiry) {
            $activities[] = [
                'type' => 'inquiry',
                'action' => 'New contact inquiry',
                'details' => ($inquiry->name ?? 'Unknown') . ': ' . substr($inquiry->message ?? '', 0, 30) . '...',
                'time' => $inquiry->created_at ? $inquiry->created_at->diffForHumans() : 'N/A',
                'icon' => 'message-circle',
                'color' => 'emerald'
            ];
        }

        // Recent Newsletter Subscribers
        $recentSubscribers = NewsletterSubscriber::latest()->take(2)->get();
        foreach ($recentSubscribers as $subscriber) {
            $activities[] = [
                'type' => 'newsletter',
                'action' => 'New newsletter subscriber',
                'details' => ($subscriber->email ?? 'Unknown'),
                'time' => $subscriber->created_at ? $subscriber->created_at->diffForHumans() : 'N/A',
                'icon' => 'mail',
                'color' => 'rose'
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, $limit);
    }

    /**
     * Chart Data - For All Models
     */
    private function getChartData($days = 7)
    {
        $labels = [];
        $usersData = [];
        $productsData = [];
        $blogsData = [];
        $servicesData = [];
        $jobsData = [];
        $applicationsData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D, M d');
            
            $usersData[] = User::whereDate('created_at', $date)->count();
            $productsData[] = Product::whereDate('created_at', $date)->count();
            $blogsData[] = Blog::whereDate('created_at', $date)->count();
            $servicesData[] = Service::whereDate('created_at', $date)->count();
            $jobsData[] = JobOpening::whereDate('created_at', $date)->count();
            $applicationsData[] = JobApplication::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $usersData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)'
                ],
                [
                    'label' => 'Products',
                    'data' => $productsData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)'
                ],
                [
                    'label' => 'Blogs',
                    'data' => $blogsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)'
                ],
                [
                    'label' => 'Services',
                    'data' => $servicesData,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)'
                ],
                [
                    'label' => 'Job Openings',
                    'data' => $jobsData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)'
                ],
                [
                    'label' => 'Applications',
                    'data' => $applicationsData,
                    'borderColor' => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.1)'
                ]
            ]
        ];
    }

    /**
     * Quick Stats - System Overview
     */
    private function getQuickStats()
    {
        return [
            'database' => [
                'total_tables' => $this->getTableCount(),
                'total_records' => $this->getTotalRecords(),
                'mysql_version' => DB::select('select version() as version')[0]->version ?? 'Unknown'
            ],
            'system' => [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug')
            ],
            'storage' => [
                'images_count' => GalleryImage::count(),
                'total_size' => $this->getStorageUsed(),
                'upload_dir' => storage_path('app/public')
            ],
            'performance' => [
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'queue_connection' => config('queue.default')
            ]
        ];
    }

    /**
     * System Information
     */
    private function getSystemInfo()
    {
        return [
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
            'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time())
        ];
    }

    /**
     * Get Total Tables Count
     */
    private function getTableCount()
    {
        $tables = DB::select('SHOW TABLES');
        return count($tables);
    }

    /**
     * Get Total Records Across All Tables
     */
    private function getTotalRecords()
    {
        $total = 0;
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . env('DB_DATABASE');
        
        foreach ($tables as $table) {
            $tableName = $table->$key;
            $count = DB::table($tableName)->count();
            $total += $count;
        }
        
        return $total;
    }

    /**
     * Get Storage Used
     */
    private function getStorageUsed()
    {
        $totalSize = 0;
        
        // Gallery Images
        if (Schema::hasColumn('gallery_images', 'file_size')) {
            $totalSize += GalleryImage::sum('file_size') ?? 0;
        }
        
        // Add other file size calculations as needed
        
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
     * Calculate Growth Percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Format Growth with + sign
     */
    private function formatGrowth($percentage)
    {
        $rounded = round($percentage);
        return ($rounded > 0 ? '+' : '') . $rounded . '%';
    }

    /**
     * Get Module Specific Stats
     */
    public function getModuleCounts(Request $request)
    {
        $module = $request->module;
        
        switch($module) {
            case 'users':
                return response()->json($this->getUserStats());
            case 'products':
                return response()->json($this->getProductStats());
            case 'services':
                return response()->json($this->getServiceStats());
            case 'blogs':
                return response()->json($this->getBlogStats());
            case 'career':
                return response()->json($this->getCareerStats());
            case 'gallery':
                return response()->json($this->getGalleryStats());
            case 'portfolio':
                return response()->json($this->getPortfolioStats());
            case 'team':
                return response()->json($this->getTeamStats());
            case 'testimonials':
                return response()->json($this->getTestimonialStats());
            case 'inquiries':
                return response()->json($this->getInquiryStats());
            case 'newsletter':
                return response()->json($this->getNewsletterStats());
            default:
                return response()->json($this->getSidebarCounts());
        }
    }

    /**
     * User Detailed Stats
     */
    private function getUserStats()
    {
        return [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'this_year' => User::whereYear('created_at', now()->year)->count(),
            'online_now' => Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0,
            'by_role' => User::select('role', DB::raw('count(*) as total'))
                            ->groupBy('role')
                            ->get()
        ];
    }

    /**
     * Product Detailed Stats
     */
    private function getProductStats()
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'with_features' => ProductFeature::distinct('product_id')->count('product_id'),
            'with_images' => ProductImage::distinct('product_id')->count('product_id'),
            'with_pricing' => ProductPricingTier::distinct('product_id')->count('product_id'),
            'categories' => Category::withCount('products')->get(),
            'price_range' => [
                'min_usd' => Product::min('price_usd'),
                'max_usd' => Product::max('price_usd'),
                'avg_usd' => Product::avg('price_usd'),
                'min_inr' => Product::min('price_inr'),
                'max_inr' => Product::max('price_inr'),
                'avg_inr' => Product::avg('price_inr')
            ],
            'features_count' => ProductFeature::count(),
            'images_count' => ProductImage::count(),
            'pricing_tiers_count' => ProductPricingTier::count(),
            'related_count' => RelatedProduct::count()
        ];
    }

    /**
     * Service Detailed Stats
     */
    private function getServiceStats()
    {
        return [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'inactive' => Service::where('is_active', false)->count(),
            'featured' => Service::where('is_featured', true)->count(),
            'features_count' => ServiceFeature::count(),
            'by_company' => Service::select('company_id', DB::raw('count(*) as total'))
                                ->with('company')
                                ->groupBy('company_id')
                                ->get()
        ];
    }

    /**
     * Blog Detailed Stats
     */
    private function getBlogStats()
    {
        return [
            'total' => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'drafts' => Blog::where('status', 'draft')->count(),
            'by_category' => Blog::select('category_id', DB::raw('count(*) as total'))
                                ->with('category')
                                ->groupBy('category_id')
                                ->get(),
            'by_author' => Blog::select('author_id', DB::raw('count(*) as total'))
                              ->with('author')
                              ->groupBy('author_id')
                              ->get(),
            'most_viewed' => Blog::orderBy('views', 'desc')->first(),
            'recent_comments' => 0
        ];
    }

    /**
     * Career Detailed Stats
     */
    private function getCareerStats()
    {
        return [
            'openings' => [
                'total' => JobOpening::count(),
                'active' => JobOpening::where('is_active', true)->count(),
                'inactive' => JobOpening::where('is_active', false)->count(),
                'featured' => JobOpening::where('is_featured', true)->count(),
                'by_department' => JobOpening::select('department_id', DB::raw('count(*) as total'))
                                    ->with('department')
                                    ->groupBy('department_id')
                                    ->get(),
                'by_category' => JobOpening::select('category_id', DB::raw('count(*) as total'))
                                  ->with('category')
                                  ->groupBy('category_id')
                                  ->get(),
                'by_location' => JobOpening::select('location_id', DB::raw('count(*) as total'))
                                 ->with('location')
                                 ->groupBy('location_id')
                                 ->get()
            ],
            'applications' => [
                'total' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count(),
                'reviewed' => JobApplication::where('status', 'reviewed')->count(),
                'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
                'rejected' => JobApplication::where('status', 'rejected')->count(),
                'hired' => JobApplication::where('status', 'hired')->count(),
                'by_job' => JobOpening::withCount('applications')->get()
            ],
            'departments' => Department::withCount('jobOpenings')->get(),
            'job_categories' => JobCategory::withCount('jobOpenings')->get(),
            'locations' => Location::withCount('jobOpenings')->get(),
            'perks' => PerkBenefit::count(),
            'job_alerts' => JobAlert::count(),
            'interviews' => [
                'total' => InterviewSchedule::count(),
                'scheduled' => InterviewSchedule::where('status', 'scheduled')->count(),
                'completed' => InterviewSchedule::where('status', 'completed')->count(),
                'cancelled' => InterviewSchedule::where('status', 'cancelled')->count()
            ]
        ];
    }

    /**
     * Gallery Detailed Stats
     */
    private function getGalleryStats()
    {
        return [
            'total' => GalleryImage::count(),
            'by_category' => GalleryImage::select('category_id', DB::raw('count(*) as total'))
                                ->with('category')
                                ->groupBy('category_id')
                                ->get(),
            'recent' => GalleryImage::whereDate('created_at', today())->count(),
            'this_week' => GalleryImage::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => GalleryImage::whereMonth('created_at', now()->month)->count()
        ];
    }

    /**
     * Portfolio Detailed Stats
     */
    private function getPortfolioStats()
    {
        return [
            'total' => PortfolioProject::count(),
            'featured' => PortfolioProject::where('is_featured', true)->count(),
            'active' => PortfolioProject::where('is_active', true)->count(),
            'by_category' => PortfolioProject::select('category', DB::raw('count(*) as total'))
                                ->groupBy('category')
                                ->get(),
            'by_year' => PortfolioProject::select(DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as total'))
                            ->groupBy('year')
                            ->orderBy('year', 'desc')
                            ->get()
        ];
    }

    /**
     * Team Detailed Stats
     */
    private function getTeamStats()
    {
        return [
            'total' => TeamMember::count(),
            'active' => TeamMember::where('is_active', true)->count(),
            'inactive' => TeamMember::where('is_active', false)->count(),
            'by_position' => TeamMember::select('position', DB::raw('count(*) as total'))
                                ->groupBy('position')
                                ->get(),
            'by_department' => TeamMember::select('department', DB::raw('count(*) as total'))
                                  ->groupBy('department')
                                  ->get()
        ];
    }

    /**
     * Testimonial Detailed Stats
     */
    private function getTestimonialStats()
    {
        return [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'inactive' => Testimonial::where('is_active', false)->count(),
            'featured' => Testimonial::where('is_featured', true)->count(),
            'avg_rating' => Testimonial::avg('rating'),
            'rating_distribution' => Testimonial::select('rating', DB::raw('count(*) as total'))
                                    ->groupBy('rating')
                                    ->orderBy('rating', 'desc')
                                    ->get()
        ];
    }

    /**
     * Inquiry Detailed Stats
     */
    private function getInquiryStats()
    {
        return [
            'total' => ContactInquiry::count(),
            'read' => ContactInquiry::where('is_read', true)->count(),
            'unread' => ContactInquiry::where('is_read', false)->count(),
            'replied' => ContactInquiry::where('is_replied', true)->count(),
            'pending' => ContactInquiry::where('is_read', false)->count(),
            'today' => ContactInquiry::whereDate('created_at', today())->count(),
            'this_week' => ContactInquiry::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ContactInquiry::whereMonth('created_at', now()->month)->count()
        ];
    }

    /**
     * Newsletter Detailed Stats
     */
    private function getNewsletterStats()
    {
        return [
            'total' => NewsletterSubscriber::count(),
            'subscribed' => NewsletterSubscriber::where('is_subscribed', true)->count(),
            'unsubscribed' => NewsletterSubscriber::where('is_subscribed', false)->count(),
            'today' => NewsletterSubscriber::whereDate('created_at', today())->count(),
            'this_week' => NewsletterSubscriber::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => NewsletterSubscriber::whereMonth('created_at', now()->month)->count()
        ];
    }
}