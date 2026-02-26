<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\GalleryImageController;
use App\Http\Controllers\API\JobApplicationController;
use App\Http\Controllers\API\JobOpeningController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductFeatureController;
use App\Http\Controllers\API\ServiceController ;
use App\Http\Controllers\API\ServiceFeatureController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\API\ApplicationStatusHistoryController;
use App\Http\Controllers\Api\EmailMessageController;
use App\Http\Controllers\Api\EmailStatisticController;
use App\Http\Controllers\Api\EmailOpenController;
use App\Http\Controllers\Api\ViewController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\StatisticController;
use App\Http\Controllers\API\WhyChooseUsPointController;
use App\Http\Controllers\API\ProcessStepController;
use App\Http\Controllers\API\CommitmentController;
use App\Http\Controllers\API\TeamMemberController;
use App\Http\Controllers\API\TestimonialController;
use App\Http\Controllers\API\ContactInquiryController;
use App\Http\Controllers\API\SiteSettingController;
use App\Http\Controllers\API\NewsletterSubscriberController;
use App\Http\Controllers\API\PortfolioProjectController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CareerSettingController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\InterviewScheduleController;
use App\Http\Controllers\API\JobAlertController;
use App\Http\Controllers\API\JobCategoryController;
use App\Http\Controllers\API\PerkBenefitController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\ProductImageController;
use App\Http\Controllers\API\ProductPricingTierController;
use App\Http\Controllers\API\RelatedProductController;
use App\Http\Controllers\API\TierFeatureController;
use App\Http\Controllers\API\CategorysController;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ✅ PUBLIC ROUTES 
Route::prefix('blogs')->group(function() {  // 👈 NORMAL PARENTHESES
    Route::get('/', [BlogController::class, 'index']);      // GET /api/blogs
    Route::get('/search', [BlogController::class, 'search']); // GET /api/blogs/search
    Route::get('/latest', [BlogController::class, 'latest']); // GET /api/blogs/latest
    Route::get('/{identifier}', [BlogController::class, 'show']); // GET /api/blogs/1
   
});

// ✅ PROTECTED ROUTES (WITH AUTH)
Route::middleware('auth:sanctum')->prefix('blogs')->group(function() {
    Route::post('/', [BlogController::class, 'store']);        // POST /api/blogs
    Route::put('/{id}', [BlogController::class, 'update']);    // PUT /api/blogs/1
    Route::delete('/{id}', [BlogController::class, 'destroy']); // DELETE /api/blogs/1
    Route::post('/{id}/thumbnail', [BlogController::class, 'uploadThumbnail']);
});



// ----------------------------category-------------------------------------


// Category Routes
Route::prefix('categories')->group(function () {
    
    // Public routes (no authentication required)
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/parents', [CategoryController::class, 'getParents'])->name('parents');
    Route::get('/{identifier}', [CategoryController::class, 'show'])->name('show');
    
    // Protected routes (add authentication later)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
    });
});


// ------------------------gallery_images-------------------------------------------|
//  GALLERY IMAGES ROUTES
Route::prefix('gallery-images')->group(function() {
    Route::get('/', [GalleryImageController::class, 'index']);           // GET /api/gallery-images
    Route::get('/category/{categoryId}', [GalleryImageController::class, 'byCategory']); // GET /api/gallery-images/category/1
    Route::get('/{id}', [GalleryImageController::class, 'show']);        // GET /api/gallery-images/1
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/', [GalleryImageController::class, 'store']);      // POST /api/gallery-images
        Route::put('/{id}', [GalleryImageController::class, 'update']);   // PUT /api/gallery-images/1
        Route::delete('/{id}', [GalleryImageController::class, 'destroy']); // DELETE /api/gallery-images/1
    });
});

