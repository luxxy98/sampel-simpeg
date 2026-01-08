<div class="modal fade" id="form_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Master Jadwal Kerja</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="bt_submit_create_jadwal">
                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Nama Jadwal</label>
                        <input type="text" id="nama_jadwal" name="nama_jadwal" class="form-control form-control-sm">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Jam Masuk</label>
                            <input type="time" step="1" id="jam_masuk" name="jam_masuk" class="form-control form-control-sm" value="07:00:00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Jam Pulang</label>
                            <input type="time" step="1" id="jam_pulang" name="jam_pulang" class="form-control form-control-sm" value="15:00:00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Keterangan</label>
                        <input type="text" id="keterangan" name="keterangan" class="form-control form-control-sm">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>