<div class="modal fade" id="modal_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Edit Distribusi Transfer</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="form_edit">
                    <input type="hidden" id="edit_id_distribusi" name="id_distribusi">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Periode</label>
                            <select id="edit_id_periode" name="id_periode" class="form-select form-select-sm" data-control="select2">
                                @isset($periodeOptions)
                                    @foreach($periodeOptions as $p)
                                        <option value="{{ $p['id_periode'] }}">
                                            {{ ($p['tahun'] ?? '') . '-' . str_pad(($p['bulan'] ?? 0), 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Transaksi Gaji</label>
                            <select id="edit_id_gaji" name="id_gaji" class="form-select form-select-sm" data-control="select2">
                                @isset($trxOptions)
                                    @foreach($trxOptions as $t)
                                        <option value="{{ $t['id_gaji'] }}">
                                            {{ $t['label'] ?? ('TRX #' . $t['id_gaji']) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">SDM</label>
                            <select id="edit_id_sdm" name="id_sdm" class="form-select form-select-sm" data-control="select2">
                                @isset($sdmOptions)
                                    @foreach($sdmOptions as $s)
                                        <option value="{{ $s['id_sdm'] }}">{{ $s['nama'] ?? ('SDM #' . $s['id_sdm']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Rekening SDM (opsional)</label>
                            <select id="edit_id_rekening" name="id_rekening" class="form-select form-select-sm"
                                    data-control="select2" data-allow-clear="true">
                                <option></option>
                                @isset($rekeningOptions)
                                    @foreach($rekeningOptions as $r)
                                        <option value="{{ $r['id_rekening'] }}">{{ $r['label'] ?? ('Rekening #' . $r['id_rekening']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bolder mb-1">Jumlah Transfer</label>
                            <input type="number" step="0.01" id="edit_jumlah_transfer" name="jumlah_transfer"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Status Transfer</label>
                            <select id="edit_status_transfer" name="status_transfer" class="form-select form-select-sm" data-control="select2">
                                <option value="PENDING">PENDING</option>
                                <option value="SUCCESS">SUCCESS</option>
                                <option value="FAILED">FAILED</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bolder mb-1">Tanggal Transfer</label>
                            <input type="text" id="edit_tanggal_transfer" name="tanggal_transfer" class="form-control form-control-sm"
                                   placeholder="YYYY-MM-DD HH:mm:ss">
                        </div>

                        <div class="col-md-12">
                            <label class="fw-bolder mb-1">Catatan</label>
                            <input type="text" id="edit_catatan" name="catatan" class="form-control form-control-sm">
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