// -------------------------------------------------------------------------------------------------------------
// Public FAQ routes
Route::prefix('faqs')->name('api.faqs.')->group(function () {
    
    // Public routes (no authentication required)
    Route::get('/', [FaqController::class, 'index'])->name('index');
    Route::get('/active', [FaqController::class, 'getActive'])->name('active');
    Route::get('/{id}', [FaqController::class, 'show'])->name('show');
    
    // Protected routes (add authentication later)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [FaqController::class, 'store'])->name('store');
        Route::put('/{id}', [FaqController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [FaqController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [FaqController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [FaqController::class, 'bulkDelete'])->name('bulk-delete');
    });
});

// ---------------------------------------------------------------------------------------
// Public routes
Route::apiResource('users', UserController::class);


// -------------------------------------------------------------------------------------

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getFreshStats'])->name('admin.dashboard.stats');
});
// ------------------------------------------------------------------------------------------
Route::prefix('v1')->group(function () {
    Route::get('/activity-logs/dashboard', [ActivityLogController::class, 'dashboard']);
    Route::get('/activity-logs/stats', [ActivityLogController::class, 'stats']);
    Route::post('/activity-logs/clear', [ActivityLogController::class, 'clear']);
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::post('/activity-logs', [ActivityLogController::class, 'store']);
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show']);
});
// ---------------------------------------------------------------------------------------------------------------
Route::prefix('v1')->group(function () {
    
    // Email Messages Routes
    Route::prefix('emails')->group(function () {
        Route::get('/dashboard', [EmailMessageController::class, 'dashboard']);
        Route::get('/{id}/track/open', [EmailMessageController::class, 'trackOpen'])->name('email.track.open');
        Route::post('/{id}/mark-as-sent', [EmailMessageController::class, 'markAsSent']);
        Route::post('/{id}/mark-as-delivered', [EmailMessageController::class, 'markAsDelivered']);
        
        Route::get('/', [EmailMessageController::class, 'index']);
        Route::post('/', [EmailMessageController::class, 'store']);
        Route::get('/{id}', [EmailMessageController::class, 'show']);
        Route::put('/{id}', [EmailMessageController::class, 'update']);
        Route::delete('/{id}', [EmailMessageController::class, 'destroy']);
        
        Route::get('/{emailId}/opens', [EmailOpenController::class, 'index']);
    });
    
    // Email Statistics Routes
    Route::prefix('email-stats')->group(function () {
        Route::get('/', [EmailStatisticController::class, 'index']);
        Route::get('/trend', [EmailStatisticController::class, 'trend']);
        Route::get('/summary', [EmailStatisticController::class, 'summary']);
    });
    
    // Email Opens Routes
    Route::prefix('email-opens')->group(function () {
        Route::get('/device-breakdown', [EmailOpenController::class, 'deviceBreakdown']);
        Route::get('/hourly-distribution', [EmailOpenController::class, 'hourlyDistribution']);
        Route::get('/unique-stats', [EmailOpenController::class, 'uniqueStats']);
        Route::get('/', [EmailOpenController::class, 'index']);
    });
});
// ---------------------------------------------------------------------------------------------------------
Route::prefix('v1')->group(function () {
    
    // Views Routes
    Route::prefix('views')->group(function () {
        // Dashboard with +8% metric
        Route::get('/dashboard', [ViewController::class, 'dashboard']);
        
        // Statistics routes
        Route::get('/statistics', [ViewController::class, 'statistics']);
        Route::get('/trend', [ViewController::class, 'trend']);
        Route::get('/summary', [ViewController::class, 'summary']);
        
        // Device breakdown
        Route::get('/device-breakdown', [ViewController::class, 'deviceBreakdown']);
        
        // Views by type
        Route::get('/by-type', [ViewController::class, 'viewsByType']);
        
        // Track new view
        Route::post('/track', [ViewController::class, 'track']);
        
        // CRUD routes
        Route::get('/', [ViewController::class, 'index']);
        Route::get('/{id}', [ViewController::class, 'show']);
    });
});

