<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\SetCurrentTenant;

/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/', function () {
    return Inertia::render('Hello', [
        'message' => 'Hola mundo 👋 desde Inertia + React + Laravel 12'
    ]);
});

Route::get('/tenants', function () {
    return Inertia::render('Audit/TenantList');
});

Route::middleware([SetCurrentTenant::class])->group(function () {
    Route::get('/tenants/{tenant}/audit-records', function ($tenant) {
        return Inertia::render('Audit/AuditTable');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
