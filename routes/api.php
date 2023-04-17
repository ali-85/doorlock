<?php

use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/', [device::class, 'index']);
Route::post('/getroom', [device::class, 'getroom']);
Route::post('/registerdev', [device::class, 'registerdev']);
Route::post('/access_room', [device::class, 'access_room']);
Route::post('/remarks/{id}', [device::class, 'remarks'])->name('withremarks');
Route::post('/counter/{id}', [device::class, 'counter'])->name('withcounter');
Route::post('/capture',[device::class,'checkCapture'])->name('checkCapture');
Route::post('/capture/{id}', [device::class, 'capture'])->name('withcapture');

// absence
Route::get('/datetime', [AbsenceController::class, 'datetime']);
Route::post('/absensirfidcam', [AbsenceController::class, 'absensi']);
Route::post('/getmoderfidcam ', [AbsenceController::class, 'getmode']);
Route::post('/addcardrfidcam ', [AbsenceController::class, 'addcardrfidcam']);
