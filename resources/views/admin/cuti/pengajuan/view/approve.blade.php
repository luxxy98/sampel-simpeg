<div class="modal fade" id="form_approve" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_approve">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve / Tolak Cuti</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="approve_id" />

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="fw-bolder mb-1">Nama</div>
                            <div id="approve_nama" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-bolder mb-1">Jenis</div>
                            <div id="approve_jenis" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bolder mb-1">Mulai</div>
                            <div id="approve_mulai" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bolder mb-1">Selesai</div>
                            <div id="approve_selesai" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-2">
                            <div class="fw-bolder mb-1">Hari</div>
                            <div id="approve_hari" class="text-gray-700"></div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Keputusan</span>
                                </label>
                                <select id="approve_status" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Pilih keputusan" required>
                                    <option></option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1">
                                    <span>Catatan (opsional)</span>
                                </label>
                                <textarea id="approve_catatan" rows="2" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="255"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-secondary py-2 mb-0 fs-sm-8 fs-lg-6">
                                Setelah diproses, status tidak bisa diubah lagi lewat edit biasa (controller sudah mengunci).
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-success fs-sm-8 fs-lg-6">Simpan Keputusan</button>
                </div>
            </div>
        </form>
    </div>
</div>
