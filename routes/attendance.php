<?php

use App\Http\Controllers\Attendance\LeaveAbsenceController;
use Illuminate\Support\Facades\Route;

Route::resource('leave-absence', LeaveAbsenceController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
