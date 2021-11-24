<?php

use App\Http\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;

Route::prefix('documentation')->group(function () {
    Route::get('/', [DocumentationController::class, 'index'])
        ->name('documentation.index');
});
