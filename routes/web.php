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
    ]);
});
