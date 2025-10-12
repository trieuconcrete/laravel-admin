<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverShipmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Vehicles API routes
Route::get('/vehicles/by-car-rental', [VehicleController::class, 'getByCarRental'])
    ->name('api.vehicles.by-car-rental');

Route::get('/vehicles/get-driver-by-vehicle', [VehicleController::class, 'getDriverByVehicle']);

Route::prefix('driver')->group(function () {
    Route::post('login', [AuthController::class, 'loginDriver']);
});

Route::get('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/driver/shipments', [DriverShipmentController::class, 'index']);
    Route::get('/driver/shipments/{shipment}', [DriverShipmentController::class, 'show']);
});