// ---------------------------------------------------------------------------------------------------------------------
   // ========== PUBLIC ROUTES (No Auth Required) ==========
    
    // Auth routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
   
    
    // Newsletter public routes
    Route::post('newsletter/subscribe', [NewsletterSubscriberController::class, 'store']);
    Route::post('newsletter/unsubscribe', [NewsletterSubscriberController::class, 'unsubscribe']);
    
    // Company info routes
    Route::get('company/settings', [CompanyController::class, 'settings']);
    Route::get('company/meta', [CompanyController::class, 'meta']);
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{id}', [CompanyController::class, 'show']);
    
    // Services routes
    Route::get('services', [ServiceController::class, 'index']);
    // Route::get('services/slug/{slug}', [ServiceController::class, 'findBySlug']);
    Route::get('services/{id}', [ServiceController::class, 'show']);
    
    // Service Features routes
    Route::get('services/{serviceId}/features', [ServiceFeatureController::class, 'index']);
    Route::get('service-features', [ServiceFeatureController::class, 'index']);
    Route::get('service-features/{id}', [ServiceFeatureController::class, 'show']);
    
    // Commitments routes
    Route::get('commitments', [CommitmentController::class, 'index']);
    Route::get('commitments/{id}', [CommitmentController::class, 'show']);
    
    // Process Steps routes
    Route::get('process-steps', [ProcessStepController::class, 'index']);
    Route::get('process-steps/range', [ProcessStepController::class, 'getRange']);
    Route::get('process-steps/{id}', [ProcessStepController::class, 'show']);
    
    // Statistics routes
    Route::get('statistics', [StatisticController::class, 'index']);
    Route::get('statistics/summary', [StatisticController::class, 'summary']);
    Route::get('statistics/{id}', [StatisticController::class, 'show']);
    
    // Why Choose Us Points routes
    Route::get('why-choose-us-points', [WhyChooseUsPointController::class, 'index']);
    Route::get('why-choose-us-points/random', [WhyChooseUsPointController::class, 'getRandom']);
    Route::get('why-choose-us-points/{id}', [WhyChooseUsPointController::class, 'show']);
    
    // Team Members routes
    Route::get('team-members', [TeamMemberController::class, 'index']);
    Route::get('team-members/with-social', [TeamMemberController::class, 'getWithSocialLinks']);
    Route::get('team-members/by-position', [TeamMemberController::class, 'getByPosition']);
    Route::get('team-members/{id}', [TeamMemberController::class, 'show']);
    
    // Testimonials routes
    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::get('testimonials/rating-stats', [TestimonialController::class, 'ratingStats']);
    Route::get('testimonials/featured', [TestimonialController::class, 'getFeatured']);
    Route::get('testimonials/{id}', [TestimonialController::class, 'show']);
    
    // Public settings routes (read-only)
    Route::get('settings/all', [SiteSettingController::class, 'getAll']);
    Route::get('settings/key/{key}/value', [SiteSettingController::class, 'getValue']);
    Route::get('settings/key/{key}', [SiteSettingController::class, 'getByKey']);
    
    // Portfolio public routes
    Route::get('portfolio', [PortfolioProjectController::class, 'index']);
    Route::get('portfolio/categories', [PortfolioProjectController::class, 'getCategories']);
    Route::get('portfolio/years', [PortfolioProjectController::class, 'getYears']);
    Route::get('portfolio/featured', [PortfolioProjectController::class, 'getFeatured']);
    Route::get('portfolio/recent', [PortfolioProjectController::class, 'getRecent']);
    Route::get('portfolio/category/{category}', [PortfolioProjectController::class, 'getByCategory']);
    Route::get('portfolio/slug/{slug}', [PortfolioProjectController::class, 'findBySlug']);
    Route::get('portfolio/{id}', [PortfolioProjectController::class, 'show']);

    // ========== PROTECTED ROUTES (Auth Required) ==========
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        
        // Newsletter admin routes
        Route::get('newsletter/subscribers', [NewsletterSubscriberController::class, 'index']);
        Route::get('newsletter/subscribers/stats', [NewsletterSubscriberController::class, 'stats']);
        Route::get('newsletter/subscribers/export', [NewsletterSubscriberController::class, 'export']);
        Route::get('newsletter/subscribers/{id}', [NewsletterSubscriberController::class, 'show']);
        Route::get('newsletter/subscribers/email/{email}', [NewsletterSubscriberController::class, 'findByEmail']);
        Route::put('newsletter/subscribers/{id}', [NewsletterSubscriberController::class, 'update']);
        Route::post('newsletter/subscribers/{id}/resubscribe', [NewsletterSubscriberController::class, 'resubscribe']);
        Route::post('newsletter/subscribers/{id}/unsubscribe', [NewsletterSubscriberController::class, 'unsubscribeById']);
        Route::delete('newsletter/subscribers/{id}', [NewsletterSubscriberController::class, 'destroy']);
        Route::post('newsletter/subscribers/bulk-delete', [NewsletterSubscriberController::class, 'bulkDelete']);
        Route::post('newsletter/send', [NewsletterSubscriberController::class, 'sendNewsletter']);
        
        // Company routes
        Route::post('companies', [CompanyController::class, 'store']);
        Route::put('companies/{id}', [CompanyController::class, 'update']);
        Route::delete('companies/{id}', [CompanyController::class, 'destroy']);
        Route::post('companies/bulk-delete', [CompanyController::class, 'bulkDelete']);
        
        // Service routes
        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{id}', [ServiceController::class, 'update']);
        Route::delete('services/{id}', [ServiceController::class, 'destroy']);
        Route::post('services/bulk-delete', [ServiceController::class, 'bulkDelete']);
        Route::post('services/reorder', [ServiceController::class, 'reorder']);
        Route::patch('services/{id}/toggle-featured', [ServiceController::class, 'toggleFeatured']);
        Route::patch('services/{id}/toggle-active', [ServiceController::class, 'toggleActive']);
        
        // Service Features routes
        Route::post('service-features', [ServiceFeatureController::class, 'store']);
        Route::put('service-features/{id}', [ServiceFeatureController::class, 'update']);
        Route::delete('service-features/{id}', [ServiceFeatureController::class, 'destroy']);
        Route::post('service-features/bulk-delete', [ServiceFeatureController::class, 'bulkDelete']);
        Route::post('service-features/reorder', [ServiceFeatureController::class, 'reorder']);
        Route::patch('service-features/{id}/toggle-active', [ServiceFeatureController::class, 'toggleActive']);
        Route::post('service-features/clone', [ServiceFeatureController::class, 'clone']);
        
        // Commitments routes
        Route::post('commitments', [CommitmentController::class, 'store']);
        Route::put('commitments/{id}', [CommitmentController::class, 'update']);
        Route::delete('commitments/{id}', [CommitmentController::class, 'destroy']);
        Route::post('commitments/bulk-delete', [CommitmentController::class, 'bulkDelete']);
        Route::post('commitments/reorder', [CommitmentController::class, 'reorder']);
        Route::patch('commitments/{id}/toggle-active', [CommitmentController::class, 'toggleActive']);
        
        // Process Steps routes
        Route::post('process-steps', [ProcessStepController::class, 'store']);
        Route::put('process-steps/{id}', [ProcessStepController::class, 'update']);
        Route::delete('process-steps/{id}', [ProcessStepController::class, 'destroy']);
        Route::post('process-steps/bulk-delete', [ProcessStepController::class, 'bulkDelete']);
        Route::post('process-steps/reorder', [ProcessStepController::class, 'reorder']);
        Route::patch('process-steps/{id}/toggle-active', [ProcessStepController::class, 'toggleActive']);
        
        // Statistics routes
        Route::post('statistics', [StatisticController::class, 'store']);
        Route::put('statistics/{id}', [StatisticController::class, 'update']);
        Route::delete('statistics/{id}', [StatisticController::class, 'destroy']);
        Route::post('statistics/bulk-delete', [StatisticController::class, 'bulkDelete']);
        Route::post('statistics/reorder', [StatisticController::class, 'reorder']);
        Route::patch('statistics/{id}/toggle-active', [StatisticController::class, 'toggleActive']);
        Route::post('statistics/{id}/increment', [StatisticController::class, 'increment']);
        Route::post('statistics/{id}/decrement', [StatisticController::class, 'decrement']);
        
        // Why Choose Us Points routes
        Route::post('why-choose-us-points', [WhyChooseUsPointController::class, 'store']);
        Route::put('why-choose-us-points/{id}', [WhyChooseUsPointController::class, 'update']);
        Route::delete('why-choose-us-points/{id}', [WhyChooseUsPointController::class, 'destroy']);
        Route::post('why-choose-us-points/bulk-delete', [WhyChooseUsPointController::class, 'bulkDelete']);
        Route::post('why-choose-us-points/reorder', [WhyChooseUsPointController::class, 'reorder']);
        Route::patch('why-choose-us-points/{id}/toggle-active', [WhyChooseUsPointController::class, 'toggleActive']);
        
        // Team Members routes
        Route::post('team-members', [TeamMemberController::class, 'store']);
        Route::put('team-members/{id}', [TeamMemberController::class, 'update']);
        Route::delete('team-members/{id}', [TeamMemberController::class, 'destroy']);
        Route::post('team-members/bulk-delete', [TeamMemberController::class, 'bulkDelete']);
        Route::post('team-members/reorder', [TeamMemberController::class, 'reorder']);
        Route::patch('team-members/{id}/toggle-active', [TeamMemberController::class, 'toggleActive']);
        
        // Testimonials routes
        Route::post('testimonials', [TestimonialController::class, 'store']);
        Route::put('testimonials/{id}', [TestimonialController::class, 'update']);
        Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);
        Route::post('testimonials/bulk-delete', [TestimonialController::class, 'bulkDelete']);
        Route::post('testimonials/reorder', [TestimonialController::class, 'reorder']);
        Route::patch('testimonials/{id}/toggle-active', [TestimonialController::class, 'toggleActive']);
        
        // Contact Inquiries routes
        Route::get('contact-inquiries', [ContactInquiryController::class, 'index']);
        Route::get('contact-inquiries/stats', [ContactInquiryController::class, 'stats']);
        Route::get('contact-inquiries/export', [ContactInquiryController::class, 'export']);
        Route::get('contact-inquiries/{id}', [ContactInquiryController::class, 'show']);
        Route::put('contact-inquiries/{id}', [ContactInquiryController::class, 'update']);
        Route::patch('contact-inquiries/{id}/status', [ContactInquiryController::class, 'updateStatus']);
        Route::delete('contact-inquiries/{id}', [ContactInquiryController::class, 'destroy']);
        Route::post('contact-inquiries/bulk-delete', [ContactInquiryController::class, 'bulkDelete']);
        
        // Site Settings routes (admin only - full CRUD)
        Route::get('settings', [SiteSettingController::class, 'index']);
        Route::post('settings', [SiteSettingController::class, 'store']);
        Route::post('settings/bulk-update', [SiteSettingController::class, 'bulkUpdate']);
        Route::post('settings/reset', [SiteSettingController::class, 'resetToDefaults']);
        Route::get('settings/{id}', [SiteSettingController::class, 'show']);
        Route::put('settings/{id}', [SiteSettingController::class, 'update']);
        Route::put('settings/key/{key}', [SiteSettingController::class, 'updateByKey']);
        Route::delete('settings/{id}', [SiteSettingController::class, 'destroy']);
        Route::delete('settings/key/{key}', [SiteSettingController::class, 'deleteByKey']);
        Route::post('settings/bulk-delete', [SiteSettingController::class, 'bulkDelete']);
        
        // Portfolio admin routes
        Route::post('portfolio', [PortfolioProjectController::class, 'store']);
        Route::put('portfolio/{id}', [PortfolioProjectController::class, 'update']);
        Route::delete('portfolio/{id}', [PortfolioProjectController::class, 'destroy']);
        Route::post('portfolio/bulk-delete', [PortfolioProjectController::class, 'bulkDelete']);
        Route::post('portfolio/reorder', [PortfolioProjectController::class, 'reorder']);
        Route::patch('portfolio/{id}/toggle-featured', [PortfolioProjectController::class, 'toggleFeatured']);
        Route::patch('portfolio/{id}/toggle-active', [PortfolioProjectController::class, 'toggleActive']);
    });
