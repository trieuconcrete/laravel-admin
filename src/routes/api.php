<?php

use App\Http\Controllers\HomeController;

Route::post('/contact', [HomeController::class, 'contact']);
