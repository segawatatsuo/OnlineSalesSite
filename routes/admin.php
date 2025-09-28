<?php
use Illuminate\Http\Request;
use App\Models\Categorization;

use Illuminate\Support\Facades\Route;
use Encore\Admin\Facades\Admin;

use App\Admin\Controllers\ProductJaController;
use App\Admin\Controllers\CategoryController;
use App\Admin\Controllers\CategorizationController;

Route::post('product/duplicate', [ProductJaController::class, 'duplicate']);

Admin::resource('categories', CategoryController::class);

Admin::resource('categorizations', CategorizationController::class);




