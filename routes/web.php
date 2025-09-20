<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAppController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('mini.home');
});

Route::middleware(['web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');

    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::put('/payments/{payment}/approve', [\App\Http\Controllers\Admin\PaymentController::class, 'approve'])->name('payments.approve');
    Route::put('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
});

// Mini app (mobile-first)
Route::prefix('mini')->name('mini.')->group(function () {
    Route::get('/', [UserAppController::class, 'home'])->name('home');
    Route::get('/orders', [UserAppController::class, 'orders'])->name('orders');
    Route::get('/cart', [UserAppController::class, 'cart'])->name('cart');
    Route::get('/profile', [UserAppController::class, 'profile'])->name('profile');
    Route::get('/history', [UserAppController::class, 'history'])->name('history');
    Route::post('/find', [UserAppController::class, 'findProduct'])->name('find');
    Route::get('/product', [UserAppController::class, 'productPage'])->name('product');
    Route::post('/freight', [UserAppController::class, 'freightEstimate'])->name('freight');
    Route::post('/profile/language', [UserAppController::class, 'updateLanguage'])->name('profile.language');
    Route::post('/profile/credentials', [UserAppController::class, 'updateCredentials'])->name('profile.credentials');
    
    // Cart routes
    Route::post('/cart/add', [UserAppController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [UserAppController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/update-quantity', [UserAppController::class, 'updateCartQuantity'])->name('cart.update-quantity');
    Route::post('/cart/view-product', [UserAppController::class, 'viewProductFromCart'])->name('cart.view-product');
    
    // Order routes
    Route::get('/checkout', [UserAppController::class, 'checkout'])->name('checkout');
    Route::post('/order/create', [UserAppController::class, 'createOrder'])->name('order.create');
});
