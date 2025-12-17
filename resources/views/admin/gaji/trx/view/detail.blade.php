<div class="modal fade" id="form_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Detail Transaksi Gaji</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="fw-bolder">Periode</div><div id="d_periode" class="text-muted"></div></div>
                    <div class="col-md-8"><div class="fw-bolder">SDM</div><div id="d_sdm" class="text-muted"></div></div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="fw-bolder">Total Penghasilan</div><div id="d_total_penghasilan" class="text-muted"></div></div>
                    <div class="col-md-4"><div class="fw-bolder">Total Potongan</div><div id="d_total_potongan" class="text-muted"></div></div>
                    <div class="col-md-4"><div class="fw-bolder">Take Home Pay</div><div id="d_thp" class="text-muted"></div></div>
                </div>

                <hr>

                <h6 class="fw-bolder mb-3">Rincian (gaji_detail)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-row-bordered" id="table_detail_gaji">
                        <thead>
                        <tr class="text-gray-600 fw-bold fs-7">
                            <th>#</th>
                            <th>Komponen</th>
                            <th>Nominal</th>
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
