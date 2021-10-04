<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin|operations|dispatch|safety']], function () {
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])
            ->name('chat.index');
        Route::get('getChatHistory', [ChatController::class, 'getChatHistory'])
            ->name('chat.getChatHistory');
        Route::post('sendMessage', [ChatController::class, 'sendMessageAsUser'])
            ->name('chat.sendMessage');
    });
});
