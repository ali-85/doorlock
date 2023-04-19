<?php

use App\Http\Controllers\Payroll\ApprovePaymentController;
use App\Http\Controllers\Payroll\ListEmployeeController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\RequestPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('employee', [ListEmployeeController::class, 'getIndex'])->name('payroll.employee.list');
Route::post('employee/pdf', [ListEmployeeController::class, 'getDownloadPdf'])->name('payroll.download.pdf');
Route::get('employee/{id}', [PayrollController::class, 'getIndex'])->name('payroll.employee.index');
Route::post('employee/{id}/store', [PayrollController::class, 'store'])->name('payroll.employee.store');
Route::get('employee/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.employee.edit');
Route::put('employee/{id}/update', [PayrollController::class, 'update'])->name('payroll.employee.update');
Route::resource('request-payment', RequestPaymentController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
Route::get('request-payment/{id}/pdf', [RequestPaymentController::class, 'getDownloadPDF'])->name('request.download.pdf');
Route::get('request-payment/{id}/excel', [RequestPaymentController::class, 'getDownloadExcel'])->name('request.download.excel');

//Approve Payment
Route::get('approve-payment', [ApprovePaymentController::class, 'index'])->name('approve.payment.index');
Route::put('approve-payment/{id}', [ApprovePaymentController::class, 'approve'])->name('approve.payment.update');
