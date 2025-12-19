<div class="modal fade" id="form_edit_absensi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_edit_absensi">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_absensi" id="edit_id_absensi">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Absensi</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Struktur form sama seperti create, hanya saja value diisi via JS --}}
                        {{-- Contoh: --}}
                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control form-control-sm"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">SDM</label>
                            <select name="id_sdm" id="edit_id_sdm" class="form-select form-select-sm" required>
                                <option value="">-- Pilih SDM --</option>
                                @foreach ($sdms ?? [] as $sdm)
                                    <option value="{{ $sdm->id_sdm }}">
                                        {{ $sdm->nama ?? ($sdm->nama_sdm ?? 'SDM #' . $sdm->id_sdm) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Jadwal</label>
                            <select name="id_jadwal_karyawan" id="edit_id_jadwal_karyawan"
                                class="form-select form-select-sm" required>
                                <option value="">-- Pilih Jadwal --</option>
                                @foreach ($jadwals ?? [] as $j)
                                    <option value="{{ $j->id_jadwal_karyawan }}">
                                        {{ $j->nama_jadwal ?? ($j->keterangan ?? 'Jadwal #' . $j->id_jadwal_karyawan) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Jam Kerja (jam)</label>
                            <input type="number" name="total_jam_kerja" id="edit_total_jam_kerja" step="0.25"
                                min="0" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Terlambat (jam)</label>
                            <input type="number" name="total_terlambat" id="edit_total_terlambat" step="0.25"
                                min="0" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Pulang Awal (jam)</label>
                            <input type="number" name="total_pulang_awal" id="edit_total_pulang_awal" step="0.25"
                                min="0" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
