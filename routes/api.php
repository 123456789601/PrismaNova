<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\VentaApiController;

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

Route::middleware(['web','auth'])->group(function () {
    Route::get('/productos', [ProductApiController::class,'index']);
    Route::get('/productos/{producto}', [ProductApiController::class,'show']);
    Route::post('/productos', [ProductApiController::class,'store'])->middleware('role:admin,cajero,bodeguero');
    Route::post('/productos/{producto}', [ProductApiController::class,'update'])->middleware('role:admin,cajero,bodeguero');
    Route::delete('/productos/{producto}', [ProductApiController::class,'destroy'])->middleware('role:admin,cajero,bodeguero');

    Route::post('/ventas', [VentaApiController::class,'store'])->middleware('role:admin,cajero,cliente');
});
