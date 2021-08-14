<?php

use App\Http\Controllers\ChatController;

Route::prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])
        ->name('chat.index');
    Route::get('getChatHistory', [ChatController::class, 'getChatHistory'])
        ->name('chat.getChatHistory');
    Route::post('sendMessage', [ChatController::class, 'sendMessageAsUser'])
        ->name('chat.sendMessage');
});
