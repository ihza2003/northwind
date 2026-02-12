<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;


Route::get('/products', [ProductsController::class, 'index']);

Route::get('/products/{id}', [ProductsController::class, 'show']);

Route::post('/products', [ProductsController::class, 'store']);
