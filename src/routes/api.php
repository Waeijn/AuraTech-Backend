<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Category Routes
Route::apiResource('categories', CategoryController::class);

// Product Routes
Route::apiResource('products', ProductController::class);
