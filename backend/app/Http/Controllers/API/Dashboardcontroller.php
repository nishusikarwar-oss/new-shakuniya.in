<?php
// ✅ FIX: Changed namespace from admin to API to match route imports
namespace App\Http\Controllers\API;

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
     * Main Dashboard API - Complete Dashboard Data
     * GET /api/admin/dashboard
     */
    public function index()
    {
        try {
            $data = [
                'stats_cards'       => $this->getStatsCards(),
                'recent_activities' => $this->getRecentActivities(),
                'chart_data'        => $this->getChartData(),
                'quick_stats'       => $this->getQuickStats(),
                'module_counts'     => $this->getSidebarCounts(),
                'system_info'       => $this->getSystemInfo(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data fetched successfully',
                'data'    => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✅ FIX: Added missing getFreshStats method (was referenced in routes but didn't exist)
     * GET /api/admin/dashboard/stats
     */
    public function getFreshStats()
    {
        try {
            return response()->json([
                'success' => true,
                'data'    => $this->getStatsCards(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Module Specific Stats
     * GET /api/admin/dashboard/module/{module}
     */
    public function getModuleCounts(Request $request)
    {
        $module = $request->module ?? $request->route('module');

        try {
            switch ($module) {
                case 'users':        return response()->json(['success' => true, 'data' => $this->getUserStats()]);
                case 'products':     return response()->json(['success' => true, 'data' => $this->getProductStats()]);
                case 'services':     return response()->json(['success' => true, 'data' => $this->getServiceStats()]);
                case 'blogs':        return response()->json(['success' => true, 'data' => $this->getBlogStats()]);
                case 'career':       return response()->json(['success' => true, 'data' => $this->getCareerStats()]);
                case 'gallery':      return response()->json(['success' => true, 'data' => $this->getGalleryStats()]);
                case 'portfolio':    return response()->json(['success' => true, 'data' => $this->getPortfolioStats()]);
                case 'team':         return response()->json(['success' => true, 'data' => $this->getTeamStats()]);
                case 'testimonials': return response()->json(['success' => true, 'data' => $this->getTestimonialStats()]);
                case 'inquiries':    return response()->json(['success' => true, 'data' => $this->getInquiryStats()]);
                case 'newsletter':   return response()->json(['success' => true, 'data' => $this->getNewsletterStats()]);
                default:             return response()->json(['success' => true, 'data' => $this->getSidebarCounts()]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Stats Cards
     */
    private function getStatsCards()
    {
        $totalUsers        = $this->safeCount(fn() => User::count());
        $currentMonthUsers = $this->safeCount(fn() => User::whereMonth('created_at', now()->month)->count());
        $lastMonthUsers    = $this->safeCount(fn() => User::whereMonth('created_at', now()->subMonth()->month)->count());

        $totalProducts        = $this->safeCount(fn() => Product::count());
        $currentMonthProducts = $this->safeCount(fn() => Product::whereMonth('created_at', now()->month)->count());
        $lastMonthProducts    = $this->safeCount(fn() => Product::whereMonth('created_at', now()->subMonth()->month)->count());

        $totalBlogs        = $this->safeCount(fn() => Blog::count());
        $currentMonthBlogs = $this->safeCount(fn() => Blog::whereMonth('created_at', now()->month)->count());
        $lastMonthBlogs    = $this->safeCount(fn() => Blog::whereMonth('created_at', now()->subMonth()->month)->count());

        $totalServices  = $this->safeCount(fn() => Service::count());
        $activeServices = $this->safeCount(fn() => Service::where('is_active', true)->count());
        $totalJobs      = $this->safeCount(fn() => JobOpening::count());
        $activeJobs     = $this->safeCount(fn() => JobOpening::where('is_active', true)->count());
        $totalApplications = $this->safeCount(fn() => JobApplication::count());
        $totalImages    = $this->safeCount(fn() => GalleryImage::count());
        $totalFaqs      = $this->safeCount(fn() => Faq::count());
        $totalPortfolio = $this->safeCount(fn() => PortfolioProject::count());
        $totalTeam      = $this->safeCount(fn() => TeamMember::count());
        $activeTeam     = $this->safeCount(fn() => TeamMember::where('is_active', true)->count());
        $totalTestimonials  = $this->safeCount(fn() => Testimonial::count());
        $totalInquiries     = $this->safeCount(fn() => ContactInquiry::count());
        $unreadInquiries    = $this->safeCount(fn() => ContactInquiry::where('is_read', false)->count());
        $totalSubscribers   = $this->safeCount(fn() => NewsletterSubscriber::count());
        $activeSubscribers  = $this->safeCount(fn() => NewsletterSubscriber::where('is_subscribed', true)->count());

        // Contact Messages
        $totalMessages  = $this->safeCount(fn() => ContactMessage::count());
        $unreadMessages = $this->safeCount(fn() => ContactMessage::where('status', 'unread')->count());

        $todayActivity = $this->safeCount(fn() =>
            Schema::hasTable('activity_logs') ? ActivityLog::whereDate('created_at', today())->count() : 0
        );

        return [
            [
                'title'   => 'Total Users',
                'value'   => $totalUsers,
                'growth'  => $this->formatGrowth($this->calculateGrowth($currentMonthUsers, $lastMonthUsers)),
                'subtext' => 'vs last month',
                'icon'    => 'users',
                'color'   => 'primary',
                'details' => [
                    'new_today' => $this->safeCount(fn() => User::whereDate('created_at', today())->count()),
                    'verified'  => $this->safeCount(fn() => User::whereNotNull('email_verified_at')->count()),
                ],
            ],
            [
                'title'   => 'Products',
                'value'   => $totalProducts,
                'growth'  => $this->formatGrowth($this->calculateGrowth($currentMonthProducts, $lastMonthProducts)),
                'subtext' => 'total products',
                'icon'    => 'package',
                'color'   => 'success',
                'details' => [
                    'with_features' => $this->safeCount(fn() => ProductFeature::distinct('product_id')->count('product_id')),
                ],
            ],
            [
                'title'   => 'Blogs',
                'value'   => $totalBlogs,
                'growth'  => $this->formatGrowth($this->calculateGrowth($currentMonthBlogs, $lastMonthBlogs)),
                'subtext' => 'total posts',
                'icon'    => 'file-text',
                'color'   => 'warning',
                'details' => [
                    'published' => $this->safeCount(fn() => Blog::where('status', 'published')->count()),
                    'drafts'    => $this->safeCount(fn() => Blog::where('status', 'draft')->count()),
                ],
            ],
            [
                'title'   => 'Services',
                'value'   => $totalServices,
                'subtext' => $activeServices . ' active',
                'icon'    => 'settings',
                'color'   => 'info',
            ],
            [
                'title'   => 'Job Openings',
                'value'   => $totalJobs,
                'subtext' => $activeJobs . ' active',
                'icon'    => 'briefcase',
                'color'   => 'danger',
                'details' => ['applications' => $totalApplications],
            ],
            [
                'title'   => 'Gallery',
                'value'   => $totalImages,
                'subtext' => 'images',
                'icon'    => 'image',
                'color'   => 'purple',
            ],
            [
                'title'   => 'FAQ',
                'value'   => $totalFaqs,
                'subtext' => 'entries',
                'icon'    => 'help-circle',
                'color'   => 'pink',
            ],
            [
                'title'   => 'Portfolio',
                'value'   => $totalPortfolio,
                'subtext' => 'projects',
                'icon'    => 'grid',
                'color'   => 'indigo',
            ],
            [
                'title'   => 'Team',
                'value'   => $totalTeam,
                'subtext' => $activeTeam . ' active',
                'icon'    => 'users',
                'color'   => 'cyan',
            ],
            [
                'title'   => 'Testimonials',
                'value'   => $totalTestimonials,
                'subtext' => 'reviews',
                'icon'    => 'star',
                'color'   => 'amber',
            ],
            [
                'title'   => 'Contact Messages',
                'value'   => $totalMessages,
                'subtext' => $unreadMessages . ' unread',
                'icon'    => 'message-square',
                'color'   => 'emerald',
                'details' => [
                    'unread'  => $unreadMessages,
                    'today'   => $this->safeCount(fn() => ContactMessage::whereDate('created_at', today())->count()),
                ],
            ],
            [
                'title'   => 'Inquiries',
                'value'   => $totalInquiries,
                'subtext' => $unreadInquiries . ' unread',
                'icon'    => 'message-circle',
                'color'   => 'rose',
            ],
            [
                'title'   => 'Newsletter',
                'value'   => $totalSubscribers,
                'subtext' => $activeSubscribers . ' active',
                'icon'    => 'mail',
                'color'   => 'slate',
            ],
            [
                'title'   => 'Activity Logs',
                'value'   => $todayActivity,
                'subtext' => 'today',
                'icon'    => 'activity',
                'color'   => 'gray',
            ],
        ];
    }

    /**
     * Sidebar / Module Counts
     */
    private function getSidebarCounts()
    {
        return [
            'users'      => ['total' => $this->safeCount(fn() => User::count())],
            'products'   => ['total' => $this->safeCount(fn() => Product::count())],
            'categories' => ['total' => $this->safeCount(fn() => Category::count())],
            'services'   => [
                'total'  => $this->safeCount(fn() => Service::count()),
                'active' => $this->safeCount(fn() => Service::where('is_active', true)->count()),
            ],
            'blogs'      => [
                'total'     => $this->safeCount(fn() => Blog::count()),
                'published' => $this->safeCount(fn() => Blog::where('status', 'published')->count()),
                'drafts'    => $this->safeCount(fn() => Blog::where('status', 'draft')->count()),
            ],
            'faq'        => ['total' => $this->safeCount(fn() => Faq::count())],
            'gallery'    => ['total' => $this->safeCount(fn() => GalleryImage::count())],
            'career'     => [
                'job_openings' => $this->safeCount(fn() => JobOpening::count()),
                'applications' => $this->safeCount(fn() => JobApplication::count()),
            ],
            'portfolio'  => ['total' => $this->safeCount(fn() => PortfolioProject::count())],
            'team'       => ['total' => $this->safeCount(fn() => TeamMember::count())],
            'testimonials' => ['total' => $this->safeCount(fn() => Testimonial::count())],
            'contact_messages' => [
                'total'  => $this->safeCount(fn() => ContactMessage::count()),
                'unread' => $this->safeCount(fn() => ContactMessage::where('status', 'unread')->count()),
            ],
            'newsletter' => ['total' => $this->safeCount(fn() => NewsletterSubscriber::count())],
            'settings'   => ['total' => $this->safeCount(fn() => SiteSetting::count())],
        ];
    }

    /**
     * Recent Activities
     */
    private function getRecentActivities($limit = 15)
    {
        $activities = [];

        $models = [
            [User::class,         'user',        'New user registered',    'name',        'user-plus',    'success'],
            [Blog::class,         'blog',        'New blog created',       'title',       'file-text',    'primary'],
            [Product::class,      'product',     'New product added',      'title',       'package',      'warning'],
            [Service::class,      'service',     'New service added',      'title',       'settings',     'info'],
            [JobOpening::class,   'job',         'New job opening',        'title',       'briefcase',    'danger'],
            [ContactMessage::class,'message',    'New contact message',    'name',        'message-square','emerald'],
            [TeamMember::class,   'team',        'New team member',        'name',        'users',        'cyan'],
        ];

        foreach ($models as [$class, $type, $action, $field, $icon, $color]) {
            try {
                $items = $class::latest()->take(2)->get();
                foreach ($items as $item) {
                    $activities[] = [
                        'type'    => $type,
                        'action'  => $action,
                        'details' => $item->$field ?? 'Unknown',
                        'time'    => $item->created_at ? $item->created_at->diffForHumans() : 'N/A',
                        'icon'    => $icon,
                        'color'   => $color,
                    ];
                }
            } catch (\Exception $e) {
                // Skip if model/table doesn't exist
            }
        }

        return array_slice($activities, 0, $limit);
    }

    /**
     * Chart Data - last 7 days
     */
    private function getChartData($days = 7)
    {
        $labels          = [];
        $usersData       = [];
        $productsData    = [];
        $blogsData       = [];
        $messagesData    = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date      = now()->subDays($i);
            $labels[]  = $date->format('D, M d');
            $usersData[]    = $this->safeCount(fn() => User::whereDate('created_at', $date)->count());
            $productsData[] = $this->safeCount(fn() => Product::whereDate('created_at', $date)->count());
            $blogsData[]    = $this->safeCount(fn() => Blog::whereDate('created_at', $date)->count());
            $messagesData[] = $this->safeCount(fn() => ContactMessage::whereDate('created_at', $date)->count());
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                ['label' => 'Users',           'data' => $usersData,    'borderColor' => '#3b82f6'],
                ['label' => 'Products',        'data' => $productsData, 'borderColor' => '#f59e0b'],
                ['label' => 'Blogs',           'data' => $blogsData,    'borderColor' => '#10b981'],
                ['label' => 'Contact Messages','data' => $messagesData, 'borderColor' => '#8b5cf6'],
            ],
        ];
    }

    /**
     * Quick Stats
     */
    private function getQuickStats()
    {
        return [
            'system' => [
                'php_version'    => phpversion(),
                'laravel_version'=> app()->version(),
                'environment'    => app()->environment(),
            ],
            'performance' => [
                'cache_driver'     => config('cache.default'),
                'session_driver'   => config('session.driver'),
                'queue_connection' => config('queue.default'),
            ],
        ];
    }

    /**
     * System Information
     */
    private function getSystemInfo()
    {
        return [
            'server'       => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
        ];
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    private function safeCount(callable $fn): int
    {
        try {
            return (int) $fn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateGrowth($current, $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    private function formatGrowth($percentage): string
    {
        $r = round($percentage);
        return ($r > 0 ? '+' : '') . $r . '%';
    }

    private function getUserStats(): array
    {
        return [
            'total'      => $this->safeCount(fn() => User::count()),
            'verified'   => $this->safeCount(fn() => User::whereNotNull('email_verified_at')->count()),
            'active'     => $this->safeCount(fn() => User::where('is_active', true)->count()),
            'this_month' => $this->safeCount(fn() => User::whereMonth('created_at', now()->month)->count()),
        ];
    }

    private function getProductStats(): array
    {
        return [
            'total'  => $this->safeCount(fn() => Product::count()),
            'active' => $this->safeCount(fn() => Product::where('is_active', true)->count()),
        ];
    }

    private function getServiceStats(): array
    {
        return [
            'total'  => $this->safeCount(fn() => Service::count()),
            'active' => $this->safeCount(fn() => Service::where('is_active', true)->count()),
        ];
    }

    private function getBlogStats(): array
    {
        return [
            'total'     => $this->safeCount(fn() => Blog::count()),
            'published' => $this->safeCount(fn() => Blog::where('status', 'published')->count()),
            'drafts'    => $this->safeCount(fn() => Blog::where('status', 'draft')->count()),
        ];
    }

    private function getCareerStats(): array
    {
        return [
            'openings'     => $this->safeCount(fn() => JobOpening::count()),
            'applications' => $this->safeCount(fn() => JobApplication::count()),
            'pending'      => $this->safeCount(fn() => JobApplication::where('status', 'pending')->count()),
        ];
    }

    private function getGalleryStats(): array
    {
        return [
            'total'      => $this->safeCount(fn() => GalleryImage::count()),
            'this_month' => $this->safeCount(fn() => GalleryImage::whereMonth('created_at', now()->month)->count()),
        ];
    }

    private function getPortfolioStats(): array
    {
        return [
            'total'    => $this->safeCount(fn() => PortfolioProject::count()),
            'featured' => $this->safeCount(fn() => PortfolioProject::where('is_featured', true)->count()),
        ];
    }

    private function getTeamStats(): array
    {
        return [
            'total'  => $this->safeCount(fn() => TeamMember::count()),
            'active' => $this->safeCount(fn() => TeamMember::where('is_active', true)->count()),
        ];
    }

    private function getTestimonialStats(): array
    {
        return [
            'total'  => $this->safeCount(fn() => Testimonial::count()),
            'active' => $this->safeCount(fn() => Testimonial::where('is_active', true)->count()),
        ];
    }

    private function getInquiryStats(): array
    {
        return [
            'total'  => $this->safeCount(fn() => ContactInquiry::count()),
            'unread' => $this->safeCount(fn() => ContactInquiry::where('is_read', false)->count()),
        ];
    }

    private function getNewsletterStats(): array
    {
        return [
            'total'       => $this->safeCount(fn() => NewsletterSubscriber::count()),
            'subscribed'  => $this->safeCount(fn() => NewsletterSubscriber::where('is_subscribed', true)->count()),
        ];
    }
}
