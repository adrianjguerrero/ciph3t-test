<?php

use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}/edit', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

