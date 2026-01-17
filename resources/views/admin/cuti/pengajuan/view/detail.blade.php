<div class="modal fade" id="form_detail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan Cuti</h5>
                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
            </div>

            <div class="modal-body">
                <div id="null_data" style="display:none;">
                    <div class="alert alert-warning">Data tidak ditemukan</div>
                </div>

                <div id="show_data">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <div class="fw-bolder">Nama</div>
                            <div id="detail_nama" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="fw-bolder">Jenis Cuti</div>
                            <div id="detail_jenis" class="text-gray-700"></div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <div class="fw-bolder">Tanggal Mulai</div>
                            <div id="detail_mulai" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="fw-bolder">Tanggal Selesai</div>
                            <div id="detail_selesai" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="fw-bolder">Jumlah Hari</div>
                            <div id="detail_hari" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="fw-bolder">Status</div>
                            <div id="detail_status" class="text-gray-700"></div>
                        </div>

                        <div class="col-md-4 mb-2">
                            <div class="fw-bolder">Tanggal Pengajuan</div>
                            <div id="detail_tgl_pengajuan" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="fw-bolder">Tanggal Persetujuan</div>
                            <div id="detail_tgl_persetujuan" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="fw-bolder">Approved By</div>
                            <div id="detail_approved_by" class="text-gray-700"></div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="fw-bolder">Alasan</div>
                            <div id="detail_alasan" class="text-gray-700"></div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="fw-bolder">Catatan</div>
                            <div id="detail_catatan" class="text-gray-700"></div>
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