// ---------------------------------------------------------------------------------------------------------------------
                            //  carrer  // Auth routes
   // ========== PUBLIC ROUTES (No Auth Required) ==========
       
  
// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Job Alerts public routes
Route::post('/job-alerts', [JobAlertController::class, 'store']);
Route::post('/job-alerts/unsubscribe', [JobAlertController::class, 'unsubscribe']);
Route::get('/job-alerts/options', [JobAlertController::class, 'getOptions']);
Route::get('/job-alerts/email/{email}', [JobAlertController::class, 'findByEmail']);

// Department public routes
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/options', [DepartmentController::class, 'getOptions']);
Route::get('/departments/slug/{slug}', [DepartmentController::class, 'findBySlug']);
Route::get('/departments/stats', [DepartmentController::class, 'stats']);
Route::get('/departments/{id}', [DepartmentController::class, 'show']);

// Job Categories public routes
Route::get('/job-categories', [JobCategoryController::class, 'index']);
Route::get('/job-categories/options', [JobCategoryController::class, 'getOptions']);
Route::get('/job-categories/popular', [JobCategoryController::class, 'getPopular']);
Route::get('/job-categories/stats', [JobCategoryController::class, 'stats']);
Route::get('/job-categories/slug/{slug}', [JobCategoryController::class, 'findBySlug']);
Route::get('/job-categories/with-counts', [JobOpeningController::class, 'getJobsCountByCategory']);
Route::get('/job-categories/{id}', [JobCategoryController::class, 'show']);

