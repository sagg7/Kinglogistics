<?php

use App\Http\Controllers\ChatController;

Route::prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])
        ->name('chat.index');
});
