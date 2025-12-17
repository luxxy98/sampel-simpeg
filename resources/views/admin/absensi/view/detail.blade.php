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
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="fw-bolder">Tanggal</div>
                        <div id="d_tanggal" class="text-muted"></div>
                    </div>
                    <div class="col-md-5">
                        <div class="fw-bolder">SDM</div>
                        <div id="d_sdm" class="text-muted"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bolder">Jadwal</div>
                        <div id="d_jadwal" class="text-muted"></div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="fw-bolder">Total Jam Kerja</div>
                        <div id="d_total_jam_kerja" class="text-muted"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bolder">Total Terlambat</div>
                        <div id="d_total_terlambat" class="text-muted"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bolder">Total Pulang Awal</div>
                        <div id="d_total_pulang_awal" class="text-muted"></div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bolder mb-3">Detail (absensi_detail)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-row-bordered" id="table_detail_view">
                        <thead>
                        <tr class="text-gray-600 fw-bold fs-7">
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Durasi</th>
                            <th>Lokasi Pulang</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
