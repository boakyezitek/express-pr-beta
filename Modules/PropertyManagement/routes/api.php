<?php

use Illuminate\Support\Facades\Route;
use Modules\PropertyManagement\Http\Controllers\PropertyManagementController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::get('/v1/properties', [PropertyManagementController::class, 'index'])->name('property');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('propertymanagement', PropertyManagementController::class)->names('propertymanagement');
});
