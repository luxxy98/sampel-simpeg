<div class="modal fade" id="form_detail_absensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Absensi</h5>
                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
            </div>
            <div class="modal-body fs-sm-8 fs-lg-6">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div><strong>Tanggal:</strong> <span id="detail_tanggal"></span></div>
                        <div><strong>SDM:</strong> <span id="detail_sdm"></span></div>
                        <div><strong>Jadwal:</strong> <span id="detail_jadwal"></span></div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Total Jam Kerja:</strong> <span id="detail_total_jam"></span></div>
                        <div><strong>Terlambat:</strong> <span id="detail_total_terlambat"></span></div>
                        <div><strong>Pulang Awal:</strong> <span id="detail_total_pulang_awal"></span></div>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bolder mb-3">Detail Log Absensi</h6>
                <div class="table-responsive border border-dashed rounded p-3">
                    <table class="table table-sm align-middle">
                        <thead class="text-gray-600">
                        <tr>
                            <th>Jenis Absen</th>
                            <th>Kategori</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Durasi (jam)</th>
                            <th>Lokasi Pulang</th>
                        </tr>
                        </thead>
                        <tbody id="detail_tbody_absensi_detail">
                        {{-- diisi via JS dari absensi_detail --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-dark fs-sm-8 fs-lg-6" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>