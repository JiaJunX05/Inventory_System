<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\GuestController;
use App\Http\Middleware\AdminAuth;

// Guest routes
Route::get('/', [GuestController::class, 'index'])->name('dashboard');
Route::get('/view/{id}', [GuestController::class, 'view'])->name('view');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');

// Admin routes
Route::prefix('admin')->group(function() {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware([AdminAuth::class])->group(function() {
        Route::get('/dashboard', [AuthController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/category/list', [CategoryController::class, 'index'])->name('category.list');
        Route::get('/category/create', [CategoryController::class, 'showCreateForm'])->name('category.create');
        Route::post('/category/create', [CategoryController::class, 'create'])->name('category.create.submit');
        Route::get('/category/update/{id}', [CategoryController::class, 'showEditForm'])->name('category.update');
        Route::put('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update.submit');
        Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

        Route::get('/product/list', [ProductController::class, 'index'])->name('admin.dashboard');
        Route::get('/product/create', [ProductController::class, 'showCreateForm'])->name('product.create');
        Route::post('/product/create', [ProductController::class, 'create'])->name('product.create.submit');
        Route::get('/product/view/{id}', [ProductController::class, 'view'])->name('product.view');
        Route::get('/product/stock/{id}', [ProductController::class, 'showStockForm'])->name('product.stock');
        Route::put('/product/stock/{id}', [ProductController::class, 'stockUpdate'])->name('product.stock.submit');
        Route::get('/product/update/{id}', [ProductController::class, 'showEditForm'])->name('product.update');
        Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update.submit');
        Route::delete('/product/delete/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    });
});
