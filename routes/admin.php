<?php

use App\Http\Controllers\Admin\Master\MasterJabatanController;
use App\Http\Controllers\Admin\Master\MasterPeriodeController;
use App\Http\Controllers\Admin\Master\MasterUnitController;
use App\Http\Controllers\Admin\Person\PersonAsuransiController;
use App\Http\Controllers\Admin\Person\PersonController;
use App\Http\Controllers\Admin\Ref\RefEselonController;
use App\Http\Controllers\Admin\Ref\RefHubunganKeluargaController;
use App\Http\Controllers\Admin\Ref\RefJenisAsuransiController;
use App\Http\Controllers\Admin\Ref\RefJenjangPendidikanController;
use App\Http\Controllers\Admin\Ref\RefLiburNasionalController;
use App\Http\Controllers\Admin\Ref\RefLiburPtController;
use App\Http\Controllers\Admin\Sdm\PersonSdmController;
use App\Http\Controllers\Admin\Sdm\SdmKeluargaController;
use App\Http\Controllers\Admin\Sdm\SdmRekeningController;
use App\Http\Controllers\Admin\Sdm\SdmRiwayatPendidikanController;
use App\Http\Controllers\Admin\Sdm\SdmStrukturalController;
use App\Http\Controllers\Content\PortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Absensi\AbsensiController;
use App\Http\Controllers\Admin\Absensi\AbsenJenisController;
use App\Http\Controllers\Admin\Absensi\MasterJadwalKerjaController;
use App\Http\Controllers\Admin\Absensi\JadwalKaryawanController;
use App\Http\Controllers\Admin\Gaji\GajiPeriodeController;
use App\Http\Controllers\Admin\Gaji\GajiTrxController;
use App\Http\Controllers\Admin\Gaji\GajiKomponenController;
use App\Http\Controllers\Admin\Gaji\GajiJenisKomponenController;
use App\Http\Controllers\Admin\Gaji\GajiDistribusiController;


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
    Route::post('destroy/{id}', [PersonController::class, 'destroy'])   // <- ini
        ->name('person.destroy');
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
    Route::post('destroy/{id}', [PersonSdmController::class, 'destroy'])
        ->name('sdm.sdm.destroy');


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

    Route::prefix('keluarga')->group(function () {
        Route::get('/{id}', [SdmKeluargaController::class, 'index'])
            ->name('sdm.keluarga.index');
        Route::get('data/{id}', [SdmKeluargaController::class, 'list'])
            ->name('sdm.keluarga.list');
        Route::get('show/{id}', [SdmKeluargaController::class, 'show'])
            ->name('sdm.keluarga.show');
        Route::post('/store', [SdmKeluargaController::class, 'store'])
            ->name('sdm.keluarga.store');
        Route::post('update/{id}', [SdmKeluargaController::class, 'update'])
            ->name('sdm.keluarga.update');
        Route::post('destroy/{id}', [SdmKeluargaController::class, 'destroy'])
            ->name('sdm.keluarga.destroy');
        Route::get('find/by/nik/{id}', [SdmKeluargaController::class, 'find_by_nik'])
            ->name('sdm.keluarga.find_by_nik');
    });

    Route::prefix('asuransi')->group(function () {
        Route::get('/{id}', [PersonAsuransiController::class, 'index'])
            ->name('sdm.asuransi.index');
        Route::get('data/{id}', [PersonAsuransiController::class, 'list'])
            ->name('sdm.asuransi.list');
        Route::get('show/{id}', [PersonAsuransiController::class, 'show'])
            ->name('sdm.asuransi.show');
        Route::post('/store', [PersonAsuransiController::class, 'store'])
            ->name('sdm.asuransi.store');
        Route::post('update/{id}', [PersonAsuransiController::class, 'update'])
            ->name('sdm.asuransi.update');
        Route::post('destroy/{id}', [PersonAsuransiController::class, 'destroy'])
            ->name('sdm.asuransi.destroy');
        Route::get('find/by/nik/{id}', [PersonAsuransiController::class, 'find_by_nik'])
            ->name('sdm.asuransi.find_by_nik');
    });

    Route::prefix('rekening')->group(function () {
        Route::get('/{id}', [SdmRekeningController::class, 'index'])
            ->name('sdm.rekening.index');
        Route::get('data/{id}', [SdmRekeningController::class, 'list'])
            ->name('sdm.rekening.list');
        Route::get('show/{id}', [SdmRekeningController::class, 'show'])
            ->name('sdm.rekening.show');
        Route::post('/store', [SdmRekeningController::class, 'store'])
            ->name('sdm.rekening.store');
        Route::post('update/{id}', [SdmRekeningController::class, 'update'])
            ->name('sdm.rekening.update');
        Route::post('destroy/{id}', [SdmRekeningController::class, 'destroy'])
            ->name('sdm.rekening.destroy');
    });

    Route::prefix('struktural')->group(function () {
        Route::get('/{id}', [SdmStrukturalController::class, 'index'])
            ->name('sdm.struktural.index');
        Route::get('data/{id}', [SdmStrukturalController::class, 'list'])
            ->name('sdm.struktural.list');
        Route::get('show/{id}', [SdmStrukturalController::class, 'show'])
            ->name('sdm.struktural.show');
        Route::post('/store', [SdmStrukturalController::class, 'store'])
            ->name('sdm.struktural.store');
        Route::post('update/{id}', [SdmStrukturalController::class, 'update'])
            ->name('sdm.struktural.update');
        Route::post('destroy/{id}', [SdmStrukturalController::class, 'destroy'])
            ->name('sdm.struktural.destroy');
    });
});

