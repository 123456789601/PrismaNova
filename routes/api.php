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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web'])->group(function () {
    Route::get('/productos', [ProductApiController::class,'index']); // Public access for catalog
    Route::get('/productos/{producto}', [ProductApiController::class,'show']);
    
    Route::middleware('auth')->group(function() {
        Route::post('/productos', [ProductApiController::class,'store'])->name('api.productos.store')->middleware('role:admin,cajero,bodeguero');
        Route::post('/productos/{producto}', [ProductApiController::class,'update'])->middleware('role:admin,cajero,bodeguero');
        Route::delete('/productos/{producto}', [ProductApiController::class,'destroy'])->middleware('role:admin,cajero,bodeguero');
    
        Route::post('/ventas', [VentaApiController::class,'store'])->middleware('role:admin,cajero,cliente');
        Route::get('/ventas', [VentaApiController::class,'index'])->middleware('role:admin,cajero');
        Route::get('/ventas/export', [VentaApiController::class,'exportCsv'])->middleware('role:admin,cajero');
    });
});
