<?php

use App\Http\Controllers\Api\V1\Report\ReportController;

Route::prefix('report')->group(function (){
    Route::get('service', [ReportController::class,'serviceReport'])->name('api-report.service');
    Route::get('service-day-sucsses', [ReportController::class,'serviceDayReport'])->name('api-report.service-sucsses');
    Route::get('service-day-new-sucsses', [ReportController::class,'serviceDayNewReport'])->name('api-report.service-new-sucsses');

    Route::get('service-day', [ReportController::class,'serviceReportDay'])->name('api-report.service-day');
    Route::get('service-auto', [ReportController::class,'serviceAutoReport'])->name('api-report.service-auto');

    Route::get('service-auto-v2', [ReportController::class,'serviceAutoReportV2'])->name('api-report.service-auto-v2');

    Route::get('service-auto-pedding', [ReportController::class,'serviceAutoPedding'])->name('api-report.service-auto-pedding');

    Route::get('confirm-withdraw', [ReportController::class,'confirmWithdraw'])->name('api-report.confirm-withdraw');

    Route::get('get-service-attribute', [ReportController::class,'getServiceAttribute'])->name('get-service-attribute.service');
});
