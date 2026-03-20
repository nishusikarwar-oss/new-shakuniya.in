<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\GalleryImageController;
use App\Http\Controllers\API\JobApplicationController;
use App\Http\Controllers\API\JobOpeningController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductFeatureController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ServiceFeatureController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\ApplicationStatusHistoryController;
use App\Http\Controllers\API\EmailMessageController;
use App\Http\Controllers\API\EmailStatisticController;
use App\Http\Controllers\API\EmailOpenController;
use App\Http\Controllers\API\ViewController;
// ✅ FIX #3a: FaqController lives in faqcontroller.php with class FaqController
// PHP class names are case-insensitive but Laravel autoloader uses file names.
// The file is lowercase "faqcontroller.php" but class is "FaqController" — fixed by aliasing below.
use App\Http\Controllers\API\FaqController;
// ✅ FIX #3b: UserController is in usercontroller.php with namespace Api (lowercase i)
use App\Http\Controllers\Api\UserController;
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
use App\Http\Controllers\API\ContactMessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// AUTH ROUTES (Public)
// ============================================================
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'user']);
});


// ============================================================
// ADMIN DASHBOARD
// ============================================================
Route::prefix('admin')->group(function () {
    Route::get('/dashboard',                    [DashboardController::class, 'index']);
    Route::get('/dashboard/stats',              [DashboardController::class, 'getFreshStats']);
    Route::get('/dashboard/module/{module}',    [DashboardController::class, 'getModuleCounts']);
});


// ============================================================
// BLOGS
// ============================================================
Route::prefix('blogs')->group(function () {
    Route::get('/',              [BlogController::class, 'index']);
    Route::get('/search',        [BlogController::class, 'search']);
    Route::get('/latest',        [BlogController::class, 'latest']);
    Route::post('/',             [BlogController::class, 'store']);
    Route::get('/{identifier}',  [BlogController::class, 'show']);
    Route::put('/{id}',          [BlogController::class, 'update']);
    Route::delete('/{id}',       [BlogController::class, 'destroy']);
});


// ============================================================
// CATEGORIES
// ============================================================
Route::prefix('categories')->group(function () {
    Route::get('/',              [CategoryController::class, 'index']);
    Route::get('/parents',       [CategoryController::class, 'getParents']);
    Route::get('/{identifier}',  [CategoryController::class, 'show']);
    Route::post('/',             [CategoryController::class, 'store']);
    Route::put('/{id}',          [CategoryController::class, 'update']);
    Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
    Route::delete('/{id}',       [CategoryController::class, 'destroy']);
    Route::post('/bulk-delete',  [CategoryController::class, 'bulkDelete']);
});


// ============================================================
// GALLERY IMAGES
// ============================================================
Route::prefix('gallery-images')->group(function () {
    Route::get('/',                          [GalleryImageController::class, 'index']);
    Route::get('/category/{categoryId}',     [GalleryImageController::class, 'byCategory']);
    Route::get('/{id}',                      [GalleryImageController::class, 'show']);
    Route::post('/',                         [GalleryImageController::class, 'store']);
    Route::put('/{id}',                      [GalleryImageController::class, 'update']);
    Route::delete('/{id}',                   [GalleryImageController::class, 'destroy']);
});


// ============================================================
// FAQS
// ============================================================
Route::prefix('faqs')->group(function () {
    Route::get('/',                          [FaqController::class, 'index']);
    Route::get('/active',                    [FaqController::class, 'getActive']);
    Route::get('/{id}',                      [FaqController::class, 'show']);
    Route::post('/',                         [FaqController::class, 'store']);
    Route::put('/{id}',                      [FaqController::class, 'update']);
    Route::patch('/{id}/toggle-status',      [FaqController::class, 'toggleStatus']);
    Route::delete('/{id}',                   [FaqController::class, 'destroy']);
    Route::post('/bulk-delete',              [FaqController::class, 'bulkDelete']);
});


// ============================================================
// USERS
// ============================================================
Route::apiResource('users', UserController::class);
Route::prefix('users')->group(function () {
    Route::get('/stats',                     [UserController::class, 'stats']);
    Route::patch('/{id}/toggle-status',      [UserController::class, 'toggleStatus']);
    Route::post('/bulk-delete',              [UserController::class, 'bulkDelete']);
});


