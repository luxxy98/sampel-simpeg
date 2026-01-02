<div class="modal fade" id="modal_edit_komponen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Komponen</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="form_edit_komponen">
                    <input type="hidden" id="edit_id_gaji_komponen" name="id_gaji_komponen">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Jabatan</label>
                            <select id="edit_id_jabatan" name="id_jabatan" class="form-select form-select-sm" data-control="select2">
                                @isset($jabatanOptions)
                                    @foreach($jabatanOptions as $j)
                                        <option value="{{ $j['id_jabatan'] }}">{{ $j['nama_jabatan'] ?? ('Jabatan #' . $j['id_jabatan']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Jenis Komponen</label>
                            <select id="edit_id_jenis_komponen" name="id_jenis_komponen" class="form-select form-select-sm" data-control="select2">
                                @isset($jenisKomponenOptions)
                                    @foreach($jenisKomponenOptions as $k)
                                        <option value="{{ $k['id_jenis_komponen'] }}">
                                            {{ $k['nama_komponen'] ?? ('Jenis #' . $k['id_jenis_komponen']) }}
                                            ({{ $k['jenis'] ?? '-' }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="fw-bolder mb-1">Nominal</label>
                            <input type="number" step="0.01" id="edit_nominal" name="nominal" class="form-control form-control-sm">
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
