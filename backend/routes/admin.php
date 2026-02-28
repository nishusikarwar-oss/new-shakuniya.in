<?php
// routes/api.php

use App\Http\Controllers\admin\DashboardController;

use Illuminate\Support\Facades\Route;

// routes/api.php mein yeh add karein

Route::prefix('admin')->name('api.admin.')->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard Stats (Fresh data)
    Route::get('/dashboard/stats', [DashboardController::class, 'getFreshStats'])->name('dashboard.stats');
    
    // Module Specific Stats
    Route::get('/dashboard/module/{module}', [DashboardController::class, 'getModuleCounts'])->name('dashboard.module');
    
    // Individual Stats (if needed)
    Route::get('/dashboard/stats/users', [DashboardController::class, 'getUserStats'])->name('dashboard.stats.users');
    Route::get('/dashboard/stats/products', [DashboardController::class, 'getProductStats'])->name('dashboard.stats.products');
    Route::get('/dashboard/stats/services', [DashboardController::class, 'getServiceStats'])->name('dashboard.stats.services');
    Route::get('/dashboard/stats/blogs', [DashboardController::class, 'getBlogStats'])->name('dashboard.stats.blogs');
    Route::get('/dashboard/stats/career', [DashboardController::class, 'getCareerStats'])->name('dashboard.stats.career');
     Route::get('/dashboard/stats/faqs', [DashboardController::class, 'getFaqStats'])->name('dashboard.stats.faqs');
});