// Job Openings public routes
Route::get('/jobs', [JobOpeningController::class, 'index']);
Route::get('/jobs/filters', [JobOpeningController::class, 'index'])->defaults('get_filters', true);
Route::get('/jobs/stats', [JobOpeningController::class, 'stats']);
Route::get('/jobs/slug/{slug}', [JobOpeningController::class, 'findBySlug']);
Route::get('/jobs/{id}', [JobOpeningController::class, 'show']);

// Job Applications public routes
Route::post('/jobs/apply', [JobApplicationController::class, 'store']);
Route::post('/jobs/check-application', [JobApplicationController::class, 'checkApplication']);

// Perks & Benefits public routes
Route::get('/perks', [PerkBenefitController::class, 'index']);
Route::get('/perks/categories', [PerkBenefitController::class, 'getCategories']);
Route::get('/perks/grouped', [PerkBenefitController::class, 'index'])->defaults('grouped', true);
Route::get('/perks/stats', [PerkBenefitController::class, 'stats']);
Route::get('/perks/{id}', [PerkBenefitController::class, 'show']);

// Locations public routes
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/options', [LocationController::class, 'getOptions']);
Route::get('/locations/countries', [LocationController::class, 'index'])->defaults('countries_only', true);
Route::get('/locations/states', [LocationController::class, 'index'])->defaults('states_only', true);
Route::get('/locations/grouped', [LocationController::class, 'index'])->defaults('grouped', true);
Route::get('/locations/stats', [LocationController::class, 'stats']);
Route::get('/locations/{id}', [LocationController::class, 'show']);

