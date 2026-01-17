<div class="modal fade" id="form_detail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Jenis Cuti</h5>
                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
            </div>

            <div class="modal-body">
                <div id="null_data" style="display:none;">
                    <div class="alert alert-warning">Data tidak ditemukan</div>
                </div>

                <div id="show_data">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <div class="fw-bolder">Nama Jenis</div>
                            <div id="detail_nama_jenis" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="fw-bolder">Maks Hari / Tahun</div>
                            <div id="detail_maks_hari_per_tahun" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="fw-bolder">Status</div>
                            <div id="detail_status" class="text-gray-700"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
