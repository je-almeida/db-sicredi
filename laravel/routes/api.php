<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OperationController;

Route::apiResource('customers', CustomerController::class);
Route::apiResource('operations', OperationController::class)->only(['index', 'store', 'show']);
Route::post('operations/{id}/apply', [OperationController::class, 'apply']);