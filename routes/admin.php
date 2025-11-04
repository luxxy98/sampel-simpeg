<?php

use App\Http\Controllers\Admin\Person\PersonController;
use App\Http\Controllers\Admin\Ref\RefJenjangPendidikanController;
use App\Http\Controllers\Admin\Sdm\PersonSdmController;
use App\Http\Controllers\Admin\Sdm\SdmRiwayatPendidikanController;
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

    Route::prefix('riwayat-pendidikan')->group(function () {
        Route::get('/{id}', [SdmRiwayatPendidikanController::class, 'index'])
            ->name('sdm.riwayat-pendidikan.index');
        Route::get('data/{id}', [SdmRiwayatPendidikanController::class, 'list'])
            ->name('sdm.riwayat-pendidikan.list');
        Route::get('show/{id}', [SdmRiwayatPendidikanController::class, 'show'])
            ->name('sdm.riwayat-pendidikan.show');
        Route::post('/store', [SdmRiwayatPendidikanController::class, 'store'])
            ->name('sdm.riwayat-pendidikan.store');
        Route::post('update/{id}', [SdmRiwayatPendidikanController::class, 'update'])
            ->name('sdm.riwayat-pendidikan.update');
        Route::post('destroy/{id}', [SdmRiwayatPendidikanController::class, 'destroy'])
            ->name('sdm.riwayat-pendidikan.destroy');
    });
});

Route::prefix('ref')->group(function () {
    Route::prefix('jenjang-pendidikan')->group(function () {
        Route::get('/', [RefJenjangPendidikanController::class, 'index'])
            ->name('ref.jenjang-pendidikan.index');
        Route::get('data', [RefJenjangPendidikanController::class, 'list'])
            ->name('ref.jenjang-pendidikan.list');
        Route::get('show/{id}', [RefJenjangPendidikanController::class, 'show'])
            ->name('ref.jenjang-pendidikan.show');
        Route::post('/store', [RefJenjangPendidikanController::class, 'store'])
            ->name('ref.jenjang-pendidikan.store');
        Route::post('update/{id}', [RefJenjangPendidikanController::class, 'update'])
            ->name('ref.jenjang-pendidikan.update');
    });
});
