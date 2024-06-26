<?php

use App\Http\Controllers\MasterData\AccountController;
use App\Http\Controllers\MasterData\DepartementController;
use App\Http\Controllers\MasterData\EmployeeController;
use App\Http\Controllers\MasterData\HolidayController;
use App\Http\Controllers\masterData\MenuController;
use App\Http\Controllers\MasterData\RoleController;
use App\Http\Controllers\MasterData\SubdepartementController;
use App\Http\Controllers\masterData\SubmenuController;
use Illuminate\Support\Facades\Route;

Route::resource('account', AccountController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::put('account/reset/{id}', [AccountController::class, 'reset'])->name('account.reset.password');
Route::resource('role', RoleController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('department', DepartementController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('subdepartment', SubdepartementController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('employee', EmployeeController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::get('employee/subdepartement/{id}', [EmployeeController::class, 'getSubdepartement'])->name('employee.subdepartement.option');
Route::resource('holiday', HolidayController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::post('employee/excel', [EmployeeController::class, 'excel'])->name('absensi.karyawan.excel');
Route::resource('menu', MenuController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::resource('submenu', SubmenuController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
