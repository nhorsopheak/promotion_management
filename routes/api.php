<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CreatePromotionController;

Route::prefix('v1')->group(function () {
    Route::post('/promotions', CreatePromotionController::class);
});
