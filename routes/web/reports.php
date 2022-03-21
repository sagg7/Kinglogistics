<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('report')->group(function () {
    Route::group(['middleware' => ['permission:read-report-daily-loads']], function () {
        Route::get('dailyLoads', [ReportController::class, 'dailyLoads'])
            ->name('report.dailyLoads');
        Route::get('dailyLoadsData', [ReportController::class, 'dailyLoadsData'])
            ->name('report.dailyLoadsData');
        Route::get('activeTime', [ReportController::class, 'activeTime'])
            ->name('report.activeTime');
        Route::get('profitAndLoss', [ReportController::class, 'profitAndLoss'])
            ->name('report.profitAndLoss');
        Route::get('profitAndLossData', [ReportController::class, 'profitAndLossData'])
            ->name('report.profitAndLossData');
        Route::get('activeTimeData', [ReportController::class, 'activeTimeData'])
            ->name('report.activeTimeData');
        Route::get('getDispatchReport', [ReportController::class, 'getDispatchReport'])
            ->name('report.getDispatchReport');
        Route::get('showDispatchReportById/{id}', [ReportController::class, 'showDispatchReportById'])
            ->name('report.showDispatchReportById');
        Route::get('customerLoads', [ReportController::class, 'customerLoads'])
            ->name('report.customerLoads');
        Route::get('customerLoadsData', [ReportController::class, 'customerLoadsData'])
            ->name('report.customerLoadsData');
    });

    Route::group(['middleware' => ['permission:update-load-dispatch']], function () {
        Route::get('dailyDispatchReport', [ReportController::class, 'createDailyDispatchReport'])
            ->name('report.createDailyDispatchReport');
        Route::post('storeDispatchReport', [ReportController::class, 'storeDispatchReport'])
            ->name('report.storeDispatchReport');
    });
});
