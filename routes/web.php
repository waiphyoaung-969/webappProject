<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'storefront'])->name('store.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id')->name('store.products.show');
Route::get('/admin/products', [ProductController::class, 'adminIndex'])->name('admin.products.index');
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
Route::put('/admin/products/{id}', [ProductController::class, 'update'])->whereNumber('id')->name('admin.products.update');
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->whereNumber('id')->name('admin.products.destroy');
Route::get('/health', fn () => response()->json(['status' => 'ok']));

