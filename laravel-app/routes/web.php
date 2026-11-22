<?php

// يجمع هذا الملف مسارات واجهة المتجر وحساب العميل ومساحة البائع ولوحة الإدارة.

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::controller(StorefrontController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/products', 'products')->name('products.index');
    Route::get('/products/{slug}', 'product')->name('products.show');
    Route::get('/artisans/{slug}', 'artisan')->name('artisans.show');
    Route::get('/cart', 'cart')->name('cart.show');
    Route::post('/cart/{slug}', 'addToCart')->name('cart.add');
    Route::put('/cart/{slug}', 'updateCartQuantity')->name('cart.quantity');
    Route::delete('/cart/{slug}', 'removeFromCart')->name('cart.remove');
    Route::post('/favorites/{slug}', [AccountController::class, 'toggleFavorite'])->middleware('auth')->name('favorites.toggle');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [StorefrontController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'storeLogin'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [StorefrontController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->controller(AccountController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'profile')->name('profile');
    Route::put('/profile', 'updateProfile')->name('profile.update');
    Route::get('/addresses', 'addresses')->name('addresses');
    Route::post('/addresses', 'addAddress')->name('addresses.add');
    Route::put('/addresses/{id}', 'updateAddress')->name('addresses.update');
    Route::put('/addresses/{id}/default', 'setDefaultAddress')->name('addresses.default');
    Route::delete('/addresses/{id}', 'deleteAddress')->name('addresses.delete');
    Route::get('/favorites', 'favorites')->name('favorites');
    Route::get('/orders', 'orders')->name('orders');
    Route::get('/orders/{reference}', 'orderDetails')->name('orders.show');
    Route::post('/orders/{reference}/cancel', 'cancelOrder')->name('orders.cancel');
    Route::post('/orders/{reference}/reviews', 'addReview')->name('orders.reviews.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/seller-application', [SellerApplicationController::class, 'show'])->name('seller-application.show');
    Route::post('/seller-application', [SellerApplicationController::class, 'store'])->name('seller-application.store');
    Route::get('/checkout', [AccountController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [AccountController::class, 'placeOrder'])->name('checkout.store');
    Route::get('/orders/{reference}/confirmation', [AccountController::class, 'orderConfirmation'])->name('orders.confirmation');
});

Route::middleware(['auth', 'can:access-seller-workspace'])->prefix('seller')->name('seller.')->controller(SellerController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/products', 'products')->name('products.index');
    Route::get('/products/create', 'createProduct')->name('products.create');
    Route::post('/products', 'storeProduct')->name('products.store');
    Route::get('/products/{slug}/edit', 'editProduct')->name('products.edit');
    Route::put('/products/{slug}', 'updateProduct')->name('products.update');
    Route::get('/orders', 'orders')->name('orders.index');
    Route::get('/orders/{reference}', 'orderDetails')->name('orders.show');
    Route::patch('/orders/{reference}', 'updateOrderStatus')->name('orders.status');
    Route::get('/inventory', 'inventory')->name('inventory');
    Route::get('/reports', 'reports')->name('reports');
    Route::get('/profile', 'profile')->name('profile');
    Route::put('/profile', 'updateProfile')->name('profile.update');
});
