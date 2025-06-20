<?php

use App\Http\Controllers\LiffController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('liff')->name('liff.')->group(function () {
    Route::get('/sample', [LiffController::class, 'sample'])->name('sample');
    Route::get('/{endpointUuid}', [LiffController::class, 'show'])->name('show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
