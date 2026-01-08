<div class="modal fade" id="form_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Master Jadwal Kerja</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="bt_submit_edit_jadwal">
                    <input type="hidden" id="id_jadwal_edit" name="id_jadwal">

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Nama Jadwal</label>
                        <input type="text" id="nama_jadwal_edit" name="nama_jadwal" class="form-control form-control-sm">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Jam Masuk</label>
                            <input type="time" step="1" id="jam_masuk_edit" name="jam_masuk" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Jam Pulang</label>
                            <input type="time" step="1" id="jam_pulang_edit" name="jam_pulang" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Keterangan</label>
                        <input type="text" id="keterangan_edit" name="keterangan" class="form-control form-control-sm">
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