// ========== PROTECTED ROUTES (Auth Required) ==========
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes (protected)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Job Alerts admin routes
    Route::get('/job-alerts', [JobAlertController::class, 'index']);
    Route::get('/job-alerts/stats', [JobAlertController::class, 'stats']);
    Route::get('/job-alerts/{id}', [JobAlertController::class, 'show']);
    Route::get('/job-alerts/{id}/matching-jobs', [JobAlertController::class, 'getMatchingJobs']);
    Route::put('/job-alerts/{id}', [JobAlertController::class, 'update']);
    Route::post('/job-alerts/send/{frequency}', [JobAlertController::class, 'sendAlerts']);
    Route::post('/job-alerts/{id}/unsubscribe', [JobAlertController::class, 'unsubscribeById']);
    Route::delete('/job-alerts/{id}', [JobAlertController::class, 'destroy']);
    Route::post('/job-alerts/bulk-delete', [JobAlertController::class, 'bulkDelete']);
    
    // Department admin routes
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
    Route::patch('/departments/{id}/toggle-active', [DepartmentController::class, 'toggleActive']);
    Route::post('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete']);
    
    // Job Categories admin routes
    Route::post('/job-categories', [JobCategoryController::class, 'store']);
    Route::put('/job-categories/{id}', [JobCategoryController::class, 'update']);
    Route::delete('/job-categories/{id}', [JobCategoryController::class, 'destroy']);
    Route::patch('/job-categories/{id}/toggle-active', [JobCategoryController::class, 'toggleActive']);
    Route::post('/job-categories/bulk-delete', [JobCategoryController::class, 'bulkDelete']);
    
    // Job Openings admin routes
    Route::post('/jobs', [JobOpeningController::class, 'store']);
    Route::put('/jobs/{id}', [JobOpeningController::class, 'update']);
    Route::delete('/jobs/{id}', [JobOpeningController::class, 'destroy']);
    Route::post('/jobs/bulk-delete', [JobOpeningController::class, 'bulkDelete']);
    Route::patch('/jobs/{id}/toggle-featured', [JobOpeningController::class, 'toggleFeatured']);
    Route::patch('/jobs/{id}/toggle-active', [JobOpeningController::class, 'toggleActive']);
    Route::post('/jobs/{id}/duplicate', [JobOpeningController::class, 'duplicate']);
    
    // Job Category Mapping routes
    Route::post('/jobs/by-categories', [JobOpeningController::class, 'getByCategories']);
    Route::post('/jobs/{id}/add-category', [JobOpeningController::class, 'addCategory']);
    Route::delete('/jobs/{id}/remove-category', [JobOpeningController::class, 'removeCategory']);
    Route::get('/jobs/categories/counts', [JobOpeningController::class, 'getJobsCountByCategory']);
    
    // Job Applications admin routes
    Route::get('/applications', [JobApplicationController::class, 'index']);
    Route::get('/applications/stats', [JobApplicationController::class, 'stats']);
    Route::get('/applications/export', [JobApplicationController::class, 'export']);
    Route::get('/applications/{id}', [JobApplicationController::class, 'show']);
    Route::get('/applications/{id}/download-resume', [JobApplicationController::class, 'downloadResume']);
    Route::put('/applications/{id}', [JobApplicationController::class, 'update']);
    Route::patch('/applications/{id}/status', [JobApplicationController::class, 'updateStatus']);
    Route::delete('/applications/{id}', [JobApplicationController::class, 'destroy']);
    Route::post('/applications/bulk-delete', [JobApplicationController::class, 'bulkDelete']);

    // Application Status History routes
    Route::get('/applications/history', [ApplicationStatusHistoryController::class, 'index']);
    Route::get('/applications/history/stats', [ApplicationStatusHistoryController::class, 'stats']);
    Route::get('/applications/{applicationId}/history', [ApplicationStatusHistoryController::class, 'forApplication']);
    Route::get('/applications/{applicationId}/timeline', [ApplicationStatusHistoryController::class, 'timeline']);
    Route::get('/applications/history/{id}', [ApplicationStatusHistoryController::class, 'show']);
    Route::post('/applications/history', [ApplicationStatusHistoryController::class, 'store']);
    Route::put('/applications/history/{id}', [ApplicationStatusHistoryController::class, 'update']);
    Route::delete('/applications/history/{id}', [ApplicationStatusHistoryController::class, 'destroy']);
    Route::post('/applications/history/bulk-delete', [ApplicationStatusHistoryController::class, 'bulkDelete']);
    
    // Perks & Benefits admin routes
    Route::post('/perks', [PerkBenefitController::class, 'store']);
    Route::put('/perks/{id}', [PerkBenefitController::class, 'update']);
    Route::delete('/perks/{id}', [PerkBenefitController::class, 'destroy']);
    Route::post('/perks/bulk-delete', [PerkBenefitController::class, 'bulkDelete']);
    Route::post('/perks/reorder', [PerkBenefitController::class, 'reorder']);
    Route::patch('/perks/{id}/toggle-active', [PerkBenefitController::class, 'toggleActive']);
    Route::post('/perks/bulk-update-category', [PerkBenefitController::class, 'bulkUpdateCategory']);
    
    // Locations admin routes
    Route::post('/locations', [LocationController::class, 'store']);
    Route::post('/locations/import', [LocationController::class, 'import']);
    Route::put('/locations/{id}', [LocationController::class, 'update']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);
    Route::post('/locations/bulk-delete', [LocationController::class, 'bulkDelete']);
    Route::patch('/locations/{id}/toggle-active', [LocationController::class, 'toggleActive']);

    // Interview Schedule routes (protected)
Route::get('/interviews', [InterviewScheduleController::class, 'index']);
Route::get('/interviews/calendar', [InterviewScheduleController::class, 'calendar']);
Route::get('/interviews/stats', [InterviewScheduleController::class, 'stats']);
Route::get('/interviews/application/{applicationId}', [InterviewScheduleController::class, 'forApplication']);
Route::get('/interviews/{id}', [InterviewScheduleController::class, 'show']);
Route::post('/interviews', [InterviewScheduleController::class, 'store']);
Route::put('/interviews/{id}', [InterviewScheduleController::class, 'update']);
Route::patch('/interviews/{id}/status', [InterviewScheduleController::class, 'updateStatus']);
Route::post('/interviews/{id}/feedback', [InterviewScheduleController::class, 'addFeedback']);
Route::post('/interviews/{id}/reschedule', [InterviewScheduleController::class, 'reschedule']);
Route::post('/interviews/{id}/cancel', [InterviewScheduleController::class, 'cancel']);
Route::delete('/interviews/{id}', [InterviewScheduleController::class, 'destroy']);



