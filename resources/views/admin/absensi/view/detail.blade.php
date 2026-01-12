<div class="modal fade" id="form_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Detail Absensi</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                {{-- Holiday Alert Badge --}}
                <div class="row mb-3" id="d_holiday_alert" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-warning d-flex align-items-center py-2 mb-0">
                            <i class="bi bi-calendar-x-fill fs-4 me-2 text-warning"></i>
                            <div>
                                <span class="fw-bold">Absensi di Hari Libur!</span>
                                <span id="d_holiday_name" class="ms-1"></span>
                                <span class="badge bg-warning text-dark ms-2" id="d_holiday_info">Jam kerja dihitung sebagai lembur</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Tanggal</label>
                        <span id="d_tanggal" class="fs-6">-</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">SDM</label>
                        <span id="d_sdm" class="fs-6">-</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Jadwal</label>
                        <span id="d_jadwal" class="fs-6">-</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Total Jam Kerja</label>
                        <span id="d_total_jam_kerja" class="fs-6">0.00</span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Total Terlambat</label>
                        <span id="d_total_terlambat" class="fs-6">0.00</span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Total Pulang Awal</label>
                        <span id="d_total_pulang_awal" class="fs-6">0.00</span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bolder text-success d-block mb-1">Total Lembur</label>
                        <span id="d_total_lembur" class="fs-6 text-success fw-bold">0.00</span>
                    </div>
                </div>

                {{-- Info Lembur Section --}}
                <div class="row mb-4 bg-light-success rounded p-3" id="d_lembur_section" style="display: none;">
                    <div class="col-12 mb-2">
                        <h6 class="fw-bolder text-success mb-0"><i class="bi bi-clock-history me-1"></i> Info Lembur</h6>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Tarif Lembur</label>
                        <span id="d_tarif_lembur_nama" class="fs-6">-</span>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="fw-bolder text-gray-600 d-block mb-1">Tarif Per Jam</label>
                        <span id="d_tarif_per_jam" class="fs-6">Rp 0</span>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="fw-bolder text-primary d-block mb-1">Nominal Lembur</label>
                        <span id="d_nominal_lembur" class="fs-5 text-primary fw-bold">Rp 0</span>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bolder mb-3">Detail Absensi</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-row-bordered table-hover" id="table_detail_view">
                        <thead>
                        <tr class="text-gray-600 fw-bold fs-7 bg-light">
                            <th style="width: 40px;">#</th>
                            <th style="min-width: 120px;">Jenis</th>
                            <th style="min-width: 160px;">Waktu Mulai</th>
                            <th style="min-width: 160px;">Waktu Selesai</th>
                            <th style="min-width: 80px;">Durasi</th>
                            <th style="min-width: 150px;">Lokasi Pulang</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
