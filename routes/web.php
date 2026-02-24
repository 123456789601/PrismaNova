<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\MisComprasController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\VentaApiController;

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

Route::get('/', function () { return auth()->check() ? redirect()->route('dashboard') : view('landing'); })->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get')->middleware('auth');
Route::get('/registro', [RegistrationController::class, 'show'])->name('register')->middleware('guest');
Route::post('/registro', [RegistrationController::class, 'register'])->name('register.attempt')->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [PerfilController::class,'show'])->name('perfil');
    Route::put('/perfil', [PerfilController::class,'update'])->name('perfil.update');
    Route::post('/perfil/tema', [PerfilController::class,'updateTheme'])->name('perfil.tema.update');
    Route::prefix('api')->group(function () {
        Route::get('productos', [ProductApiController::class,'index']);
        Route::get('productos/{producto}', [ProductApiController::class,'show']);
        Route::post('productos', [ProductApiController::class,'store'])->middleware('role:admin,cajero,bodeguero');
        Route::post('productos/{producto}', [ProductApiController::class,'update'])->middleware('role:admin,cajero,bodeguero');
        Route::delete('productos/{producto}', [ProductApiController::class,'destroy'])->middleware('role:admin,cajero,bodeguero');
        Route::post('ventas', [VentaApiController::class,'store'])->middleware('role:admin,cajero,cliente');
    });
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::resource('cupones', \App\Http\Controllers\CuponController::class);
    });
    Route::middleware(['role:admin,cajero'])->group(function () {
        Route::resource('clientes', ClienteController::class);
    });
    Route::middleware(['role:admin,bodeguero'])->group(function () {
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('categorias', CategoriaController::class);
        Route::resource('productos', ProductoController::class);
        Route::resource('compras', CompraController::class)->only(['index','create','store','show']);
        Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');
    });
    Route::middleware(['role:admin,cajero'])->group(function () {
        Route::resource('ventas', VentaController::class)->only(['index','create','store','show']);
        Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
        Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
        Route::get('caja', [CajaController::class,'index'])->name('caja.index');
        Route::post('caja/abrir', [CajaController::class,'abrir'])->name('caja.abrir');
        Route::get('caja/{caja}', [CajaController::class,'show'])->name('caja.show');
        Route::patch('caja/{caja}/cerrar', [CajaController::class,'cerrar'])->name('caja.cerrar');
        Route::post('caja/{caja}/movimiento', [CajaController::class,'storeMovimiento'])->name('caja.movimiento.store');
    });
    Route::get('reportes', [ReporteController::class,'index'])->name('reportes.index')->middleware('role:admin');
    Route::middleware(['role:admin'])->group(function () {
        Route::get('reportes/sync-logs', [ReporteController::class,'syncLogs'])->name('reportes.sync');
        Route::post('reportes/sync-now', [ReporteController::class,'syncRun'])->name('reportes.sync.run');
        Route::get('reportes/export/ventas', [ReporteController::class,'exportVentasCsv'])->name('reportes.export.ventas');
    });
    Route::middleware(['role:cliente'])->group(function () {
        Route::prefix('tienda')->group(function () {
            Route::get('carrito/json', [\App\Http\Controllers\CarritoController::class,'list'])->name('tienda.carrito.json');
            Route::post('carrito/agregar', [\App\Http\Controllers\CarritoController::class,'add'])->name('tienda.carrito.add');
            Route::patch('carrito/cantidad', [\App\Http\Controllers\CarritoController::class,'update'])->name('tienda.carrito.update');
            Route::delete('carrito/item/{id}', [\App\Http\Controllers\CarritoController::class,'remove'])->name('tienda.carrito.remove');
        });
        Route::get('mis-compras', [MisComprasController::class,'index'])->name('mis-compras.index');
        Route::get('mis-compras/{venta}', [MisComprasController::class,'show'])->name('mis-compras.show');
        Route::get('mis-compras/{venta}/factura', [MisComprasController::class,'factura'])->name('mis-compras.factura');
        Route::get('tienda', [TiendaController::class,'catalogo'])->name('tienda.catalogo');
        Route::get('carrito', [TiendaController::class,'carrito'])->name('tienda.carrito');
    });
});
