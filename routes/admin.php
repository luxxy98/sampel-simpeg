<?php

use App\Http\Controllers\Admin\Person\PersonController;
use App\Http\Controllers\Admin\Sdm\PersonSdmController;
use App\Http\Controllers\Content\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('view-file/{folder}/{filename}', [PortalController::class, 'viewFile'])
    ->where(['folder' => '[A-Za-z0-9_\-]+', 'filename' => '[A-Za-z0-9_\-\.]+'])
    ->name('view-file');

Route::prefix('person')->group(function () {
    Route::get('/', [PersonController::class, 'index'])
        ->name('person.index');
    Route::get('data', [PersonController::class, 'list'])
        ->name('person.list');
    Route::get('show/{id}', [PersonController::class, 'show'])
        ->name('person.show');
    Route::post('/store', [PersonController::class, 'store'])
        ->name('person.store');
    Route::post('update/{id}', [PersonController::class, 'update'])
        ->name('person.update');
});

Route::prefix('sdm')->group(function () {
    Route::get('/', [PersonSdmController::class, 'index'])
        ->name('sdm.sdm.index');
    Route::get('data', [PersonSdmController::class, 'list'])
        ->name('sdm.sdm.list');
    Route::get('show/{id}', [PersonSdmController::class, 'show'])
        ->name('sdm.sdm.show');
    Route::post('/store', [PersonSdmController::class, 'store'])
        ->name('sdm.sdm.store');
    Route::post('update/{id}', [PersonSdmController::class, 'update'])
        ->name('sdm.sdm.update');
    Route::get('histori/{id}', [PersonSdmController::class, 'histori'])
        ->name('sdm.sdm.histori');
    Route::get('find/by/nik/{id}', [PersonSdmController::class, 'find_by_nik'])
        ->name('sdm.sdm.find_by_nik');
});
