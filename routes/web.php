<?php

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

Route::get('/', [\App\Http\Controllers\AssinaturaController::class, 'view']);
Route::post('/iniciar-assinatura', [\App\Http\Controllers\AssinaturaController::class, 'iniciarAssinatura']);
Route::post('/finalizar-assinatura', [\App\Http\Controllers\AssinaturaController::class, 'finalizarAssinatura']);