// ============================================================
// ACTIVITY LOGS
// ============================================================
Route::prefix('v1/activity-logs')->group(function () {
    Route::get('/dashboard',  [ActivityLogController::class, 'dashboard']);
    Route::get('/stats',      [ActivityLogController::class, 'stats']);
    Route::post('/clear',     [ActivityLogController::class, 'clear']);
    Route::get('/',           [ActivityLogController::class, 'index']);
    Route::post('/',          [ActivityLogController::class, 'store']);
    Route::get('/{id}',       [ActivityLogController::class, 'show']);
});


// ============================================================
// EMAIL / VIEWS
// ============================================================
Route::prefix('v1')->group(function () {
    Route::prefix('emails')->group(function () {
        Route::get('/dashboard',                  [EmailMessageController::class, 'dashboard']);
        Route::post('/{id}/mark-as-sent',         [EmailMessageController::class, 'markAsSent']);
        Route::post('/{id}/mark-as-delivered',    [EmailMessageController::class, 'markAsDelivered']);
        Route::get('/',                           [EmailMessageController::class, 'index']);
        Route::post('/',                          [EmailMessageController::class, 'store']);
        Route::get('/{id}',                       [EmailMessageController::class, 'show']);
        Route::put('/{id}',                       [EmailMessageController::class, 'update']);
        Route::delete('/{id}',                    [EmailMessageController::class, 'destroy']);
    });

    Route::prefix('email-stats')->group(function () {
        Route::get('/',        [EmailStatisticController::class, 'index']);
        Route::get('/trend',   [EmailStatisticController::class, 'trend']);
        Route::get('/summary', [EmailStatisticController::class, 'summary']);
    });

    Route::prefix('views')->group(function () {
        Route::get('/dashboard',        [ViewController::class, 'dashboard']);
        Route::get('/statistics',       [ViewController::class, 'statistics']);
        Route::get('/trend',            [ViewController::class, 'trend']);
        Route::get('/summary',          [ViewController::class, 'summary']);
        Route::get('/device-breakdown', [ViewController::class, 'deviceBreakdown']);
        Route::get('/by-type',          [ViewController::class, 'viewsByType']);
        Route::post('/track',           [ViewController::class, 'track']);
        Route::get('/',                 [ViewController::class, 'index']);
        Route::get('/{id}',             [ViewController::class, 'show']);
    });
});


// ============================================================
// NEWSLETTER
// ============================================================
Route::post('newsletter/subscribe',   [NewsletterSubscriberController::class, 'store']);
Route::post('newsletter/unsubscribe', [NewsletterSubscriberController::class, 'unsubscribe']);


// ============================================================
// COMPANY
// ============================================================
Route::get('company/settings',   [CompanyController::class, 'settings']);
Route::get('company/meta',       [CompanyController::class, 'meta']);
Route::get('companies',          [CompanyController::class, 'index']);
Route::get('companies/{id}',     [CompanyController::class, 'show']);
Route::post('companies',         [CompanyController::class, 'store']);
Route::put('companies/{id}',     [CompanyController::class, 'update']);
Route::delete('companies/{id}',  [CompanyController::class, 'destroy']);


// ============================================================
// SERVICES
// ============================================================
Route::prefix('services')->group(function () {
    Route::get('/',                           [ServiceController::class, 'index']);
    Route::get('/slug/{slug}',                [ServiceController::class, 'findBySlug']);
    Route::get('/{id}',                       [ServiceController::class, 'show']);
    Route::post('/',                          [ServiceController::class, 'store']);
    Route::put('/{id}',                       [ServiceController::class, 'update']);
    Route::patch('/{id}/toggle-featured',     [ServiceController::class, 'toggleFeatured']);
    Route::patch('/{id}/toggle-active',       [ServiceController::class, 'toggleActive']);
    Route::delete('/{id}',                    [ServiceController::class, 'destroy']);
    Route::post('/bulk-delete',               [ServiceController::class, 'bulkDelete']);
    Route::post('/reorder',                   [ServiceController::class, 'reorder']);
});

Route::get('services/{serviceId}/features',  [ServiceFeatureController::class, 'index']);
Route::get('service-features',               [ServiceFeatureController::class, 'index']);
Route::get('service-features/{id}',          [ServiceFeatureController::class, 'show']);
Route::post('service-features',              [ServiceFeatureController::class, 'store']);
Route::put('service-features/{id}',          [ServiceFeatureController::class, 'update']);
Route::delete('service-features/{id}',       [ServiceFeatureController::class, 'destroy']);


