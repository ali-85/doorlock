<?php

use App\Http\Controllers\Report\AbsenceController;
use App\Http\Controllers\Report\DoorlockController;
use App\Http\Controllers\Report\OutMonitoringController;
use Illuminate\Support\Facades\Route;

Route::resource('absence', AbsenceController::class)->only(['index', 'show']);
// Route::post('absence/list', [AbsenceController::class, 'index'])->name('absence.list');
Route::resource('doorlock', DoorlockController::class)->only(['index', 'show']);
Route::resource('outmonitoring', OutMonitoringController::class)->only(['index', 'show']);