// Career Settings public routes (read-only)
Route::get('/career-settings/page', [CareerSettingController::class, 'getCareerPage']);
Route::get('/career-settings/application-form', [CareerSettingController::class, 'getApplicationFormSettings']);
Route::get('/career-settings/email-settings', [CareerSettingController::class, 'getEmailSettings']);
Route::get('/career-settings/all', [CareerSettingController::class, 'getAll']);
Route::get('/career-settings/key/{key}/value', [CareerSettingController::class, 'getValue']);
Route::get('/career-settings/key/{key}', [CareerSettingController::class, 'getByKey']);

// Career Settings admin routes (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/career-settings', [CareerSettingController::class, 'index']);
    Route::get('/career-settings/{id}', [CareerSettingController::class, 'show']);
    Route::post('/career-settings', [CareerSettingController::class, 'store']);
    Route::post('/career-settings/bulk-update', [CareerSettingController::class, 'bulkUpdate']);
    Route::post('/career-settings/career-page', [CareerSettingController::class, 'updateCareerPage']);
    Route::post('/career-settings/reset', [CareerSettingController::class, 'resetToDefaults']);
    Route::put('/career-settings/{id}', [CareerSettingController::class, 'update']);
    Route::put('/career-settings/key/{key}', [CareerSettingController::class, 'updateByKey']);
    Route::delete('/career-settings/{id}', [CareerSettingController::class, 'destroy']);
    Route::delete('/career-settings/key/{key}', [CareerSettingController::class, 'deleteByKey']);
});
});
// ------------------------------------------------------------------------------------------------------------
  // ========== PRODUCTS PUBLIC ROUTES ==========
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/options', [ProductController::class, 'getOptions']);
    Route::get('/products/slug/{slug}', [ProductController::class, 'findBySlug']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // ========== PRODUCT FEATURES PUBLIC ROUTES ==========
    Route::get('/product-features/icons', [ProductFeatureController::class, 'getIcons']);
    Route::get('/product-features', [ProductFeatureController::class, 'index']);
    Route::get('/products/{productId}/features', [ProductFeatureController::class, 'forProduct']);
    Route::get('/product-features/{id}', [ProductFeatureController::class, 'show']);

    // ========== PRODUCT IMAGES PUBLIC ROUTES ==========
    Route::get('/product-images', [ProductImageController::class, 'index']);
    Route::get('/products/{productId}/images', [ProductImageController::class, 'forProduct']);
    Route::get('/products/{productId}/primary-image', [ProductImageController::class, 'getPrimary']);
    Route::get('/product-images/{id}', [ProductImageController::class, 'show']);

    // ========== PRODUCT PRICING TIERS PUBLIC ROUTES ==========
    Route::get('/pricing-tiers/billing-periods', [ProductPricingTierController::class, 'getBillingPeriods']);
    Route::get('/pricing-tiers/tier-names', [ProductPricingTierController::class, 'getTierNames']);
    Route::get('/pricing-tiers', [ProductPricingTierController::class, 'index']);
    Route::get('/products/{productId}/pricing-tiers', [ProductPricingTierController::class, 'forProduct']);
    Route::get('/products/{productId}/popular-tier', [ProductPricingTierController::class, 'getPopular']);
    Route::get('/pricing-tiers/{id}', [ProductPricingTierController::class, 'show']);

    // ========== TIER FEATURES PUBLIC ROUTES ==========
    Route::get('/tier-features', [TierFeatureController::class, 'index']);
    Route::get('/pricing-tiers/{tierId}/features', [TierFeatureController::class, 'forTier']);
    Route::get('/products/{productId}/tier-comparison', [TierFeatureController::class, 'getComparison']);
    Route::get('/tier-features/{id}', [TierFeatureController::class, 'show']);

    // ========== RELATED PRODUCTS PUBLIC ROUTES ==========
    Route::get('/related-products/types', [RelatedProductController::class, 'getTypes']);
    Route::get('/related-products', [RelatedProductController::class, 'index']);
    Route::get('/products/{productId}/related', [RelatedProductController::class, 'forProduct']);
    Route::get('/products/{productId}/upsells', [RelatedProductController::class, 'getUpsells']);
    Route::get('/products/{productId}/cross-sells', [RelatedProductController::class, 'getCrossSells']);
    Route::get('/products/{productId}/alternatives', [RelatedProductController::class, 'getAlternatives']);
    Route::get('/related-products/{productId}/{relatedId}', [RelatedProductController::class, 'show']);

    // ========== PROTECTED ROUTES (Auth Required) ==========
    // Route::middleware('auth:sanctum')->group(function () {
        
        // Product admin routes
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::patch('/products/{id}/toggle-active', [ProductController::class, 'toggleActive']);
        Route::post('/products/reorder', [ProductController::class, 'reorder']);
        Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete']);
        
        // Product Features admin routes
        Route::post('/product-features', [ProductFeatureController::class, 'store']);
        Route::put('/product-features/{id}', [ProductFeatureController::class, 'update']);
        Route::delete('/product-features/{id}', [ProductFeatureController::class, 'destroy']);
        Route::patch('/product-features/{id}/toggle-active', [ProductFeatureController::class, 'toggleActive']);
        Route::post('/product-features/reorder', [ProductFeatureController::class, 'reorder']);
        Route::post('/product-features/bulk-delete', [ProductFeatureController::class, 'bulkDelete']);
        Route::post('/product-features/clone', [ProductFeatureController::class, 'clone']);
        
        // Product Images admin routes
        Route::post('/product-images', [ProductImageController::class, 'store']);
        Route::post('/product-images/multiple', [ProductImageController::class, 'storeMultiple']);
        Route::put('/product-images/{id}', [ProductImageController::class, 'update']);
        Route::delete('/product-images/{id}', [ProductImageController::class, 'destroy']);
        Route::post('/product-images/{id}/set-primary', [ProductImageController::class, 'setPrimary']);
        Route::post('/product-images/reorder', [ProductImageController::class, 'reorder']);
        Route::post('/product-images/bulk-delete', [ProductImageController::class, 'bulkDelete']);
        
        // Product Pricing Tiers admin routes
        Route::post('/pricing-tiers', [ProductPricingTierController::class, 'store']);
        Route::put('/pricing-tiers/{id}', [ProductPricingTierController::class, 'update']);
        Route::delete('/pricing-tiers/{id}', [ProductPricingTierController::class, 'destroy']);
        Route::patch('/pricing-tiers/{id}/toggle-active', [ProductPricingTierController::class, 'toggleActive']);
        Route::patch('/pricing-tiers/{id}/toggle-popular', [ProductPricingTierController::class, 'togglePopular']);
        Route::post('/pricing-tiers/reorder', [ProductPricingTierController::class, 'reorder']);
        Route::post('/pricing-tiers/bulk-delete', [ProductPricingTierController::class, 'bulkDelete']);
        Route::post('/pricing-tiers/clone', [ProductPricingTierController::class, 'clone']);
        
        // Tier Features admin routes
        Route::post('/tier-features', [TierFeatureController::class, 'store']);
        Route::post('/tier-features/multiple', [TierFeatureController::class, 'storeMultiple']);
        Route::put('/tier-features/{id}', [TierFeatureController::class, 'update']);
        Route::delete('/tier-features/{id}', [TierFeatureController::class, 'destroy']);
        Route::patch('/tier-features/{id}/toggle-availability', [TierFeatureController::class, 'toggleAvailability']);
        Route::post('/tier-features/reorder', [TierFeatureController::class, 'reorder']);
        Route::post('/tier-features/bulk-delete', [TierFeatureController::class, 'bulkDelete']);
        Route::post('/tier-features/copy', [TierFeatureController::class, 'copyFromTier']);
        
        // Related Products admin routes
        Route::post('/related-products', [RelatedProductController::class, 'store']);
        Route::post('/related-products/multiple', [RelatedProductController::class, 'storeMultiple']);
        Route::put('/related-products/{productId}/{relatedId}', [RelatedProductController::class, 'update']);
        Route::delete('/related-products/{productId}/{relatedId}', [RelatedProductController::class, 'destroy']);
        Route::post('/related-products/bulk-delete', [RelatedProductController::class, 'bulkDelete']);
        Route::post('/products/{productId}/related/reorder', [RelatedProductController::class, 'reorder']);


   // ========== PUBLIC ROUTES ==========
    Route::get('/categories', [CategorysController::class, 'index']);
    Route::get('/categories/tree', [CategorysController::class, 'index'])->defaults('tree', true);
    Route::get('/categories/dropdown', [CategorysController::class, 'index'])->defaults('for_dropdown', true);
    Route::get('/categories/slug/{slug}', [CategorysController::class, 'findBySlug']);
    Route::get('/categories/{id}/children', [CategorysController::class, 'getChildren']);
    Route::get('/categories/{id}/path', [CategorysController::class, 'getPath']);
    Route::get('/categories/{id}', [CategorysController::class, 'show']);

    // ========== PROTECTED ROUTES ==========
    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::post('/categories', [CategorysController::class, 'store']);
    //     Route::put('/categories/{id}', [CategorysController::class, 'update']);
    //     Route::delete('/categories/{id}', [CategorysController::class, 'destroy']);
    //     Route::patch('/categories/{id}/toggle-active', [CategorysController::class, 'toggleActive']);
    //     Route::post('/categories/reorder', [CategorysController::class, 'reorder']);
    //     Route::post('/categories/bulk-delete', [CategorysController::class, 'bulkDelete']);
    //     Route::patch('/categories/{id}/move', [CategorysController::class, 'move']);
    // });
    // });
