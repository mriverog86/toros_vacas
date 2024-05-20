<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\GameController as GameControllerV1;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'game'], function() {
        Route::post('/create',[GameControllerV1::class, 'create']);
        Route::post('/propose_combination',[GameControllerV1::class, 'proposeCombination']);
        Route::get('/previous_combination',[GameControllerV1::class, 'previousCombination']);
        Route::delete('/delete',[GameControllerV1::class, 'delete']);
    });
});

