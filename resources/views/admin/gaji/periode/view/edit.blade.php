<div class="modal fade" id="form_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Periode Gaji</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="bt_submit_edit">
                    <input type="hidden" id="edit_id_periode" name="id_periode">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Tahun</label>
                            <input type="number" id="edit_tahun" name="tahun" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Bulan</label>
                            <select id="edit_bulan" name="bulan" class="form-select form-select-sm" data-control="select2">
                                @for($i=1;$i<=12;$i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Tanggal Mulai</label>
                            <input type="text" id="edit_tanggal_mulai" name="tanggal_mulai" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Tanggal Selesai</label>
                            <input type="text" id="edit_tanggal_selesai" name="tanggal_selesai" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Tanggal Penggajian</label>
                            <input type="text" id="edit_tanggal_penggajian" name="tanggal_penggajian" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Status</label>
                            <select id="edit_status" name="status" class="form-select form-select-sm" data-control="select2">
                                <option value="DRAFT">DRAFT</option>
                                <option value="PROSES">PROSES</option>
                                <option value="SELESAI">SELESAI</option>
                                <option value="DIBATALKAN">DIBATALKAN</option>
                                <option value="CLOSE">CLOSE</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Status Peninjauan</label>
                            <select id="edit_status_peninjauan" name="status_peninjauan" class="form-select form-select-sm" data-control="select2">
                                <option value="DRAFT">DRAFT</option>
                                <option value="DISETUJUI">DISETUJUI</option>
                                <option value="GAGAL">GAGAL</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-6">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
