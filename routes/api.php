<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\SettingController;

use App\Http\Controllers\Shop\CustomerController as ShopCustomerController;
use App\Http\Controllers\Api\PublicShopController;

// ─── Public Routes (QR Ordering) ─────────────────────────────
Route::get('/public/shop/{initial}', [PublicShopController::class, 'getShop']);
Route::get('/public/shop/{initial}/products', [PublicShopController::class, 'getProducts']);
Route::get('/public/orders/{order}', [PublicShopController::class, 'getOrderStatus']);
Route::post('/public/orders', [PublicShopController::class, 'storeOrder']);

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
        Route::post('orders/archive', [ShopOrderController::class, 'apiArchive']);
        Route::get('orders/archived', [ShopOrderController::class, 'apiArchivedIndex']);
        Route::get('orders/archived/day/{date}', [ShopOrderController::class, 'apiArchivedDayDetail']);
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
        Route::put('/settings/password', [SettingController::class, 'apiUpdatePassword']);

        Route::get('/settings/products', [SettingController::class, 'apiProducts']);
        Route::put('/settings/products/reorder', [SettingController::class, 'apiUpdateProductsOrder']);
        Route::post('/settings/products', [SettingController::class, 'apiStoreProduct']);
        Route::put('/settings/products/{product}', [SettingController::class, 'apiUpdateProduct']);
        Route::patch('/settings/products/{product}/toggle', [SettingController::class, 'apiToggleProduct']);
        Route::delete('/settings/products/{product}', [SettingController::class, 'apiDestroyProduct']);
    });

    // ─── System Owner (Admin) Routes ─────────────────────────────
    Route::middleware(['role:system_owner'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'apiIndex']);

        Route::get('/tenants', [App\Http\Controllers\Admin\TenantController::class, 'apiIndex']);
        Route::get('/tenants/{shop}', [App\Http\Controllers\Admin\TenantController::class, 'apiShow']);
        Route::patch('/tenants/{shop}/toggle', [App\Http\Controllers\Admin\TenantController::class, 'apiToggleStatus']);
    });

    // ─── Customer Routes ─────────────────────────────────────────
    Route::middleware(['role:customer'])->prefix('customer')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'apiIndex']);

        Route::get('orders', [App\Http\Controllers\Customer\OrderController::class, 'apiIndex']);
        Route::get('orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'apiShow']);
    });
});
