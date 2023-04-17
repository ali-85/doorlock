<?php

use App\Http\Controllers\Payroll\ListEmployeeController;
use App\Http\Controllers\Payroll\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('employee', [ListEmployeeController::class, 'getIndex'])->name('payroll.employee.list');
Route::post('employee/pdf', [ListEmployeeController::class, 'getDownloadPdf'])->name('payroll.download.pdf');
Route::get('employee/{id}', [PayrollController::class, 'getIndex'])->name('payroll.employee.index');
Route::post('employee/{id}/store', [PayrollController::class, 'store'])->name('payroll.employee.store');
Route::get('employee/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.employee.edit');
Route::put('employee/{id}/update', [PayrollController::class, 'update'])->name('payroll.employee.update');
