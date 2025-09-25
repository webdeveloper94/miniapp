<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAppController;
use App\Http\Controllers\MiniAuthController;

// Admin authentication routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Redirect root to mini app
Route::get('/', function () {
    return redirect()->route('mini.home');
});

// Admin panel routes (protected)
Route::middleware(['web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin');
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Xizmat haqi boshqaruvi
    Route::post('/settings/service-fees', [\App\Http\Controllers\Admin\SettingController::class, 'storeServiceFee'])->name('settings.service-fees.store');
    Route::put('/settings/service-fees/{serviceFee}', [\App\Http\Controllers\Admin\SettingController::class, 'updateServiceFee'])->name('settings.service-fees.update');
    Route::delete('/settings/service-fees/{serviceFee}', [\App\Http\Controllers\Admin\SettingController::class, 'destroyServiceFee'])->name('settings.service-fees.destroy');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');

    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
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
    // Payment routes
    Route::post('/payment/submit', [UserAppController::class, 'submitPayment'])->name('payment.submit');

    // Mini auth (login password for profile recovery)
    Route::post('/auth/set-password', [MiniAuthController::class, 'setPassword'])->name('auth.setPassword');
    Route::post('/auth/recover', [MiniAuthController::class, 'recover'])->name('auth.recover');
    Route::post('/auth/change-password', [MiniAuthController::class, 'changePassword'])->name('auth.changePassword');
});
