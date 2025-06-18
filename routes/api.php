<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('liff/v1')->group(function () {
    Route::post('/actions', function (Request $request) {
        $request->validate([
            'idToken' => 'required|string',
            'acid' => 'required|string',
        ]);

        // Log the action request
        logger('LIFF Action Request', [
            'acid' => $request->acid,
            'idToken' => substr($request->idToken, 0, 20) . '...',
            'timestamp' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Action logged successfully',
        ]);
    });
});