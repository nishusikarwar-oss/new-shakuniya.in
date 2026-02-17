<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\GalleryImageController;
use App\Http\Controllers\API\JobApplicationController;
use App\Http\Controllers\API\JobOpeningController;
use App\Http\Controllers\API\PerkController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductFeatureController;
use App\Http\Controllers\API\ServicesController;
use App\Http\Controllers\API\ServiceFeatureController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DashboardController;

use App\Http\Controllers\API\FaqController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ✅ PUBLIC ROUTES - SAHI TARIKA
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


// ------------------------------job_application--------------------------------------------------------
//  JOB APPLICATION ROUTES
Route::prefix('job-applications')->group(function() {
    // ✅ SAB ROUTES PUBLIC KARO - ABHI KE LIYE
    Route::post('/', [JobApplicationController::class, 'store']);
    Route::get('/', [JobApplicationController::class, 'index']);
    Route::get('/job/{jobId}', [JobApplicationController::class, 'byJob']);
    Route::get('/{id}', [JobApplicationController::class, 'show']);
    Route::get('/{id}/download-cv', [JobApplicationController::class, 'downloadCV']);
    Route::put('/{id}', [JobApplicationController::class, 'update']);
    Route::delete('/{id}', [JobApplicationController::class, 'destroy']);
});


// -----------------------------job opening-----------------------------

//  JOB OPENINGS ROUTES - PUBLIC
Route::prefix('job-openings')->group(function() {
    

    // Route::post('/', [JobOpeningController::class, 'index']);
    Route::get('/', [JobOpeningController::class, 'index']);
    Route::get('/recent', [JobOpeningController::class, 'recent']);
    Route::get('/locations', [JobOpeningController::class, 'locations']);
    Route::get('/{id}', [JobOpeningController::class, 'show']);
    
    
    // Admin routes - protected
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/', [JobOpeningController::class, 'store']);
        Route::put('/{id}', [JobOpeningController::class, 'update']);
        Route::delete('/{id}', [JobOpeningController::class, 'destroy']);
    });
});
// --------------------------------------------------------------------
// ✅ PERKS ROUTES - PUBLIC
Route::prefix('perks')->group(function() {
    Route::get('/', [PerkController::class, 'index']);        // Paginated perks
    Route::get('/all', [PerkController::class, 'all']);       // All perks without pagination
    Route::get('/recent', [PerkController::class, 'recent']); // Recent perks
    Route::get('/{id}', [PerkController::class, 'show']);     // Single perk
    
    // Admin routes - Protected
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/', [PerkController::class, 'store']);      // Create perk
        Route::put('/{id}', [PerkController::class, 'update']);  // Update perk
        Route::delete('/{id}', [PerkController::class, 'destroy']); // Delete perk
    });
});
// -------------------------------------------------------------------------------------------------
// ✅ PRODUCTS ROUTES - PUBLIC
Route::prefix('products')->group(function() {
    Route::get('/', [ProductController::class, 'index']);           // Paginated products
    Route::get('/all', [ProductController::class, 'all']);          // All products
    Route::get('/recent', [ProductController::class, 'recent']);    // Recent products
    Route::get('/slug/{slug}', [ProductController::class, 'bySlug']); // By slug
    Route::get('/{identifier}', [ProductController::class, 'show']);   // By ID or slug
    
    // Admin routes - Protected
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/', [ProductController::class, 'store']);      // Create product
        Route::put('/{id}', [ProductController::class, 'update']);  // Update product
        Route::delete('/{id}', [ProductController::class, 'destroy']); // Delete product
    });
});
// --------------------------------------------------------------------------
// ✅ PRODUCT FEATURES ROUTES - PUBLIC (READ ONLY)
Route::prefix('product-features')->group(function() {
    // Public routes
    Route::get('/', [ProductFeatureController::class, 'index']);              // All features
    Route::get('/product/{productId}', [ProductFeatureController::class, 'byProduct']); // By product
    Route::get('/{id}', [ProductFeatureController::class, 'show']);           // Single feature
    
    // Admin routes - Protected
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/', [ProductFeatureController::class, 'store']);              // Create single
        Route::post('/bulk', [ProductFeatureController::class, 'storeBulk']);      // Create multiple
        Route::put('/{id}', [ProductFeatureController::class, 'update']);          // Update
        Route::delete('/{id}', [ProductFeatureController::class, 'destroy']);      // Delete single
        Route::post('/delete-bulk', [ProductFeatureController::class, 'destroyBulk']); // Delete multiple
    });
});
// --------------------------------------------------------------------------------------------------------------------?
// ✅ BEST PRACTICE: ALL SERVICES ROUTES TOGETHER
Route::prefix('services')->group(function() {
    
  // 🔓 PUBLIC ROUTES - SPECIFIC ROUTES FIRST!
Route::get('/search', [ServicesController::class, 'search']); // ✅ FIRST
Route::get('/slug/{slug}', [ServicesController::class, 'showBySlug']); // ✅ SECOND
Route::get('/', [ServicesController::class, 'index']); // ✅ THIRD
Route::get('/{id}', [ServicesController::class, 'show']); // ✅ LAST

// 🔒 PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::post('services', [ServicesController::class, 'store']);
    Route::put('services/{id}', [ServicesController::class, 'update']);
    Route::delete('services/{id}', [ServicesController::class, 'destroy']);
});
});
// -----------------------------------------------------------------------------------------------------------------------
// ✅ SERVICE FEATURES API ROUTES
Route::prefix('service-features')->group(function() {
    
    // 🔓 PUBLIC ROUTES
    Route::get('/', [ServiceFeatureController::class, 'index']);
    Route::get('{id}', [ServiceFeatureController::class, 'show']);

    // 🔒 PROTECTED ROUTES
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ServiceFeatureController::class, 'store']);
        Route::put('{id}', [ServiceFeatureController::class, 'update']);
        Route::delete('{id}', [ServiceFeatureController::class, 'destroy']);
    });
});

// ✅ SERVICE-SPECIFIC FEATURES ROUTES
Route::prefix('services')->group(function() {
    
    // 🔓 PUBLIC ROUTES
    Route::get('{serviceId}/features', [ServiceFeatureController::class, 'index']);
    
    // 🔒 PROTECTED ROUTES
    Route::middleware('auth:sanctum')->group(function () {
        // Single feature operations
        Route::post('{serviceId}/features', [ServiceFeatureController::class, 'store']);
        
        // Bulk operations
        Route::post('{serviceId}/features/bulk', [ServiceFeatureController::class, 'bulkStore']);
        Route::put('{serviceId}/features', [ServiceFeatureController::class, 'sync']);
        Route::delete('{serviceId}/features', [ServiceFeatureController::class, 'destroyAll']);
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
