<div class="modal fade" id="form_edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Cuti</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_id" />

                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Nama Jenis</span>
                                </label>
                                <input type="text" id="edit_nama_jenis" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="50" required />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1">
                                    <span>Maks Hari / Tahun</span>
                                </label>
                                <input type="number" id="edit_maks_hari_per_tahun" class="form-control form-control-sm fs-sm-8 fs-lg-6" min="0" max="366" />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Status</span>
                                </label>
                                <select id="edit_status" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Pilih status" required>
                                    <option></option>
                                    <option value="active">Aktif</option>
                                    <option value="block">Non Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
