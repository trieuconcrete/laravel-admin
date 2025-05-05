<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResetPasswordController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\PromptController;

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Prompts CRUD
    Route::resource('prompts', PromptController::class);
    Route::post('prompts/{prompt}/toggle-public', [PromptController::class, 'togglePublic'])
        ->name('prompts.toggle-public');
});

Route::get('admin/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');

Route::post('admin/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');

Route::get('admin/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');

Route::post('admin/reset-password', [ResetPasswordController::class, 'reset'])->name('admin.password.update');
