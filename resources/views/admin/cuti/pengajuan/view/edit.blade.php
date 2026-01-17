<div class="modal fade" id="form_edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengajuan Cuti</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_id" />

                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Pegawai (SDM)</span>
                                </label>
                                <select id="edit_id_sdm" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Pilih pegawai" required>
                                    <option></option>
                                    @foreach($sdmOptions as $r)
                                        <option value="{{ $r['id_sdm'] }}">{{ $r['nama'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Jenis Cuti</span>
                                </label>
                                <select id="edit_id_jenis_cuti" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Pilih jenis cuti" required>
                                    <option></option>
                                    @foreach($jenisOptions as $r)
                                        <option value="{{ $r['id_jenis_cuti'] }}">{{ $r['nama_jenis'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Tanggal Mulai</span>
                                </label>
                                <input type="date" id="edit_tanggal_mulai" class="form-control form-control-sm fs-sm-8 fs-lg-6" required />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Tanggal Selesai</span>
                                </label>
                                <input type="date" id="edit_tanggal_selesai" class="form-control form-control-sm fs-sm-8 fs-lg-6" required />
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex flex-column mb-2">
                                <label class="d-flex align-items-center fs-sm-8 fs-lg-6 fw-bolder mb-1 required">
                                    <span>Alasan</span>
                                </label>
                                <textarea id="edit_alasan" rows="2" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="255" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-warning py-2 mb-0 fs-sm-8 fs-lg-6">
                                <strong>Info:</strong> pengajuan yang sudah <b>disetujui/ditolak</b> tidak bisa diedit (controller sudah mengunci).
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
