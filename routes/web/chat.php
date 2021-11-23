<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin|operations|dispatch|safety']], function () {
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])
            ->name('chat.index');
        Route::get('getContacts', [ChatController::class, 'getContacts'])
            ->name('chat.getContacts');
        Route::get('getChatHistory', [ChatController::class, 'getChatHistory'])
            ->name('chat.getChatHistory');
        Route::get('getUnreadCount', [ChatController::class, 'getUnreadCount'])
            ->name('chat.getUnreadCount');
        Route::post('sendMessage', [ChatController::class, 'sendMessageAsUser'])
            ->name('chat.sendMessage');
    });
});
