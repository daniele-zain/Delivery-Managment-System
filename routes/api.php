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

    Route::post('/search', [ProductController::class, 'search']);
    Route::get('/show_order/{order_id}', [ProductController::class, 'show_order']);
    Route::get('/show_all_orders', [ProductController::class, 'show_all_orders']);

    Route::get('/profile', [ProductController::class, 'show_profile']);
    Route::put('/profile', [ProductController::class, 'edit_profile']);

    Route::post('/favorites/add/{product_id}', [ProductController::class, 'addToFavorites']);
    Route::delete('/favorites/remove/{product_id}', [ProductController::class, 'removeFromFavorites']);
    Route::get('/favorites', [ProductController::class, 'getFavorites']);

    Route::put('/order/{order_id}', [ProductController::class, 'edit_order']);
    Route::delete('/order/{order_id}', [ProductController::class, 'delete_order']);


});
