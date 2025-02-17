<?php

use App\Http\Controllers\Api\ContactApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::apiResource('contacts', ContactApiController::class);

Route::prefix('contacts')->group(function () {
    Route::post('/', [ContactApiController::class, 'store']);
    Route::get('/', [ContactApiController::class, 'index']);
    Route::get('/{id}', [ContactApiController::class, 'show']);
    Route::put('/{id}', [ContactApiController::class, 'update']);
    Route::delete('/{id}', [ContactApiController::class, 'destroy']);

    Route::post('/import-xml', [ContactApiController::class, 'importXML']);
});
