<div class="modal fade" id="form_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Jadwal Karyawan</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="bt_submit_edit_jadwal_karyawan">
                    <input type="hidden" id="id_jadwal_karyawan_edit" name="id_jadwal_karyawan">

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">SDM</label>
                        <select id="id_sdm_edit" name="id_sdm" class="form-select form-select-sm" data-control="select2"></select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Jadwal Kerja</label>
                        <select id="id_jadwal_edit" name="id_jadwal" class="form-select form-select-sm" data-control="select2"></select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai_edit" name="tanggal_mulai" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai_edit" name="tanggal_selesai" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>