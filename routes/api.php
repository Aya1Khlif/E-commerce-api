<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

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

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

Route::group(["middleware" => ["auth:api"]], function () {
    Route::get("profile", [AuthController::class, "profile"]);
    // Route::get("refresh", [AuthController::class, "refreshToken"]);
    Route::get("logout", [AuthController::class, "logout"]);
});

// BRAND CRUD
    Route::group(['prefix' => 'brands'], function() {
        Route::controller(BrandController::class)->group(function () {
            Route::get('index', "index");
            Route::get('show/{id}', "show");
            Route::post('store', "store");
            Route::put('update/{id}', "update");
            Route::delete('destroy/{id}', "destroy");
        });
    });

// CATEGORY CRUD
Route::group(['prefix' => 'categories'], function() {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('index', "index");
        Route::get('show/{id}', "show");
        Route::post('store', "store");
        Route::put('update/{id}', "update");
        Route::delete('destroy/{id}', "destroy");
    });
});

// LOCATION CRUD
Route::group(['prefix' => 'locations'], function() {
    Route::controller(LocationController::class)->group(function () {
        Route::get('index', "index");
        Route::post('store', 'store');
        Route::put('update/{id}', 'update');
        Route::delete('destroy/{id}', 'destroy');
    });
});

// PRODUCT CRUD
Route::group(['prefix' => 'products'], function() {
    Route::controller(ProductsController::class)->group(function () {
        Route::get('index', "index");
        Route::get('show/{id}', "show");
        Route::post('store', "store");
        Route::put('update/{id}', "update");
        Route::delete('destroy/{id}', "destroy");
    });
});

// ORDERS CRUD
Route::group(['prefix' => 'orders'], function() {
    Route::controller(OrdersController::class)->group(function () {
        Route::get('index', "index");
        Route::get('show/{id}', "show");
        Route::post('store', "store");
        Route::get('get_order_items/{id}', "get_order_items");
        Route::get('get_user_id/{id}', "get_user_id");
        Route::post('change_order_status/{id}', "change_order_status");
        Route::post('update/{id}', "update");
        Route::delete('destroy/{id}', "destroy");
    });
});
