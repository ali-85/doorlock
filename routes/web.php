<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'guest'], function(){
    Route::get('/', [AuthController::class, 'getIndex'])->name('auth.index');
    Route::post('/auth', [AuthController::class, 'postLogin'])->name('auth.login');
});

Route::group(['middleware' => 'auth'], function(){
    Route::get('/logout', [AuthController::class, 'getLogout'])->name('auth.logout');
    Route::get('/dashboard', [DashboardController::class, 'getIndex'])->name('dashboard');

    //Prefix: Admin
    Route::group(['prefix' => 'masterdata'], function(){
        require __DIR__.'/masterdata.php';
    });

    //Prefix: Device
    Route::group(['prefix' => 'device'], function(){
        require __DIR__.'/device.php';
    });

    //Prefix: Attendance
    Route::group(['prefix' => 'attendance'], function(){
        require __DIR__.'/attendance.php';
    });

    //Prefix: Report
    Route::group(['prefix' => 'report'], function(){
        require __DIR__.'/report.php';
    });

    //Prefix: Payroll
    Route::group(['prefix' => 'payroll'], function(){
        require __DIR__.'/payroll.php';
    });
});
