<script>
    function openDetailAbsensi(id) {
        $.get("{{ url('admin/absensi') }}/" + id + "/detail", function (res) {
            // res = { absensi: {...}, detail: [...] }
            $('#detail_tanggal').text(res.absensi.tanggal);
            $('#detail_sdm').text(res.absensi.nama_sdm);
            $('#detail_jadwal').text(res.absensi.jadwal);
            $('#detail_total_jam').text(res.absensi.total_jam_kerja);
            $('#detail_total_terlambat').text(res.absensi.total_terlambat);
            $('#detail_total_pulang_awal').text(res.absensi.total_pulang_awal);

            let tbody = '';
            res.detail.forEach(function (d) {
                tbody += `
                    <tr>
                        <td>${d.nama_absen}</td>
                        <td>${d.kategori}</td>
                        <td>${d.waktu_mulai ?? '-'}</td>
                        <td>${d.waktu_selesai ?? '-'}</td>
                        <td>${d.durasi_jam}</td>
                        <td>${d.lokasi_pulang ?? '-'}</td>
                    </tr>`;
            });

            $('#detail_tbody_absensi_detail').html(tbody);
            $('#form_detail_absensi').modal('show');
        });
    }
</script>