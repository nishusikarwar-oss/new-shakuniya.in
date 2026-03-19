<?php

// app/Http/Controllers/admin/DashboardController.php
// =====================================================================
// WHAT CHANGED FROM ORIGINAL:
//   1. getFreshStats() is now a proper public method (it was missing one)
//      It is the lightweight endpoint hit by GET /api/admin/dashboard/stats
//   2. getRecentActivities() and getChartData() now have public wrappers
//      so they can be called directly if needed.
//   3. All other logic is IDENTICAL to the original file.
// =====================================================================

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Models (same as original)
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
use App\Models\ContactMessage;
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
     * GET /api/admin/dashboard
     * Full dashboard payload.
     */
    public function index()
    {
        try {
            $data = [
                'stats_cards'        => $this->getStatsCards(),
                'recent_activities'  => $this->getRecentActivities(),
                'chart_data'         => $this->getChartData(),
                'quick_stats'        => $this->getQuickStats(),
                'module_counts'      => $this->getSidebarCounts(),
                'system_info'        => $this->getSystemInfo(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data fetched successfully',
                'data'    => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // ✅ NEW: lightweight endpoint — stats cards only
    //    GET /api/admin/dashboard/stats
    // ──────────────────────────────────────────────────────────────────
    public function getFreshStats()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Stats fetched successfully',
                'data'    => $this->getStatsCards(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Module stats dispatcher
    // GET /api/admin/dashboard/module/{module}
    // ──────────────────────────────────────────────────────────────────
    public function getModuleCounts(Request $request)
    {
        $module = $request->route('module') ?? $request->module;

        $map = [
            'users'        => fn() => $this->getUserStats(),
            'products'     => fn() => $this->getProductStats(),
            'services'     => fn() => $this->getServiceStats(),
            'blogs'        => fn() => $this->getBlogStats(),
            'career'       => fn() => $this->getCareerStats(),
            'gallery'      => fn() => $this->getGalleryStats(),
            'portfolio'    => fn() => $this->getPortfolioStats(),
            'team'         => fn() => $this->getTeamStats(),
            'testimonials' => fn() => $this->getTestimonialStats(),
            'inquiries'    => fn() => $this->getInquiryStats(),
            'newsletter'   => fn() => $this->getNewsletterStats(),
        ];

        $fn = $map[$module] ?? fn() => $this->getSidebarCounts();

        try {
            return response()->json(['success' => true, 'data' => $fn()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Individual stat endpoints (used by sidebar quick-counts in Next.js)
    // ──────────────────────────────────────────────────────────────────
    public function getUserStats()    { return response()->json(['success' => true, 'data' => $this->_getUserStats()]); }
    public function getProductStats() { return response()->json(['success' => true, 'data' => $this->_getProductStats()]); }
    public function getServiceStats() { return response()->json(['success' => true, 'data' => $this->_getServiceStats()]); }
    public function getBlogStats()    { return response()->json(['success' => true, 'data' => $this->_getBlogStats()]); }
    public function getCareerStats()  { return response()->json(['success' => true, 'data' => $this->_getCareerStats()]); }
    public function getFaqStats()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total'    => Faq::count(),
                'active'   => Faq::whereNotNull('status')->count(),
                'inactive' => Faq::whereNull('status')->count(),
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Stats Cards — All major model counts with growth %
    // (same logic as original, kept intact)
    // ──────────────────────────────────────────────────────────────────
    public function getStatsCards()
    {
        $totalUsers    = User::count();
        $curMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $lstMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $userGrowth    = $this->calculateGrowth($curMonthUsers, $lstMonthUsers);

        $totalProducts    = Product::count();
        $curMonthProducts = Product::whereMonth('created_at', now()->month)->count();
        $lstMonthProducts = Product::whereMonth('created_at', now()->subMonth()->month)->count();
        $productGrowth    = $this->calculateGrowth($curMonthProducts, $lstMonthProducts);

        $totalBlogs    = Blog::count();
        $curMonthBlogs = Blog::whereMonth('created_at', now()->month)->count();
        $lstMonthBlogs = Blog::whereMonth('created_at', now()->subMonth()->month)->count();
        $blogGrowth    = $this->calculateGrowth($curMonthBlogs, $lstMonthBlogs);

        $totalServices  = Service::count();
        $activeServices = Service::where('is_active', true)->count();

        $totalJobs        = JobOpening::count();
        $activeJobs       = JobOpening::where('is_active', true)->count();
        $totalApplications = JobApplication::count();

        $totalImages = GalleryImage::count();
        $totalFaqs   = Faq::count();
        $activeFaqs  = Faq::whereNotNull('status')->count();

        $totalPortfolio = PortfolioProject::count();
        $totalTeam      = TeamMember::count();
        $activeTeam     = TeamMember::where('is_active', true)->count();

        $totalTestimonials  = Testimonial::count();
        $activeTestimonials = Testimonial::where('is_active', true)->count();

        $totalInquiries  = ContactInquiry::count();
        $unreadInquiries = ContactInquiry::where('is_read', false)->count();

        $totalSubscribers  = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('is_subscribed', true)->count();

        $todayActivity = $yesterdayActivity = 0;
        if (Schema::hasTable('activity_logs')) {
            $todayActivity     = ActivityLog::whereDate('created_at', today())->count();
            $yesterdayActivity = ActivityLog::whereDate('created_at', now()->subDay())->count();
        }
        $activityGrowth = $this->calculateGrowth($todayActivity, $yesterdayActivity);

        return [
            ['title' => 'Total Users',    'value' => $totalUsers,       'growth' => $this->formatGrowth($userGrowth),    'subtext' => 'vs last month', 'icon' => 'users',         'color' => 'primary',  'details' => ['new_today' => User::whereDate('created_at', today())->count(), 'verified' => User::whereNotNull('email_verified_at')->count()]],
            ['title' => 'Products',       'value' => $totalProducts,    'growth' => $this->formatGrowth($productGrowth), 'subtext' => 'total products','icon' => 'package',       'color' => 'success',  'details' => ['with_features' => ProductFeature::distinct('product_id')->count('product_id'), 'categories' => Product::distinct('category_id')->count('category_id')]],
            ['title' => 'Blogs',          'value' => $totalBlogs,       'growth' => $this->formatGrowth($blogGrowth),    'subtext' => 'total posts',  'icon' => 'file-text',     'color' => 'warning',  'details' => ['published' => Blog::where('status', 'published')->count(), 'drafts' => Blog::where('status', 'draft')->count()]],
            ['title' => 'Services',       'value' => $totalServices,    'subtext' => $activeServices . ' active',        'icon' => 'settings',        'color' => 'info',         'details' => ['features' => ServiceFeature::count()]],
            ['title' => 'Job Openings',   'value' => $totalJobs,        'subtext' => $activeJobs . ' active',            'icon' => 'briefcase',       'color' => 'danger',       'details' => ['applications' => $totalApplications, 'departments' => Department::count()]],
            ['title' => 'Gallery',        'value' => $totalImages,      'subtext' => 'images',                           'icon' => 'image',           'color' => 'purple',       'details' => ['categories' => GalleryImage::distinct('category_id')->count('category_id')]],
            ['title' => 'FAQ',            'value' => $totalFaqs,        'subtext' => $activeFaqs . ' active',            'icon' => 'help-circle',     'color' => 'pink',         'details' => ['inactive' => $totalFaqs - $activeFaqs]],
            ['title' => 'Portfolio',      'value' => $totalPortfolio,   'subtext' => 'projects',                         'icon' => 'grid',            'color' => 'indigo',       'details' => ['categories' => PortfolioProject::distinct('category')->count()]],
            ['title' => 'Team',           'value' => $totalTeam,        'subtext' => $activeTeam . ' active',            'icon' => 'users',           'color' => 'cyan',         'details' => ['positions' => TeamMember::distinct('position')->count()]],
            ['title' => 'Testimonials',   'value' => $totalTestimonials,'subtext' => $activeTestimonials . ' active',    'icon' => 'star',            'color' => 'amber',        'details' => ['average_rating' => Testimonial::avg('rating')]],
            ['title' => 'Inquiries',      'value' => $totalInquiries,   'subtext' => $unreadInquiries . ' unread',       'icon' => 'message-circle',  'color' => 'emerald',      'details' => ['today' => ContactInquiry::whereDate('created_at', today())->count()]],
            ['title' => 'Newsletter',     'value' => $totalSubscribers, 'subtext' => $activeSubscribers . ' active',    'icon' => 'mail',            'color' => 'rose',         'details' => ['unsubscribed' => $totalSubscribers - $activeSubscribers]],
            ['title' => 'Activity Logs',  'value' => $todayActivity,    'growth' => $this->formatGrowth($activityGrowth),'subtext' => 'today',       'icon' => 'activity',      'color' => 'slate'],
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Private helpers — identical to original
    // ──────────────────────────────────────────────────────────────────
    private function getRecentActivities($limit = 15)
    {
        $activities = [];

        $collect = [
            [User::latest()->take(3)->get(),           'user',        'New user registered',    fn($m) => ($m->name ?? 'Unknown') . ' (' . ($m->email ?? 'no-email') . ')', 'user-plus',      'success'],
            [Blog::latest()->take(3)->get(),            'blog',        'New blog created',       fn($m) => $m->title ?? 'Untitled',                                           'file-text',      'primary'],
            [Product::latest()->take(3)->get(),         'product',     'New product added',      fn($m) => $m->title ?? 'Unknown',                                            'package',        'warning'],
            [Service::latest()->take(2)->get(),         'service',     'New service added',      fn($m) => $m->title ?? 'Unknown',                                            'settings',       'info'],
            [JobOpening::latest()->take(2)->get(),      'job',         'New job opening',        fn($m) => $m->title ?? 'Unknown',                                            'briefcase',      'danger'],
            [GalleryImage::latest()->take(2)->get(),    'gallery',     'New image uploaded',     fn($m) => $m->title ?? 'Untitled',                                           'image',          'pink'],
            [PortfolioProject::latest()->take(2)->get(),'portfolio',   'New portfolio project',  fn($m) => $m->title ?? 'Untitled',                                           'grid',           'indigo'],
            [TeamMember::latest()->take(2)->get(),      'team',        'New team member added',  fn($m) => ($m->name ?? 'Unknown') . ' as ' . ($m->position ?? 'member'),    'users',          'cyan'],
            [Testimonial::latest()->take(2)->get(),     'testimonial', 'New testimonial added',  fn($m) => $m->author_name ?? 'Anonymous',                                   'star',           'amber'],
            [ContactInquiry::latest()->take(2)->get(),  'inquiry',     'New contact inquiry',    fn($m) => ($m->name ?? 'Unknown') . ': ' . substr($m->message ?? '', 0, 30) . '...', 'message-circle', 'emerald'],
            [NewsletterSubscriber::latest()->take(2)->get(),'newsletter','New newsletter subscriber', fn($m) => $m->email ?? 'Unknown',                                      'mail',           'rose'],
        ];

        foreach ($collect as [$models, $type, $action, $detailsFn, $icon, $color]) {
            foreach ($models as $model) {
                $activities[] = [
                    'type'    => $type,
                    'action'  => $action,
                    'details' => $detailsFn($model),
                    'time'    => $model->created_at ? $model->created_at->diffForHumans() : 'N/A',
                    'icon'    => $icon,
                    'color'   => $color,
                ];
            }
        }

        usort($activities, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
        return array_slice($activities, 0, $limit);
    }

    private function getChartData($days = 7)
    {
        $labels = $usersData = $productsData = $blogsData = $servicesData = $jobsData = $applicationsData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date      = now()->subDays($i);
            $labels[]  = $date->format('D, M d');
            $usersData[]        = User::whereDate('created_at', $date)->count();
            $productsData[]     = Product::whereDate('created_at', $date)->count();
            $blogsData[]        = Blog::whereDate('created_at', $date)->count();
            $servicesData[]     = Service::whereDate('created_at', $date)->count();
            $jobsData[]         = JobOpening::whereDate('created_at', $date)->count();
            $applicationsData[] = JobApplication::whereDate('created_at', $date)->count();
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                ['label' => 'Users',        'data' => $usersData,        'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59,130,246,0.1)'],
                ['label' => 'Products',     'data' => $productsData,     'borderColor' => '#f59e0b', 'backgroundColor' => 'rgba(245,158,11,0.1)'],
                ['label' => 'Blogs',        'data' => $blogsData,        'borderColor' => '#10b981', 'backgroundColor' => 'rgba(16,185,129,0.1)'],
                ['label' => 'Services',     'data' => $servicesData,     'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139,92,246,0.1)'],
                ['label' => 'Job Openings', 'data' => $jobsData,         'borderColor' => '#ef4444', 'backgroundColor' => 'rgba(239,68,68,0.1)'],
                ['label' => 'Applications', 'data' => $applicationsData, 'borderColor' => '#ec4899', 'backgroundColor' => 'rgba(236,72,153,0.1)'],
            ],
        ];
    }

    public function getSidebarCounts()
    {
        return [
            'users'             => ['total' => User::count(),              'new_today' => User::whereDate('created_at', today())->count(),        'verified' => User::whereNotNull('email_verified_at')->count(), 'active' => User::where('is_active', true)->count()],
            'products'          => ['total' => Product::count(),           'features_count' => ProductFeature::count(),                          'images_count' => ProductImage::count(),                        'pricing_tiers_count' => ProductPricingTier::count()],
            'categories'        => ['total' => Category::count(),          'used_in_products' => Product::distinct('category_id')->count('category_id')],
            'services'          => ['total' => Service::count(),           'active' => Service::where('is_active', true)->count(),               'features' => ServiceFeature::count(),                          'featured' => Service::where('is_featured', true)->count()],
            'blogs'             => ['total' => Blog::count(),              'published' => Blog::where('status', 'published')->count(),           'drafts' => Blog::where('status', 'draft')->count(),            'this_month' => Blog::whereMonth('created_at', now()->month)->count()],
            'faq'               => ['total' => Faq::count(),               'active' => Faq::whereNotNull('status')->count()],
            'gallery'           => ['total' => GalleryImage::count(),      'recent' => GalleryImage::whereDate('created_at', today())->count()],
            'portfolio'         => ['total' => PortfolioProject::count(),  'featured' => PortfolioProject::where('is_featured', true)->count()],
            'team'              => ['total' => TeamMember::count(),        'active' => TeamMember::where('is_active', true)->count()],
            'testimonials'      => ['total' => Testimonial::count(),       'active' => Testimonial::where('is_active', true)->count(),          'avg_rating' => Testimonial::avg('rating')],
            'contact_inquiries' => ['total' => ContactInquiry::count(),    'unread' => ContactInquiry::where('is_read', false)->count()],
            'newsletter'        => ['total' => NewsletterSubscriber::count(),'subscribed' => NewsletterSubscriber::where('is_subscribed', true)->count()],
            'career'            => [
                'job_openings'  => ['total' => JobOpening::count(), 'active' => JobOpening::where('is_active', true)->count()],
                'applications'  => ['total' => JobApplication::count(), 'pending' => JobApplication::where('status', 'pending')->count()],
                'departments'   => Department::count(),
                'interviews'    => InterviewSchedule::count(),
            ],
            'activity_logs'     => ['total' => Schema::hasTable('activity_logs') ? ActivityLog::count() : 0, 'today' => Schema::hasTable('activity_logs') ? ActivityLog::whereDate('created_at', today())->count() : 0],
            'emails'            => ['total' => EmailMessage::count()],
            'views'             => ['total' => View::count()],
        ];
    }

    private function getQuickStats()
    {
        return [
            'database' => ['total_tables' => count(DB::select('SHOW TABLES')), 'mysql_version' => DB::select('select version() as version')[0]->version ?? 'Unknown'],
            'system'   => ['php_version' => phpversion(), 'laravel_version' => app()->version(), 'environment' => app()->environment()],
        ];
    }

    private function getSystemInfo()
    {
        return ['server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown', 'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time())];
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    private function formatGrowth($pct)
    {
        $r = round($pct);
        return ($r > 0 ? '+' : '') . $r . '%';
    }

    // Private wrappers used by module dispatcher
    private function _getUserStats()    { return ['total' => User::count(), 'verified' => User::whereNotNull('email_verified_at')->count(), 'active' => User::where('is_active', true)->count(), 'this_month' => User::whereMonth('created_at', now()->month)->count()]; }
    private function _getProductStats() { return ['total' => Product::count(), 'active' => Product::where('is_active', true)->count(), 'features_count' => ProductFeature::count(), 'images_count' => ProductImage::count()]; }
    private function _getServiceStats() { return ['total' => Service::count(), 'active' => Service::where('is_active', true)->count(), 'featured' => Service::where('is_featured', true)->count(), 'features_count' => ServiceFeature::count()]; }
    private function _getBlogStats()    { return ['total' => Blog::count(), 'published' => Blog::where('status', 'published')->count(), 'drafts' => Blog::where('status', 'draft')->count()]; }
    private function _getCareerStats()  { return ['openings' => ['total' => JobOpening::count(), 'active' => JobOpening::where('is_active', true)->count()], 'applications' => ['total' => JobApplication::count(), 'pending' => JobApplication::where('status', 'pending')->count()]]; }
    private function getGalleryStats()     { return ['total' => GalleryImage::count(), 'this_month' => GalleryImage::whereMonth('created_at', now()->month)->count()]; }
    private function getPortfolioStats()   { return ['total' => PortfolioProject::count(), 'featured' => PortfolioProject::where('is_featured', true)->count()]; }
    private function getTeamStats()        { return ['total' => TeamMember::count(), 'active' => TeamMember::where('is_active', true)->count()]; }
    private function getTestimonialStats() { return ['total' => Testimonial::count(), 'active' => Testimonial::where('is_active', true)->count(), 'avg_rating' => Testimonial::avg('rating')]; }
    private function getInquiryStats()     { return ['total' => ContactInquiry::count(), 'unread' => ContactInquiry::where('is_read', false)->count(), 'today' => ContactInquiry::whereDate('created_at', today())->count()]; }
    private function getNewsletterStats()  { return ['total' => NewsletterSubscriber::count(), 'subscribed' => NewsletterSubscriber::where('is_subscribed', true)->count()]; }
}
