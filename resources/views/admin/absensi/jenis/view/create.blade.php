<div class="modal fade" id="form_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Jenis Absen</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form id="bt_submit_create_jenis">
                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Nama Absen</label>
                        <input type="text" id="nama_absen" name="nama_absen" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Kategori</label>
                        <select id="kategori" name="kategori" class="form-select form-select-sm" data-control="select2">
                            <option value="NORMAL">NORMAL</option>
                            <option value="IZIN">IZIN</option>
                            <option value="SAKIT">SAKIT</option>
                            <option value="ALPHA">ALPHA</option>
                            <option value="CUTI">CUTI</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Potong Gaji</label>
                        <select id="potong_gaji" name="potong_gaji" class="form-select form-select-sm" data-control="select2">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
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
