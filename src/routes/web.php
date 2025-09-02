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
use App\Http\Controllers\Admin\CarRentalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PriceQuoteController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\Admin\ResetPasswordController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\Admin\ShipmentDeductionTypeController;


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
    Route::get('users/{user}/export-office-salary', [UserController::class, 'exportOfficeSalary'])->name('users.export-office-salary');
    Route::prefix('users/{user}')->group(function () {
        Route::post('salary-advance-requests', [UserController::class, 'createSalaryAdvanceRequest'])
            ->name('users.salary-advance-requests.store')
            ->middleware('App\Http\Middleware\CheckSalaryAdvanceRequestPermission');
        Route::get('salary-advance-requests', [UserController::class, 'getSalaryAdvanceRequests'])->name('users.salary-advance-requests.index');
        Route::put('salary-advance-requests/{request}', [UserController::class, 'updateSalaryAdvanceRequest'])
            ->name('users.salary-advance-requests.update')
            ->middleware('App\Http\Middleware\CheckSalaryAdvanceRequestPermission');
    });

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/export-invoice', [CustomerController::class, 'exportInvoice'])->name('customers.export-invoice');
    Route::post('customers/{customer}/summarize-monthly-report', [CustomerController::class, 'summarizeMonthlyReport'])->name('customers.summarize-monthly-report');
    Route::post('customers/{customer}/store-transaction', [PaymentTransactionController::class, 'storeTransaction'])->name('customers.store-transaction');
    Route::get('customers/{customer}/transactions', [PaymentTransactionController::class, 'getTransactions'])->name('customers.transactions');
    Route::get('customers/{customer}/transactions/{transaction}/edit', [PaymentTransactionController::class, 'editTransaction'])->name('customers.edit-transaction');
    Route::put('customers/{customer}/transactions/{transaction}', [PaymentTransactionController::class, 'updateTransaction'])->name('customers.update-transaction');
    Route::delete('customers/{customer}/transactions/{transaction}', [PaymentTransactionController::class, 'destroyTransaction'])->name('customers.destroy-transaction');
    Route::get('customers/{customer}/debt-summary', [CustomerController::class, 'getDebtSummary'])->name('customers.debt-summary');
    Route::resource('contracts', ContractController::class);
    Route::resource('quotes', PriceQuoteController::class);
    Route::resource('car-rental', CarRentalController::class);
    Route::get('car-rental/{id}/shipment-create', [CarRentalController::class, 'shipmentCreate'])->name('car-rental.shipment-create');
    Route::post('car-rental/store-vehicle-log', [CarRentalController::class, 'storeCarRentalVehicleLog'])->name('car-rental.store-vehicle-log');
    Route::get('car-rental/shipment/{shipmentId}/edit-vehicle-log', [CarRentalController::class, 'editShipmentVehicleLog'])->name('car-rental.edit-vehicle-log');
    Route::put('car-rental/shipment/{shipmentId}/update-vehicle-log', [CarRentalController::class, 'updateShipmentVehicleLog'])->name('car-rental.update-vehicle-log');
    Route::delete('car-rental/shipment/{shipmentId}/destroy-vehicle-log', [CarRentalController::class, 'destroyShipmentVehicleLog'])->name('car-rental.destroy-vehicle-log');
    
    // Backward compatibility routes
    Route::get('car-rental/shipment/{shipmentId}/edit-vehicle-log-from-shipment', [CarRentalController::class, 'editCarRentalVehicleLogFromShipment'])->name('car-rental.edit-vehicle-log-from-shipment');
    Route::delete('car-rental/shipment/{shipmentId}/destroy-vehicle-log-from-shipment', [CarRentalController::class, 'destroyCarRentalVehicleLogFromShipment'])->name('car-rental.destroy-vehicle-log-from-shipment');
    
    Route::get('car-rental/{car_rental_id}/download-vehicle-log', [CarRentalController::class, 'downloadVehicleLog'])->name('car-rental.download-vehicle-log');
    Route::get('car-rental/{id}/debt-summary', [CarRentalController::class, 'showDebtSummary'])->name('car-rental.debt-summary');
    Route::get('car-rental/{id}/export-debt-summary', [CarRentalController::class, 'exportDebtSummary'])->name('car-rental.export-debt-summary');
    
    // Car rental tổng kết công nợ routes
    Route::post('car-rental/{carRental}/summarize-report', [CarRentalController::class, 'summarizeReport'])->name('car-rental.summarize-report');
    Route::get('car-rental/{carRental}/export-summary', [CarRentalController::class, 'exportSummary'])->name('car-rental.export-summary');
    
    Route::resource('shipments', ShipmentController::class);
    
    // Shipment Report routes
    Route::prefix('shipment-reports')->name('shipment-reports.')->group(function () {
        Route::post('{customer}/summarize', [\App\Http\Controllers\Admin\ShipmentReportController::class, 'summarize'])->name('summarize');
        Route::get('{customer}/export', [\App\Http\Controllers\Admin\ShipmentReportController::class, 'export'])->name('export');
        Route::get('{customer}/data', [\App\Http\Controllers\Admin\ShipmentReportController::class, 'getReportData'])->name('data');
    });
    
    Route::resource('salary', SalaryController::class);
    Route::post('salary/sync', [SalaryController::class, 'sync'])->name('salary.sync');
    Route::post('salary/{salary}/pay', [SalaryController::class, 'processPayment'])->name('salary.pay');
    Route::post('salary/process-advance-request/{requestId}', [SalaryController::class, 'processAdvanceRequest'])
        ->name('salary.process-advance-request')
        ->middleware('App\Http\Middleware\CheckSalaryAdvanceRequestPermission');
    
    // Quản lý cài đặt
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/update/{tab?}', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/reset', [\App\Http\Controllers\Admin\SettingController::class, 'resetToDefault'])->name('settings.reset');
    Route::get('settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');

    // Vehicle Types API routes
    Route::prefix('vehicle-types')->name('vehicle-types.')->group(function () {
        Route::get('/', [VehicleTypeController::class, 'index'])->name('index');
        Route::post('/', [VehicleTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [VehicleTypeController::class, 'show'])->name('show');
        Route::put('/{id}', [VehicleTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [VehicleTypeController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [VehicleTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/select/options', [VehicleTypeController::class, 'getForSelect'])->name('select-options');
    });

    // Shipment Deduction Types Routes
    Route::prefix('shipment-deduction-types')->name('shipment-deduction-types.')->group(function () {
        // CRUD routes
        Route::get('/', [ShipmentDeductionTypeController::class, 'index'])->name('index');
        Route::get('/create', [ShipmentDeductionTypeController::class, 'create'])->name('create');
        Route::post('/', [ShipmentDeductionTypeController::class, 'store'])->name('store');
        Route::get('/{shipment_deduction_type}', [ShipmentDeductionTypeController::class, 'show'])->name('show');
        Route::get('/{shipment_deduction_type}/edit', [ShipmentDeductionTypeController::class, 'edit'])->name('edit');
        Route::put('/{shipment_deduction_type}', [ShipmentDeductionTypeController::class, 'update'])->name('update');
        Route::delete('/{shipment_deduction_type}', [ShipmentDeductionTypeController::class, 'destroy'])->name('destroy');
        
        // Additional routes
        Route::post('/update-order', [ShipmentDeductionTypeController::class, 'updateOrder'])->name('update-order');
        Route::post('/bulk-delete', [ShipmentDeductionTypeController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-update-status', [ShipmentDeductionTypeController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    });
    
    // API routes inside admin
    Route::get('/api/vehicles/by-car-rental', [VehicleController::class, 'getByCarRental'])
        ->name('api.vehicles.by-car-rental');
});


Route::get('admin/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');

Route::post('admin/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');

Route::get('admin/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');

Route::post('admin/reset-password', [ResetPasswordController::class, 'reset'])->name('admin.password.update');

// Debug route (remove in production)
Route::get('/debug/debt', function () {
    $totalReported = \App\Models\ShipmentReport::sum('total_amount');
    $reportCount = \App\Models\ShipmentReport::count();
    
    $totalPaid = \Illuminate\Support\Facades\DB::table('transactions')
        ->join('payments', 'transactions.payment_id', '=', 'payments.id')
        ->where('transactions.type', \App\Models\Transaction::TYPE_INCOME)
        ->sum('transactions.amount');
    
    $transactionCount = \Illuminate\Support\Facades\DB::table('transactions')
        ->join('payments', 'transactions.payment_id', '=', 'payments.id')
        ->where('transactions.type', \App\Models\Transaction::TYPE_INCOME)
        ->count();
    
    // Calculate debt with correct logic
    if ($totalReported < 0) {
        // Trường hợp hóa đơn âm: Công nợ = |tổng hóa đơn| - đã thanh toán
        $debt = abs($totalReported) - $totalPaid;
        $debtExplanation = "Hóa đơn âm: |{$totalReported}| - {$totalPaid} = " . number_format($debt);
    } else {
        // Trường hợp bình thường: Công nợ = tổng hóa đơn - đã thanh toán
        $debt = $totalReported - $totalPaid;
        $debtExplanation = "Hóa đơn dương: {$totalReported} - {$totalPaid} = " . number_format($debt);
    }
    
    // Calculate directly from completed shipments
    $totalFromShipments = \Illuminate\Support\Facades\DB::table('shipments')
        ->where('status', 'completed') // Chỉ tính cho shipment đã hoàn thành
        ->selectRaw('SUM(
            (COALESCE(trip_count, 1) * COALESCE(unit_price, 0)) - 
            (SELECT COALESCE(SUM(amount), 0) FROM shipment_deductions WHERE shipment_id = shipments.id)
        ) as total_value')
        ->value('total_value') ?: 0;
    
    $shipmentCount = \App\Models\Shipment::count();
    
    return [
        'shipment_reports' => [
            'total_amount' => number_format($totalReported),
            'count' => $reportCount,
        ],
        'transactions' => [
            'total_paid' => number_format($totalPaid),
            'count' => $transactionCount,
        ],
        'debt_calculation' => [
            'debt' => number_format($debt),
            'explanation' => $debtExplanation,
            'status' => $debt > 0 ? 'Khách hàng nợ công ty' : ($debt < 0 ? 'Công ty nợ khách hàng' : 'Đã cân bằng'),
        ],
        'alternative_calculation' => [
            'total_from_shipments' => number_format($totalFromShipments),
            'shipment_count' => $shipmentCount,
            'debt_from_shipments' => number_format($totalFromShipments - $totalPaid),
        ],
        'explanation' => [
            'shipment_reports_vs_shipments' => 'Nếu ShipmentReport = 0 nhưng có nhiều Shipment, có nghĩa là chưa tổng kết bảng kê',
            'debt_logic_positive' => 'Hóa đơn dương: Công nợ = Tổng bảng kê - Tổng thanh toán',
            'debt_logic_negative' => 'Hóa đơn âm: Công nợ = |Tổng bảng kê| - Tổng thanh toán (hóa đơn âm có thể do điều chỉnh/hoàn trả)',
            'current_case' => $totalReported < 0 ? 'Đang áp dụng logic hóa đơn âm' : 'Đang áp dụng logic hóa đơn dương',
            'example' => 'VD: Nếu bảng kê = -6,264,499,977 và thanh toán = 4,020,000,000 thì nợ = 6,264,499,977 - 4,020,000,000 = 2,244,499,977',
        ]
    ];
});
