<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/produtos', [ProdutoController::class, 'index']);
Route::post('/produtos/create', [ProdutoController::class, 'store']);
Route::get('/produtos/{id}', [ProdutoController::class, 'show']);
Route::put('/produtos/update/{id}', [ProdutoController::class, 'update']);
Route::delete('/produtos/{id}', [ProdutoController::class, 'destroy']);



