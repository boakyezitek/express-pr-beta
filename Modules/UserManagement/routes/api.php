<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\ClientController;
use Modules\UserManagement\Http\Controllers\StaffController;
use Modules\UserManagement\Http\Controllers\TenantController;
use Modules\UserManagement\Http\Controllers\UserManagementController;
use Modules\UserManagement\Http\Controllers\VendorController;

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

Route::get('staff/visible-on-website', StaffController::class)->names('visible-on-website');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('staff', StaffController::class)->names('staff');
    Route::apiResource('clients', ClientController::class)->names('client');
    Route::apiResource('tenants', TenantController::class)->names('usermanagement');
    Route::apiResource('vendors', VendorController::class)->names('usermanagement');
});
