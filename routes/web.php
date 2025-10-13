<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Health check endpoint for Render
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Debug authentication status
Route::get('/debug-auth', function () {
    return response()->json([
        'user' => Auth::user(),
        'check' => Auth::check(),
        'id' => Auth::id(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'intended_url' => session()->get('url.intended'),
        'previous_url' => url()->previous(),
    ]);
});

// Test admin access without Filament
Route::get('/test-admin', function () {
    if (Auth::check()) {
        return response()->json([
            'message' => 'Authentication successful',
            'user' => Auth::user(),
            'redirect_to' => '/admin'
        ]);
    } else {
        return response()->json([
            'message' => 'Not authenticated',
            'redirect_to' => '/admin'
        ], 401);
    }
})->middleware('auth');
