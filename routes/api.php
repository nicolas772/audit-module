<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\AuditTableController;
use App\Http\Controllers\Api\AuditRecordController;
use App\Http\Middleware\SetCurrentTenant;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tenants', [TenantController::class, 'index']);

Route::middleware([SetCurrentTenant::class])->group(function () {
    Route::get('/audit-tables', [AuditTableController::class, 'index']);
    Route::get('/audit-records', [AuditRecordController::class, 'index']);
});