<?php

use App\Http\Controllers\OpenAIController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/get-image-status', [OpenAIController::class, 'checkIfImageHasCoffeeCup']);
Route::post('/get-ai-response', [OpenAIController::class, 'getChatGPTResponse']);
Route::post('/ba/get-ai-response', [OpenAIController::class, 'getChatGPTResponseBa']);
