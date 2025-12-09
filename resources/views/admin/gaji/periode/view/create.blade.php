<div class="modal fade" id="form_create_periode" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="post" id="bt_submit_create_periode">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Periode Gaji</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Tahun</label>
                            <input type="number" name="tahun" class="form-control form-control-sm"
                                   value="{{ date('Y') }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Bulan</label>
                            <select name="bulan" class="form-select form-select-sm" required>
                                <option value="">-- Bulan --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control form-control-sm" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1 required">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control form-control-sm" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="DRAFT">DRAFT</option>
                                <option value="PROSES">PROSES</option>
                                <option value="SELESAI">SELESAI</option>
                                <option value="DIBATALKAN">DIBATALKAN</option>
                                <option value="CLOSE">CLOSE</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Status Peninjauan</label>
                            <select name="status_peninjauan" class="form-select form-select-sm">
                                <option value="DRAFT">DRAFT</option>
                                <option value="DISETUJUI">DISETUJUI</option>
                                <option value="GAGAL">GAGAL</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fs-sm-8 fs-lg-6 fw-bolder mb-1">Tanggal Penggajian</label>
                            <input type="date" name="tanggal_penggajian" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>