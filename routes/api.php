<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

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


////////////////// AUTH /////////////////
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);


Route::group(["middleware" => "auth:sanctum"], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

});


//////////////// product ///////////////
Route::get('/show_markets',[ProductController::class,'show_markets']);
Route::post('/show_products_in_market',[ProductController::class,'show_products_in_market']);
Route::post('/show_product_detaild',[ProductController::class,'show_product_detaild']);


Route::group(["middleware" => "auth:sanctum"], function () {

    Route::post('/add_to_cart', [ProductController::class, 'add_to_cart']);
    Route::get('/show_cart', [ProductController::class, 'show_cart']);
    Route::post('/create_order', [ProductController::class, 'create_order']);


});
