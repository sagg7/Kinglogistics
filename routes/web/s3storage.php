<?php

use App\Http\Controllers\S3StorageController;
use Illuminate\Support\Facades\Route;

Route::prefix('s3storage')->group(function () {
    Route::get('temporaryUrl', [S3StorageController::class, 'temporaryUrl'])
        ->name('s3storage.temporaryUrl');
});
