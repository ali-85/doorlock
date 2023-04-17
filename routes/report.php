<?php

use App\Http\Controllers\Report\AbsenceController;
use App\Http\Controllers\Report\DoorlockController;
use App\Http\Controllers\Report\OutMonitoringController;
use Illuminate\Support\Facades\Route;

Route::resource('absence', AbsenceController::class)->only(['index', 'show']);
Route::resource('doorlock', DoorlockController::class)->only(['index', 'show']);
Route::resource('outmonitoring', OutMonitoringController::class)->only(['index', 'show']);
