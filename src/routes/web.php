<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\HomepageController;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PriceQuoteController;
use App\Http\Controllers\Admin\ResetPasswordController;
use App\Http\Controllers\Admin\ForgotPasswordController;

Route::get('/', [HomepageController::class, 'index'])->name('homepage');
Route::get('/trangchu', [HomepageController::class, 'index1'])->name('homepage1');
Route::get('/trangchu2', [HomepageController::class, 'index1'])->name('homepage2');

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
    Route::get('users/{user}/export-salary', [UserController::class, 'exportSalary'])->name('users.export-salary');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/export-invoice', [CustomerController::class, 'exportInvoice'])->name('customers.export-invoice');
    Route::resource('contracts', ContractController::class);
    Route::resource('quotes', PriceQuoteController::class);
    Route::resource('shipments', ShipmentController::class);
    Route::resource('salary', SalaryController::class);
    
    // Quản lý cài đặt
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/update', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/reset', [\App\Http\Controllers\Admin\SettingController::class, 'resetToDefault'])->name('settings.reset');
    Route::get('settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
});


Route::get('admin/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');

Route::post('admin/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');

Route::get('admin/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');

Route::post('admin/reset-password', [ResetPasswordController::class, 'reset'])->name('admin.password.update');
