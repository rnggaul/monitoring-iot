<?php

use App\Http\Controllers\Api\ActivityController;
use Illuminate\Support\Facades\Route;

Route::post('/log-activity', [ActivityController::class, 'store']);