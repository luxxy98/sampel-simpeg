<div class="modal fade" id="form_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Detail Transaksi Gaji</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                {{-- Info Karyawan --}}
                <div class="card bg-light mb-4">
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="mb-1" id="d_sdm"></h5>
                                <div class="text-muted" id="d_jabatan_unit"></div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-primary fs-6" id="d_periode"></span>
                                <div class="text-muted small mt-1" id="d_status"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Gaji --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small">Total Penghasilan</div>
                            <div id="d_total_penghasilan" class="fs-5 fw-bold text-success"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small">Total Potongan</div>
                            <div id="d_total_potongan" class="fs-5 fw-bold text-danger"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small">Uang Lembur</div>
                            <div id="d_uang_lembur" class="fs-5 fw-bold text-info"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border border-primary rounded p-3 text-center bg-primary bg-opacity-10">
                            <div class="text-primary small fw-bold">Take Home Pay</div>
                            <div id="d_thp" class="fs-4 fw-bold text-primary"></div>
                        </div>
                    </div>
                </div>

                {{-- Info Waktu --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-clock me-2"></i>
                            <span>Dibuat: <strong id="d_created_at"></strong></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-pencil me-2"></i>
                            <span>Diupdate: <strong id="d_updated_at"></strong></span>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bolder mb-3">Rincian Komponen Gaji</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-row-bordered" id="table_detail_gaji">
                        <thead>
                        <tr class="text-gray-600 fw-bold fs-7 bg-light">
                            <th width="40">#</th>
                            <th>Komponen</th>
                            <th class="text-end">Nominal</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
