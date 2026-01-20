<div class="modal fade" id="form_create" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form method="post" id="bt_submit_create">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah SPD</h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="fw-bolder mb-1 required">Pegawai (SDM)</label>
                            <select id="id_sdm" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Pilih pegawai" required>
                                <option></option>
                                @foreach($sdmOptions as $r)
                                    <option value="{{ $r['id_sdm'] }}">{{ $r['nama'] }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Nomor Surat</label>
                            <input type="text" id="nomor_surat" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="50" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="fw-bolder mb-1 required">Tanggal Surat</label>
                            <input type="date" id="tanggal_surat" class="form-control form-control-sm fs-sm-8 fs-lg-6" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="fw-bolder mb-1 required">Tanggal Berangkat</label>
                            <input type="date" id="tanggal_berangkat" class="form-control form-control-sm fs-sm-8 fs-lg-6" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="fw-bolder mb-1 required">Tanggal Pulang</label>
                            <input type="date" id="tanggal_pulang" class="form-control form-control-sm fs-sm-8 fs-lg-6" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1 required">Tujuan</label>
                            <input type="text" id="tujuan" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="120" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Instansi Tujuan</label>
                            <input type="text" id="instansi_tujuan" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="120" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Transportasi</label>
                            <input type="text" id="transportasi" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="80" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <label class="fw-bolder mb-1 required">Maksud Tugas</label>
                            <textarea id="maksud_tugas" rows="2" class="form-control form-control-sm fs-sm-8 fs-lg-6" maxlength="255" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Biaya Transport</label>
                            <input type="number" id="biaya_transport" class="form-control form-control-sm fs-sm-8 fs-lg-6" min="0" value="0" />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Biaya Penginapan</label>
                            <input type="number" id="biaya_penginapan" class="form-control form-control-sm fs-sm-8 fs-lg-6" min="0" value="0" />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Uang Harian</label>
                            <input type="number" id="uang_harian" class="form-control form-control-sm fs-sm-8 fs-lg-6" min="0" value="0" />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bolder mb-1">Biaya Lainnya</label>
                            <input type="number" id="biaya_lainnya" class="form-control form-control-sm fs-sm-8 fs-lg-6" min="0" value="0" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-secondary py-2 mb-0 fs-sm-8 fs-lg-6">
                                Status awal otomatis <b>Draft</b>. Setelah dibuat, klik tombol <b>Ajukan</b> untuk approval.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
