<?php

use App\Http\Controllers\S3StorageController;
use Illuminate\Support\Facades\Route;

Route::prefix('s3storage')->group(function () {
    Route::get('temporaryUrl', [S3StorageController::class, 'temporaryUrl'])
        ->name('s3storage.temporaryUrl');
    Route::get('getTemporaryUrl', [S3StorageController::class, 'getTemporaryUrl'])
        ->name('s3storage.getTemporaryUrl');
    Route::post('deleteFileFromUrl', [S3StorageController::class, 'deleteFileFromUrl'])
        ->name('s3storage.deleteFileFromUrl');
});
