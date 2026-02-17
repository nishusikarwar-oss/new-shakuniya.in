<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// ============ ADMIN ROUTES ============
Route::prefix('admin')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStatsCards'])->name('admin.dashboard.stats');
    Route::get('/dashboard/sidebar-counts', [DashboardController::class, 'getSidebarCounts'])->name('admin.dashboard.sidebar');
    
    // // Admin Modules
    // Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    // Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
    // Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    // Route::get('/services', [ServiceController::class, 'index'])->name('admin.services');
    // Route::get('/faq', [FaqController::class, 'index'])->name('admin.faq');
    // Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery');
    // Route::get('/blogs', [BlogController::class, 'index'])->name('admin.blogs');
    // Route::get('/career', [CareerController::class, 'index'])->name('admin.career');
});

