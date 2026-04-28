<?php

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::post('/log-activity', [ActivityController::class, 'store']);
