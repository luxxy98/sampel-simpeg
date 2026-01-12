<div class="modal fade" id="form_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Absensi</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <form id="bt_submit_edit_absensi">
                    <input type="hidden" id="edit_id_absensi" name="id_absensi">
                    {{-- Hidden fields for holiday integration --}}
                    <input type="hidden" id="edit_is_hari_libur" name="is_hari_libur" value="0">
                    <input type="hidden" id="edit_id_tarif_lembur" name="id_tarif_lembur" value="">

                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Tanggal</label>
                            <input type="text" id="edit_tanggal" name="tanggal" class="form-control form-control-sm">
                            {{-- Holiday badge indicator --}}
                            <div id="edit_holiday_badge" class="mt-2" style="display: none;">
                                <span class="badge badge-danger fs-7">
                                    <i class="bi bi-calendar-x me-1"></i>
                                    <span id="edit_holiday_name">Hari Libur</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="fw-bolder mb-1">SDM</label>
                            <select id="edit_id_sdm" name="id_sdm" class="form-select form-select-sm" data-control="select2">
                                @isset($sdmOptions)
                                    @foreach($sdmOptions as $opt)
                                        <option value="{{ $opt['id_sdm'] }}">{{ $opt['nama'] ?? ('SDM #' . $opt['id_sdm']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Jadwal Karyawan</label>
                            <select id="edit_id_jadwal_karyawan" name="id_jadwal_karyawan" class="form-select form-select-sm" data-control="select2">
                                @isset($jadwalOptions)
                                    @foreach($jadwalOptions as $opt)
                                        <option value="{{ $opt['id_jadwal_karyawan'] }}">{{ $opt['nama'] ?? ('Jadwal #' . $opt['id_jadwal_karyawan']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bolder mb-2">Detail Absensi</h6>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-row-bordered" id="table_detail_edit">
                                    <thead>
                                    <tr class="text-gray-600 fw-bold fs-7">
                                        <th style="width: 40px;">#</th>
                                        <th style="min-width: 220px;">Jenis Absen</th>
                                        <th style="min-width: 190px;">Waktu Mulai</th>
                                        <th style="min-width: 190px;">Waktu Selesai</th>
                                        <th style="min-width: 120px;">Durasi (Jam)</th>
                                        <th style="min-width: 220px;">Lokasi Pulang</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="fw-bolder mb-1">Total Jam Kerja <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="edit_total_jam_kerja" name="total_jam_kerja" class="form-control form-control-sm bg-light" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bolder mb-1">Total Terlambat <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="edit_total_terlambat" name="total_terlambat" class="form-control form-control-sm bg-light" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bolder mb-1">Total Pulang Awal <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="edit_total_pulang_awal" name="total_pulang_awal" class="form-control form-control-sm bg-light" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bolder mb-1 text-success">Total Lembur <small class="text-muted">(otomatis)</small></label>
                                    <input type="number" step="0.01" id="edit_total_lembur" name="total_lembur" class="form-control form-control-sm bg-light-success text-success fw-bold" readonly>
                                </div>
                            </div>
                            {{-- Nominal Lembur Row --}}
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1">Tarif Lembur</label>
                                    <input type="text" id="edit_tarif_lembur_info" class="form-control form-control-sm bg-light" readonly value="-">
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1">Tarif Per Jam</label>
                                    <input type="text" id="edit_tarif_per_jam_display" class="form-control form-control-sm bg-light" readonly value="Rp 0">
                                    <input type="hidden" id="edit_tarif_per_jam" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bolder mb-1 text-primary">Nominal Lembur <small class="text-muted">(otomatis)</small></label>
                                    <input type="text" id="edit_nominal_lembur_display" class="form-control form-control-sm bg-light-primary text-primary fw-bold" readonly value="Rp 0">
                                    <input type="hidden" id="edit_nominal_lembur" name="nominal_lembur" value="0">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-6">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

