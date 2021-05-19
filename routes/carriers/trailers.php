<?php

use App\Http\Controllers\TrailerController;
use Illuminate\Support\Facades\Route;

Route::prefix('trailer')->group(function () {
    Route::get('selection', [TrailerController::class, 'selection'])
        ->name('trailer.selection');
});