// ============================================================
// STATIC CONTENT
// ============================================================
Route::get('commitments',          [CommitmentController::class, 'index']);
Route::get('commitments/{id}',     [CommitmentController::class, 'show']);
Route::post('commitments',         [CommitmentController::class, 'store']);
Route::put('commitments/{id}',     [CommitmentController::class, 'update']);
Route::delete('commitments/{id}',  [CommitmentController::class, 'destroy']);

Route::get('process-steps',          [ProcessStepController::class, 'index']);
Route::get('process-steps/{id}',     [ProcessStepController::class, 'show']);
Route::post('process-steps',         [ProcessStepController::class, 'store']);
Route::put('process-steps/{id}',     [ProcessStepController::class, 'update']);
Route::delete('process-steps/{id}',  [ProcessStepController::class, 'destroy']);

Route::get('statistics',          [StatisticController::class, 'index']);
Route::get('statistics/summary',  [StatisticController::class, 'summary']);
Route::get('statistics/{id}',     [StatisticController::class, 'show']);
Route::post('statistics',         [StatisticController::class, 'store']);
Route::put('statistics/{id}',     [StatisticController::class, 'update']);
Route::delete('statistics/{id}',  [StatisticController::class, 'destroy']);

Route::get('why-choose-us-points',          [WhyChooseUsPointController::class, 'index']);
Route::get('why-choose-us-points/{id}',     [WhyChooseUsPointController::class, 'show']);
Route::post('why-choose-us-points',         [WhyChooseUsPointController::class, 'store']);
Route::put('why-choose-us-points/{id}',     [WhyChooseUsPointController::class, 'update']);
Route::delete('why-choose-us-points/{id}',  [WhyChooseUsPointController::class, 'destroy']);

Route::get('team-members',          [TeamMemberController::class, 'index']);
Route::get('team-members/{id}',     [TeamMemberController::class, 'show']);
Route::post('team-members',         [TeamMemberController::class, 'store']);
Route::put('team-members/{id}',     [TeamMemberController::class, 'update']);
Route::delete('team-members/{id}',  [TeamMemberController::class, 'destroy']);

Route::get('testimonials',          [TestimonialController::class, 'index']);
Route::get('testimonials/featured', [TestimonialController::class, 'getFeatured']);
Route::get('testimonials/{id}',     [TestimonialController::class, 'show']);
Route::post('testimonials',         [TestimonialController::class, 'store']);
Route::put('testimonials/{id}',     [TestimonialController::class, 'update']);
Route::delete('testimonials/{id}',  [TestimonialController::class, 'destroy']);


// ============================================================
// SETTINGS
// ============================================================
Route::get('settings/all',            [SiteSettingController::class, 'getAll']);
Route::get('settings/key/{key}/value',[SiteSettingController::class, 'getValue']);
Route::get('settings/key/{key}',      [SiteSettingController::class, 'getByKey']);
Route::get('settings',                [SiteSettingController::class, 'index']);
Route::post('settings',               [SiteSettingController::class, 'store']);
Route::post('settings/bulk-update',   [SiteSettingController::class, 'bulkUpdate']);
Route::get('settings/{id}',           [SiteSettingController::class, 'show']);
Route::put('settings/{id}',           [SiteSettingController::class, 'update']);
Route::delete('settings/{id}',        [SiteSettingController::class, 'destroy']);


// ============================================================
// PORTFOLIO
// ============================================================
Route::get('portfolio',              [PortfolioProjectController::class, 'index']);
Route::get('portfolio/featured',     [PortfolioProjectController::class, 'getFeatured']);
Route::get('portfolio/slug/{slug}',  [PortfolioProjectController::class, 'findBySlug']);
Route::get('portfolio/{id}',         [PortfolioProjectController::class, 'show']);
Route::post('portfolio',             [PortfolioProjectController::class, 'store']);
Route::put('portfolio/{id}',         [PortfolioProjectController::class, 'update']);
Route::delete('portfolio/{id}',      [PortfolioProjectController::class, 'destroy']);


// ============================================================
// CONTACT INQUIRIES
// ============================================================
Route::get('contact-inquiries',                  [ContactInquiryController::class, 'index']);
Route::post('contact-inquiries', [ContactInquiryController::class, 'store']);
Route::get('contact-inquiries/stats',            [ContactInquiryController::class, 'stats']);
Route::get('contact-inquiries/{id}',             [ContactInquiryController::class, 'show']);
Route::put('contact-inquiries/{id}',             [ContactInquiryController::class, 'update']);
Route::patch('contact-inquiries/{id}/status',    [ContactInquiryController::class, 'updateStatus']);
Route::delete('contact-inquiries/{id}',          [ContactInquiryController::class, 'destroy']);


