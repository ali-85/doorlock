<?php

use App\Http\Controllers\Device\AttendanceController;
use App\Http\Controllers\Device\DataLocationController;
use App\Http\Controllers\Device\DoorlockController;
use App\Http\Controllers\Device\RemarkController;
use App\Http\Controllers\Device\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::resource('location', DataLocationController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('attendance', AttendanceController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('doorlock', DoorlockController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('remark', RemarkController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('schedule', ScheduleController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
