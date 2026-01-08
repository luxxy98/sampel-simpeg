<div class="modal fade" id="form_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Edit Libur Nasional</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="bt_submit_edit">
                    <input type="hidden" id="edit_id" />
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-bold mb-2">Tanggal</label>
                        <input type="text" class="form-control form-control-solid" placeholder="Pilih tanggal" id="edit_tanggal" name="tanggal" />
                        <span class="text-danger error-text tanggal_error"></span>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-bold mb-2">Keterangan</label>
                        <textarea class="form-control form-control-solid" placeholder="Keterangan" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                        <span class="text-danger error-text keterangan_error"></span>
                    </div>

                    <div class="text-center pt-15">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
