<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\SettingController;

use App\Http\Controllers\Shop\CustomerController as ShopCustomerController;

Route::post('/login', [LoginController::class, 'apiLogin']);
Route::post('/register', [RegisterController::class, 'apiRegister']);
Route::post('/logout', [LoginController::class, 'apiLogout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    // ─── Shop Routes ─────────────────────────────────────────────
    Route::middleware(['role:shop'])->prefix('shop')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Shop\DashboardController::class, 'apiIndex']);

        Route::get('orders', [ShopOrderController::class, 'apiIndex']);
        Route::get('orders/create', [ShopOrderController::class, 'apiCreate']);
        Route::post('orders', [ShopOrderController::class, 'apiStore']);
        Route::get('orders/{order}', [ShopOrderController::class, 'apiShow']);
        Route::patch('/orders/{order}/status', [ShopOrderController::class, 'apiUpdateStatus']);

        // Shop Customers
        Route::get('/customers', [ShopCustomerController::class, 'apiIndex']);
        Route::post('/customers', [ShopCustomerController::class, 'apiStore']);

        // Shop Settings & Products
        Route::get('/settings', [SettingController::class, 'apiIndex']);
        Route::post('/settings', [SettingController::class, 'apiUpdateShop']); // using post since we might upload files
        Route::put('/settings/colors', [SettingController::class, 'apiUpdateColor']);
        Route::get('/settings/user', [SettingController::class, 'apiUserSettings']);
        Route::put('/settings/user', [SettingController::class, 'apiUpdateUser']);

        Route::get('/settings/products', [SettingController::class, 'apiProducts']);
        Route::post('/settings/products', [SettingController::class, 'apiStoreProduct']);
        Route::put('/settings/products/{product}', [SettingController::class, 'apiUpdateProduct']);
        Route::patch('/settings/products/{product}/toggle', [SettingController::class, 'apiToggleProduct']);
        Route::delete('/settings/products/{product}', [SettingController::class, 'apiDestroyProduct']);
    });

    // ─── Customer Routes ─────────────────────────────────────────
    Route::middleware(['role:customer'])->prefix('customer')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'apiIndex']);

        Route::get('orders', [App\Http\Controllers\Customer\OrderController::class, 'apiIndex']);
        Route::get('orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'apiShow']);
    });
});
