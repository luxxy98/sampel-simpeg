<div class="modal fade" id="form_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Absensi</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <form id="bt_submit_create">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control form-control-sm" placeholder="YYYY-MM-DD">
                        </div>

                        <div class="col-md-5">
                            <label class="fw-bolder mb-1">SDM</label>
                            <select id="id_sdm" name="id_sdm" class="form-select form-select-sm" data-control="select2" data-placeholder="Pilih SDM">
                                <option></option>
                                @isset($sdmOptions)
                                    @foreach($sdmOptions as $opt)
                                        <option value="{{ $opt['id_sdm'] }}">{{ $opt['nama'] ?? ('SDM #' . $opt['id_sdm']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Jadwal Karyawan</label>
                            <select id="id_jadwal_karyawan" name="id_jadwal_karyawan" class="form-select form-select-sm" data-control="select2" data-placeholder="Pilih Jadwal">
                                <option></option>
                                @isset($jadwalOptions)
                                    @foreach($jadwalOptions as $opt)
                                        <option value="{{ $opt['id_jadwal_karyawan'] }}">{{ $opt['nama'] ?? ('Jadwal #' . $opt['id_jadwal_karyawan']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bolder mb-0">Detail Absensi</h6>
                                <button type="button" class="btn btn-sm btn-light-primary" id="btn_add_detail_row">
                                    + Tambah Baris
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-row-bordered" id="table_detail_create">
                                    <thead>
                                    <tr class="text-gray-600 fw-bold fs-7">
                                        <th style="width: 40px;">#</th>
                                        <th style="min-width: 220px;">Jenis Absen</th>
                                        <th style="min-width: 190px;">Waktu Mulai</th>
                                        <th style="min-width: 190px;">Waktu Selesai</th>
                                        <th style="min-width: 120px;">Durasi (Jam)</th>
                                        <th style="min-width: 220px;">Lokasi Pulang</th>
                                        <th style="width: 60px;">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1">Total Jam Kerja <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="total_jam_kerja" name="total_jam_kerja"
                                           class="form-control form-control-sm bg-light" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1">Total Terlambat <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="total_terlambat" name="total_terlambat"
                                           class="form-control form-control-sm bg-light" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1">Total Pulang Awal <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="total_pulang_awal" name="total_pulang_awal"
                                           class="form-control form-control-sm bg-light" value="0.00" readonly>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-6">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
