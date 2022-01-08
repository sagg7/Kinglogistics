<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('chat')->group(function () {
    Route::group(['middleware' => ['permission:read-chat']], function () {
        Route::get('/', [ChatController::class, 'index'])
            ->name('chat.index');
        Route::get('getContacts', [ChatController::class, 'getContacts'])
            ->name('chat.getContacts');
        Route::get('getChatHistory', [ChatController::class, 'getChatHistory'])
            ->name('chat.getChatHistory');
        Route::get('getUnreadCount', [ChatController::class, 'getUnreadCount'])
            ->name('chat.getUnreadCount');
    });
    Route::group(['middleware' => ['permission:create-chat']], function () {
        Route::post('sendMessage', [ChatController::class, 'sendMessageAsUser'])
            ->name('chat.sendMessage');
    });
    /*Route::group(['middleware' => ['permission:update-chat']], function () {
        });
        Route::group(['middleware' => ['permission:delete-chat']], function () {
        });*/
});
