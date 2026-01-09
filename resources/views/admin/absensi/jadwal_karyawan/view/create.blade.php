<div class="modal fade" id="form_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Tambah Jadwal Karyawan</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="bt_submit_create_jadwal_karyawan"
                      method="POST"
                      action="{{ route('admin.absensi.jadwal-karyawan.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">SDM</label>
                        <select id="id_sdm" name="id_sdm" class="form-select form-select-sm"
                                data-control="select2" data-placeholder="Pilih SDM" required>
                            <option></option>
                            @foreach(($sdmOptions ?? []) as $sdm)
                                <option value="{{ $sdm['id_sdm'] }}">{{ $sdm['nama'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bolder mb-1">Jadwal Kerja</label>
                        <select id="id_jadwal" name="id_jadwal" class="form-select form-select-sm"
                                data-control="select2" data-placeholder="Pilih Jadwal Kerja" required>
                            <option></option>
                            @foreach(($jadwalKerjaOptions ?? []) as $j)
                                <option value="{{ $j['id_jadwal'] }}">
                                    {{ $j['nama_jadwal'] }} ({{ $j['jam_masuk'] }} - {{ $j['jam_pulang'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                   class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                   class="form-control form-control-sm" required>
                        </div>
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
