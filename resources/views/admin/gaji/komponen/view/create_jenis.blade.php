<div class="modal fade" id="modal_create_jenis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Jenis Komponen</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="form_create_jenis">
                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Nama Komponen</label>
                        <input type="text" id="nama_komponen" name="nama_komponen" class="form-control form-control-sm"
                               placeholder="Mis. Tunjangan Jabatan / BPJS / Potongan Alpha">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Jenis</label>
                        <select id="jenis" name="jenis" class="form-select form-select-sm" data-control="select2">
                            <option value="PENGHASILAN">PENGHASILAN</option>
                            <option value="POTONGAN">POTONGAN</option>
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