Route::prefix('master')->group(function () {
    Route::prefix('periode')->group(function () {
        Route::get('/', [MasterPeriodeController::class, 'index'])
            ->name('master.periode.index');
        Route::get('data', [MasterPeriodeController::class, 'list'])
            ->name('master.periode.list');
        Route::get('show/{id}', [MasterPeriodeController::class, 'show'])
            ->name('master.periode.show');
        Route::post('/store', [MasterPeriodeController::class, 'store'])
            ->name('master.periode.store');
        Route::post('update/{id}', [MasterPeriodeController::class, 'update'])
            ->name('master.periode.update');
    });

    Route::prefix('unit')->group(function () {
        Route::get('/', [MasterUnitController::class, 'index'])
            ->name('master.unit.index');
        Route::get('data', [MasterUnitController::class, 'list'])
            ->name('master.unit.list');
        Route::get('show/{id}', [MasterUnitController::class, 'show'])
            ->name('master.unit.show');
        Route::post('/store', [MasterUnitController::class, 'store'])
            ->name('master.unit.store');
        Route::post('update/{id}', [MasterUnitController::class, 'update'])
            ->name('master.unit.update');
    });

    Route::prefix('jabatan')->group(function () {
        Route::get('/', [MasterJabatanController::class, 'index'])
            ->name('master.jabatan.index');
        Route::get('data', [MasterJabatanController::class, 'list'])
            ->name('master.jabatan.list');
        Route::get('show/{id}', [MasterJabatanController::class, 'show'])
            ->name('master.jabatan.show');
        Route::post('/store', [MasterJabatanController::class, 'store'])
            ->name('master.jabatan.store');
        Route::post('update/{id}', [MasterJabatanController::class, 'update'])
            ->name('master.jabatan.update');
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

    Route::prefix('hubungan-keluarga')->group(function () {
        Route::get('/', [RefHubunganKeluargaController::class, 'index'])
            ->name('ref.hubungan-keluarga.index');
        Route::get('data', [RefHubunganKeluargaController::class, 'list'])
            ->name('ref.hubungan-keluarga.list');
        Route::get('show/{id}', [RefHubunganKeluargaController::class, 'show'])
            ->name('ref.hubungan-keluarga.show');
        Route::post('/store', [RefHubunganKeluargaController::class, 'store'])
            ->name('ref.hubungan-keluarga.store');
        Route::post('update/{id}', [RefHubunganKeluargaController::class, 'update'])
            ->name('ref.hubungan-keluarga.update');
    });

    Route::prefix('jenis-asuransi')->group(function () {
        Route::get('/', [RefJenisAsuransiController::class, 'index'])
            ->name('ref.jenis-asuransi.index');
        Route::get('data', [RefJenisAsuransiController::class, 'list'])
            ->name('ref.jenis-asuransi.list');
        Route::get('show/{id}', [RefJenisAsuransiController::class, 'show'])
            ->name('ref.jenis-asuransi.show');
        Route::post('/store', [RefJenisAsuransiController::class, 'store'])
            ->name('ref.jenis-asuransi.store');
        Route::post('update/{id}', [RefJenisAsuransiController::class, 'update'])
            ->name('ref.jenis-asuransi.update');
    });

    Route::prefix('eselon')->group(function () {
        Route::get('/', [RefEselonController::class, 'index'])
            ->name('ref.eselon.index');
        Route::get('data', [RefEselonController::class, 'list'])
            ->name('ref.eselon.list');
        Route::get('show/{id}', [RefEselonController::class, 'show'])
            ->name('ref.eselon.show');
        Route::post('/store', [RefEselonController::class, 'store'])
            ->name('ref.eselon.store');
        Route::post('update/{id}', [RefEselonController::class, 'update'])
            ->name('ref.eselon.update');
    });

    Route::prefix('libur-nasional')->group(function () {
        Route::get('/', [RefLiburNasionalController::class, 'index'])
            ->name('ref.libur-nasional.index');
        Route::get('data', [RefLiburNasionalController::class, 'list'])
            ->name('ref.libur-nasional.list');
        Route::get('show/{id}', [RefLiburNasionalController::class, 'show'])
            ->name('ref.libur-nasional.show');
        Route::post('store', [RefLiburNasionalController::class, 'store'])
            ->name('ref.libur-nasional.store');
        Route::post('update/{id}', [RefLiburNasionalController::class, 'update'])
            ->name('ref.libur-nasional.update');
        Route::post('destroy/{id}', [RefLiburNasionalController::class, 'destroy'])
            ->name('ref.libur-nasional.destroy');
    });

    Route::prefix('libur-pt')->group(function () {
        Route::get('/', [RefLiburPtController::class, 'index'])
            ->name('ref.libur-pt.index');
        Route::get('data', [RefLiburPtController::class, 'list'])
            ->name('ref.libur-pt.list');
        Route::get('show/{id}', [RefLiburPtController::class, 'show'])
            ->name('ref.libur-pt.show');
        Route::post('store', [RefLiburPtController::class, 'store'])
            ->name('ref.libur-pt.store');
        Route::post('update/{id}', [RefLiburPtController::class, 'update'])
            ->name('ref.libur-pt.update');
        Route::post('destroy/{id}', [RefLiburPtController::class, 'destroy'])
            ->name('ref.libur-pt.destroy');
    });
});

    Route::prefix('absensi')->group(function () {
    Route::get('/', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('data', [AbsensiController::class, 'list'])->name('absensi.list');
    Route::get('show/{id}', [AbsensiController::class, 'show'])->name('absensi.show');
    Route::get('resolve-jadwal', [AbsensiController::class, 'resolveJadwal'])->name('absensi.resolve-jadwal');
    Route::post('store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::post('update/{id}', [AbsensiController::class, 'update'])->name('absensi.update');
    Route::post('destroy/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

    Route::prefix('jenis')->group(function () {
        Route::get('/', [AbsenJenisController::class, 'index'])->name('absensi.jenis.index');
        Route::get('data', [AbsenJenisController::class, 'list'])->name('absensi.jenis.list');
        Route::get('show/{id}', [AbsenJenisController::class, 'show'])->name('absensi.jenis.show');
        Route::post('store', [AbsenJenisController::class, 'store'])->name('absensi.jenis.store');
        Route::post('update/{id}', [AbsenJenisController::class, 'update'])->name('absensi.jenis.update');
        Route::post('destroy/{id}', [AbsenJenisController::class, 'destroy'])->name('absensi.jenis.destroy');
    });

    Route::prefix('jadwal-kerja')->group(function () {
        Route::get('/', [MasterJadwalKerjaController::class, 'index'])->name('absensi.jadwal-kerja.index');
        Route::get('data', [MasterJadwalKerjaController::class, 'list'])->name('absensi.jadwal-kerja.list');
        Route::get('show/{id}', [MasterJadwalKerjaController::class, 'show'])->name('absensi.jadwal-kerja.show');
        Route::post('store', [MasterJadwalKerjaController::class, 'store'])->name('absensi.jadwal-kerja.store');
        Route::post('update/{id}', [MasterJadwalKerjaController::class, 'update'])->name('absensi.jadwal-kerja.update');
        Route::post('destroy/{id}', [MasterJadwalKerjaController::class, 'destroy'])->name('absensi.jadwal-kerja.destroy');
    });

    Route::prefix('jadwal-karyawan')->group(function () {
        Route::get('/', [JadwalKaryawanController::class, 'index'])->name('absensi.jadwal-karyawan.index');
        Route::get('data', [JadwalKaryawanController::class, 'list'])->name('absensi.jadwal-karyawan.list');
        Route::get('show/{id}', [JadwalKaryawanController::class, 'show'])->name('absensi.jadwal-karyawan.show');
        Route::post('store', [JadwalKaryawanController::class, 'store'])->name('absensi.jadwal-karyawan.store');
        Route::post('update/{id}', [JadwalKaryawanController::class, 'update'])->name('absensi.jadwal-karyawan.update');
        Route::post('destroy/{id}', [JadwalKaryawanController::class, 'destroy'])->name('absensi.jadwal-karyawan.destroy');
    });
});


    Route::prefix('gaji')->group(function () {
    Route::prefix('periode')->group(function () {
        Route::get('/', [GajiPeriodeController::class, 'index'])->name('gaji.periode.index');
        Route::get('data', [GajiPeriodeController::class, 'list'])->name('gaji.periode.list');
        Route::get('show/{id}', [GajiPeriodeController::class, 'show'])->name('gaji.periode.show');
        Route::post('store', [GajiPeriodeController::class, 'store'])->name('gaji.periode.store');
        Route::post('update/{id}', [GajiPeriodeController::class, 'update'])->name('gaji.periode.update');
        Route::post('destroy/{id}', [GajiPeriodeController::class, 'destroy'])->name('gaji.periode.destroy');
        Route::post('generate/{id}', [GajiPeriodeController::class, 'generate'])->name('gaji.periode.generate');
    });

    Route::prefix('trx')->group(function () {
        Route::get('/', [GajiTrxController::class, 'index'])->name('gaji.trx.index');
        Route::get('data', [GajiTrxController::class, 'list'])->name('gaji.trx.list');
        Route::get('show/{id}', [GajiTrxController::class, 'show'])->name('gaji.trx.show');
    });

    Route::prefix('komponen')->group(function () {
        Route::get('/', [GajiKomponenController::class, 'index'])->name('gaji.komponen.index');
        Route::get('data', [GajiKomponenController::class, 'list'])->name('gaji.komponen.list');
        Route::get('show/{id}', [GajiKomponenController::class, 'show'])->name('gaji.komponen.show');
        Route::post('store', [GajiKomponenController::class, 'store'])->name('gaji.komponen.store');
        Route::post('update/{id}', [GajiKomponenController::class, 'update'])->name('gaji.komponen.update');
        Route::post('destroy/{id}', [GajiKomponenController::class, 'destroy'])->name('gaji.komponen.destroy');
    });

    Route::prefix('jenis-komponen')->group(function () {
        Route::get('data', [GajiJenisKomponenController::class, 'list'])->name('gaji.jenis-komponen.list');
        Route::get('show/{id}', [GajiJenisKomponenController::class, 'show'])->name('gaji.jenis-komponen.show');
        Route::post('store', [GajiJenisKomponenController::class, 'store'])->name('gaji.jenis-komponen.store');
        Route::post('update/{id}', [GajiJenisKomponenController::class, 'update'])->name('gaji.jenis-komponen.update');
        Route::post('destroy/{id}', [GajiJenisKomponenController::class, 'destroy'])->name('gaji.jenis-komponen.destroy');
    });

    Route::prefix('distribusi')->group(function () {
        Route::get('/', [GajiDistribusiController::class, 'index'])->name('gaji.distribusi.index');
        Route::get('data', [GajiDistribusiController::class, 'list'])->name('gaji.distribusi.list');
        Route::get('show/{id}', [GajiDistribusiController::class, 'show'])->name('gaji.distribusi.show');
        Route::post('store', [GajiDistribusiController::class, 'store'])->name('gaji.distribusi.store');
        Route::post('update/{id}', [GajiDistribusiController::class, 'update'])->name('gaji.distribusi.update');
        Route::post('destroy/{id}', [GajiDistribusiController::class, 'destroy'])->name('gaji.distribusi.destroy');
    });
});

