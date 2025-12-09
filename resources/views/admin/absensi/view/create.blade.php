<div class="modal fade" id="form_create_absensi" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_create_absensi">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Absensi</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Tanggal --}}
                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- SDM --}}
                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">SDM</label>
                                <select name="id_sdm" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih SDM --</option>
                                    @foreach($sdms ?? [] as $sdm)
                                        <option value="{{ $sdm->id_sdm }}">
                                            {{ $sdm->nama ?? $sdm->nama_sdm ?? 'SDM #'.$sdm->id_sdm }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- Jadwal --}}
                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Jadwal</label>
                                <select name="id_jadwal_karyawan" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Jadwal --</option>
                                    @foreach($jadwals ?? [] as $j)
                                        <option value="{{ $j->id_jadwal_karyawan }}">
                                            {{ $j->nama_jadwal ?? $j->keterangan ?? 'Jadwal #'.$j->id_jadwal_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- Total jam & keterlambatan --}}
                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Jam Kerja (jam)</label>
                                <input type="number" name="total_jam_kerja" step="0.25" min="0"
                                       class="form-control form-control-sm" value="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Terlambat (jam)</label>
                                <input type="number" name="total_terlambat" step="0.25" min="0"
                                       class="form-control form-control-sm" value="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="d-flex flex-column">
                                <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Total Pulang Awal (jam)</label>
                                <input type="number" name="total_pulang_awal" step="0.25" min="0"
                                       class="form-control form-control-sm" value="0">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- Kalau mau, di sini bisa ditambah detail absensi (datang/pulang, jenis absen) via JS dinamis --}}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>