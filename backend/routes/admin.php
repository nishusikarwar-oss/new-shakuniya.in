<?php
// routes/api.php

use App\Http\Controllers\admin\DashboardController;

use Illuminate\Support\Facades\Route;

// Admin Dashboard Routes
Route::prefix('admin')->group(function () {
    
    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStatsCards']);
    Route::get('/dashboard/sidebar-counts', [DashboardController::class, 'getSidebarCounts']);
    Route::get('/dashboard/module-counts', [DashboardController::class, 'getModuleCounts']);
    
    // Module specific stats
    Route::get('/dashboard/users/stats', [DashboardController::class, 'getUserStats']);
    Route::get('/dashboard/products/stats', [DashboardController::class, 'getProductStats']);
    Route::get('/dashboard/blogs/stats', [DashboardController::class, 'getBlogStats']);
    Route::get('/dashboard/career/stats', [DashboardController::class, 'getCareerStats']);
});