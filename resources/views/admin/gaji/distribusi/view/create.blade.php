<div class="modal fade" id="modal_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Distribusi Transfer</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="form_create">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Periode</label>
                            <select id="id_periode" name="id_periode" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Pilih Periode">
                                <option></option>
                                @isset($periodeOptions)
                                    @foreach($periodeOptions as $p)
                                        <option value="{{ $p['id_periode'] }}">
                                            {{ ($p['tahun'] ?? '') . '-' . str_pad(($p['bulan'] ?? 0), 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-8 mt-1">
                                Field <code>gaji_distribusi.id_periode</code>. :contentReference[oaicite:4]{index=4}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Transaksi Gaji</label>
                            <select id="id_gaji" name="id_gaji" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Pilih Transaksi">
                                <option></option>
                                @isset($trxOptions)
                                    @foreach($trxOptions as $t)
                                        <option value="{{ $t['id_gaji'] }}">
                                            {{ $t['label'] ?? ('TRX #' . $t['id_gaji']) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-8 mt-1">
                                Field <code>gaji_distribusi.id_gaji</code> (FK ke <code>gaji_trx</code>). :contentReference[oaicite:5]{index=5}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">SDM</label>
                            <select id="id_sdm" name="id_sdm" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Pilih SDM">
                                <option></option>
                                @isset($sdmOptions)
                                    @foreach($sdmOptions as $s)
                                        <option value="{{ $s['id_sdm'] }}">
                                            {{ $s['nama'] ?? ('SDM #' . $s['id_sdm']) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-8 mt-1">
                                Field <code>gaji_distribusi.id_sdm</code>. :contentReference[oaicite:6]{index=6}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Rekening SDM (opsional)</label>
                            <select id="id_rekening" name="id_rekening" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Pilih Rekening" data-allow-clear="true">
                                <option></option>
                                @isset($rekeningOptions)
                                    @foreach($rekeningOptions as $r)
                                        <option value="{{ $r['id_rekening'] }}">
                                            {{ $r['label'] ?? ('Rekening #' . $r['id_rekening']) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-8 mt-1">
                                Field <code>gaji_distribusi.id_rekening</code> boleh NULL. :contentReference[oaicite:7]{index=7}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Jumlah Transfer</label>
                            <input type="number" step="0.01" id="jumlah_transfer" name="jumlah_transfer"
                                   class="form-control form-control-sm" value="0.00">
                            <div class="text-muted fs-8 mt-1">
                                Field <code>jumlah_transfer</code>. :contentReference[oaicite:8]{index=8}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Status Transfer</label>
                            <select id="status_transfer" name="status_transfer" class="form-select form-select-sm" data-control="select2">
                                <option value="PENDING">PENDING</option>
                                <option value="SUCCESS">SUCCESS</option>
                                <option value="FAILED">FAILED</option>
                            </select>
                            <div class="text-muted fs-8 mt-1">
                                Enum <code>status_transfer</code>. :contentReference[oaicite:9]{index=9}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Tanggal Transfer</label>
                            <input type="text" id="tanggal_transfer" name="tanggal_transfer" class="form-control form-control-sm"
                                   placeholder="YYYY-MM-DD HH:mm:ss">
                            <div class="text-muted fs-8 mt-1">
                                Field <code>tanggal_transfer</code> (datetime). :contentReference[oaicite:10]{index=10}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="fw-bolder mb-1">Catatan</label>
                            <input type="text" id="catatan" name="catatan" class="form-control form-control-sm"
                                   placeholder="Opsional: keterangan transfer / alasan gagal / referensi bank, dll">
                            <div class="text-muted fs-8 mt-1">
                                Field <code>catatan</code>. :contentReference[oaicite:11]{index=11}
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-6">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