// ============================================================
// CONTACT MESSAGES (Public form + Admin)
// ============================================================
Route::post('/contact',                          [ContactMessageController::class, 'store']);
Route::post('/contact-messages',                 [ContactMessageController::class, 'store']);
Route::get('/contact-messages',                  [ContactMessageController::class, 'index']);
Route::get('/contact-messages/stats',            [ContactMessageController::class, 'stats']);
Route::get('/contact-messages/{id}',             [ContactMessageController::class, 'show']);
Route::put('/contact-messages/{id}',             [ContactMessageController::class, 'update']);
Route::patch('/contact-messages/{id}/status',    [ContactMessageController::class, 'updateStatus']);
Route::delete('/contact-messages/{id}',          [ContactMessageController::class, 'destroy']);
Route::post('/contact-messages/bulk-delete',     [ContactMessageController::class, 'bulkDelete']);


// ============================================================
// CAREER
// ============================================================
Route::post('/job-alerts',              [JobAlertController::class, 'store']);
Route::post('/job-alerts/unsubscribe',  [JobAlertController::class, 'unsubscribe']);
Route::get('/job-alerts/options',       [JobAlertController::class, 'getOptions']);

Route::get('/departments',                       [DepartmentController::class, 'index']);
Route::get('/departments/options',               [DepartmentController::class, 'getOptions']);
Route::get('/departments/slug/{slug}',           [DepartmentController::class, 'findBySlug']);
Route::get('/departments/{id}',                  [DepartmentController::class, 'show']);
Route::post('/departments',                      [DepartmentController::class, 'store']);
Route::put('/departments/{id}',                  [DepartmentController::class, 'update']);
Route::delete('/departments/{id}',               [DepartmentController::class, 'destroy']);

Route::get('/job-categories',                    [JobCategoryController::class, 'index']);
Route::get('/job-categories/options',            [JobCategoryController::class, 'getOptions']);
Route::get('/job-categories/{id}',               [JobCategoryController::class, 'show']);
Route::post('/job-categories',                   [JobCategoryController::class, 'store']);
Route::put('/job-categories/{id}',               [JobCategoryController::class, 'update']);
Route::delete('/job-categories/{id}',            [JobCategoryController::class, 'destroy']);

Route::get('/jobs',                  [JobOpeningController::class, 'index']);
Route::get('/jobs/stats',            [JobOpeningController::class, 'stats']);
Route::get('/jobs/slug/{slug}',      [JobOpeningController::class, 'findBySlug']);
Route::get('/jobs/{id}',             [JobOpeningController::class, 'show']);
Route::post('/jobs/apply',           [JobApplicationController::class, 'store']);
Route::post('/jobs',                 [JobOpeningController::class, 'store']);
Route::put('/jobs/{id}',             [JobOpeningController::class, 'update']);
Route::delete('/jobs/{id}',          [JobOpeningController::class, 'destroy']);

Route::get('/applications',              [JobApplicationController::class, 'index']);
Route::get('/applications/stats',        [JobApplicationController::class, 'stats']);
Route::get('/applications/{id}',         [JobApplicationController::class, 'show']);
Route::put('/applications/{id}',         [JobApplicationController::class, 'update']);
Route::patch('/applications/{id}/status',[JobApplicationController::class, 'updateStatus']);
Route::delete('/applications/{id}',      [JobApplicationController::class, 'destroy']);

Route::get('/perks',          [PerkBenefitController::class, 'index']);
Route::get('/perks/{id}',     [PerkBenefitController::class, 'show']);
Route::post('/perks',         [PerkBenefitController::class, 'store']);
Route::put('/perks/{id}',     [PerkBenefitController::class, 'update']);
Route::delete('/perks/{id}',  [PerkBenefitController::class, 'destroy']);

Route::get('/locations',          [LocationController::class, 'index']);
Route::get('/locations/{id}',     [LocationController::class, 'show']);
Route::post('/locations',         [LocationController::class, 'store']);
Route::put('/locations/{id}',     [LocationController::class, 'update']);
Route::delete('/locations/{id}',  [LocationController::class, 'destroy']);

