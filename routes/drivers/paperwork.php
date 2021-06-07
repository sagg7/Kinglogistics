<?php

use App\Http\Controllers\PaperworkController;
use Illuminate\Support\Facades\Route;

Route::prefix('paperwork')->group(function () {
    Route::get('showTemplate/{id}/{related_id}', [PaperworkController::class, 'showTemplate'])
        ->name('paperwork.showTemplate');
    Route::get('pdf/{id}/{related_id}', [PaperworkController::class, 'pdf'])
        ->name('paperwork.pdf');
    Route::post('storeTemplate/{id}/{related_id}', [PaperworkController::class, 'storeTemplate'])
        ->name('paperwork.storeTemplate');
});
