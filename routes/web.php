<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'shop' => redirect()->route('shop.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            'system_owner' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return view('welcome');
});

Auth::routes();

Route::get('/home', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'shop' => redirect()->route('shop.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            'system_owner' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->name('home');

// ─── Shop Routes ─────────────────────────────────────────────
Route::middleware(['auth', 'role:shop'])->prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [App\Http\Controllers\Shop\DashboardController::class, 'index'])->name('dashboard');

    Route::get('orders', [App\Http\Controllers\Shop\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [App\Http\Controllers\Shop\OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [App\Http\Controllers\Shop\OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [App\Http\Controllers\Shop\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Shop\OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('customers', [App\Http\Controllers\Shop\CustomerController::class, 'index'])->name('customers.index');
    Route::post('customers', [App\Http\Controllers\Shop\CustomerController::class, 'store'])->name('customers.store');

    Route::get('settings', [App\Http\Controllers\Shop\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings/shop', [App\Http\Controllers\Shop\SettingController::class, 'updateShop'])->name('settings.update-shop');
    Route::put('settings/color', [App\Http\Controllers\Shop\SettingController::class, 'updateColor'])->name('settings.update-color');
    Route::get('settings/products', [App\Http\Controllers\Shop\SettingController::class, 'products'])->name('settings.products');
    Route::post('settings/products', [App\Http\Controllers\Shop\SettingController::class, 'storeProduct'])->name('settings.store-product');
    Route::patch('settings/products/{product}/toggle', [App\Http\Controllers\Shop\SettingController::class, 'toggleProduct'])->name('settings.toggle-product');
    Route::delete('settings/products/{product}', [App\Http\Controllers\Shop\SettingController::class, 'destroyProduct'])->name('settings.destroy-product');
    Route::get('settings/user', [App\Http\Controllers\Shop\SettingController::class, 'userSettings'])->name('settings.user');
    Route::put('settings/user', [App\Http\Controllers\Shop\SettingController::class, 'updateUser'])->name('settings.update-user');
});

// ─── Customer Routes ─────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');

    Route::get('favorites', [App\Http\Controllers\Customer\FavoriteOrderController::class, 'index'])->name('favorites.index');
    Route::delete('favorites/{favoriteOrder}', [App\Http\Controllers\Customer\FavoriteOrderController::class, 'destroy'])->name('favorites.destroy');

    Route::get('settings', [App\Http\Controllers\Customer\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Customer\SettingController::class, 'update'])->name('settings.update');
});

// ─── System Owner (Admin) Routes ─────────────────────────────
Route::middleware(['auth', 'role:system_owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('tenants', [App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/{shop}', [App\Http\Controllers\Admin\TenantController::class, 'show'])->name('tenants.show');
    Route::patch('tenants/{shop}/toggle', [App\Http\Controllers\Admin\TenantController::class, 'toggleStatus'])->name('tenants.toggle');
});
