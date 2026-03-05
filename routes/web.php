<?php

use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index']);
Route::get('/chat/{conversation}', [ChatController::class, 'show']);
Route::post('/chat/send', [ChatController::class, 'send']);
Route::delete('/chat/{conversation}', [ChatController::class, 'destroy']);