Route::get('/interviews',          [InterviewScheduleController::class, 'index']);
Route::get('/interviews/{id}',     [InterviewScheduleController::class, 'show']);
Route::post('/interviews',         [InterviewScheduleController::class, 'store']);
Route::put('/interviews/{id}',     [InterviewScheduleController::class, 'update']);
Route::delete('/interviews/{id}',  [InterviewScheduleController::class, 'destroy']);

Route::prefix('career-settings')->group(function () {
    Route::get('/page',        [CareerSettingController::class, 'getCareerPage']);
    Route::get('/all',         [CareerSettingController::class, 'getAll']);
    Route::get('/',            [CareerSettingController::class, 'index']);
    Route::post('/',           [CareerSettingController::class, 'store']);
    Route::get('/{id}',        [CareerSettingController::class, 'show']);
    Route::put('/{id}',        [CareerSettingController::class, 'update']);
    Route::delete('/{id}',     [CareerSettingController::class, 'destroy']);
    Route::post('/bulk-update',[CareerSettingController::class, 'bulkUpdate']);
});


// ============================================================
// PRODUCTS
// ============================================================
Route::prefix('products')->group(function () {
    Route::get('/',                      [ProductController::class, 'index']);
    Route::get('/options',               [ProductController::class, 'getOptions']);
    Route::get('/slug/{slug}',           [ProductController::class, 'findBySlug']);
    Route::get('/{id}',                  [ProductController::class, 'show']);
    Route::post('/',                     [ProductController::class, 'store']);
    Route::put('/{id}',                  [ProductController::class, 'update']);
    Route::patch('/{id}/toggle-active',  [ProductController::class, 'toggleActive']);
    Route::delete('/{id}',               [ProductController::class, 'destroy']);
    Route::post('/bulk-delete',          [ProductController::class, 'bulkDelete']);
    Route::post('/reorder',              [ProductController::class, 'reorder']);
});

Route::get('/product-features',                    [ProductFeatureController::class, 'index']);
Route::get('/products/{productId}/features',       [ProductFeatureController::class, 'forProduct']);
Route::get('/product-features/{id}',               [ProductFeatureController::class, 'show']);
Route::post('/products/{productId}/features', [ProductFeatureController::class, 'store']);
Route::put('/products/{productId}/features/{id}',  [ProductFeatureController::class, 'update']);
Route::delete('/products/{productId}/features/{id}',            [ProductFeatureController::class, 'destroy']);

 // Add these feature routes
    Route::prefix('{productId}/features')->group(function () {
        Route::get('/', [ProductFeatureController::class, 'index']);
        Route::post('/', [ProductFeatureController::class, 'store']);
        Route::delete('/{featureId}', [ProductFeatureController::class, 'destroy']);
    });

Route::get('/product-images',                      [ProductImageController::class, 'index']);
Route::get('/products/{productId}/images',         [ProductImageController::class, 'forProduct']);
Route::get('/product-images/{id}',                 [ProductImageController::class, 'show']);
Route::post('/product-images',                     [ProductImageController::class, 'store']);
Route::put('/product-images/{id}',                 [ProductImageController::class, 'update']);
Route::delete('/product-images/{id}',              [ProductImageController::class, 'destroy']);

Route::get('/pricing-tiers',                       [ProductPricingTierController::class, 'index']);
Route::get('/products/{productId}/pricing-tiers',  [ProductPricingTierController::class, 'forProduct']);
Route::get('/pricing-tiers/{id}',                  [ProductPricingTierController::class, 'show']);
Route::post('/pricing-tiers',                      [ProductPricingTierController::class, 'store']);
Route::put('/pricing-tiers/{id}',                  [ProductPricingTierController::class, 'update']);
Route::delete('/pricing-tiers/{id}',               [ProductPricingTierController::class, 'destroy']);

Route::get('/tier-features',                       [TierFeatureController::class, 'index']);
Route::get('/pricing-tiers/{tierId}/features',     [TierFeatureController::class, 'forTier']);
Route::get('/tier-features/{id}',                  [TierFeatureController::class, 'show']);
Route::post('/tier-features',                      [TierFeatureController::class, 'store']);
Route::put('/tier-features/{id}',                  [TierFeatureController::class, 'update']);
Route::delete('/tier-features/{id}',               [TierFeatureController::class, 'destroy']);

Route::get('/related-products',                    [RelatedProductController::class, 'index']);
Route::get('/products/{productId}/related',        [RelatedProductController::class, 'forProduct']);
Route::post('/related-products',                   [RelatedProductController::class, 'store']);
Route::delete('/related-products/{productId}/{relatedId}', [RelatedProductController::class, 'destroy']);
