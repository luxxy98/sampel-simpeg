<script defer>
    async function openDetailAbsensi(id_absensi) {
        try {
            DataManager.openLoading();
            const url = '{{ route('admin.absensi.show', ['id' => '___ID___']) }}'.replace('___ID___', id_absensi);
            const res = await DataManager.readData(url);

            Swal.close();

            if (!res.success) {
                Swal.fire('Peringatan', res.message || 'Gagal memuat detail', 'warning');
                return;
            }

            const a = res.data.absensi;
            const detail = res.data.detail || [];

            $('#d_tanggal').text(a.tanggal ? formatter.formatDate(a.tanggal) : '-');
            $('#d_sdm').text(a.sdm_nama || ('SDM #' + a.id_sdm));
            $('#d_jadwal').text(a.jadwal_nama || ('Jadwal #' + a.id_jadwal_karyawan));

            $('#d_total_jam_kerja').text(a.total_jam_kerja ?? '0.00');
            $('#d_total_terlambat').text(a.total_terlambat ?? '0.00');
            $('#d_total_pulang_awal').text(a.total_pulang_awal ?? '0.00');
            $('#d_total_lembur').text(a.total_lembur ?? '0.00');
            
            // Lembur Info Section
            const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num || 0);
            const totalLembur = parseFloat(a.total_lembur) || 0;
            
            if (totalLembur > 0) {
                $('#d_lembur_section').show();
                $('#d_tarif_lembur_nama').text(a.tarif_lembur_nama || '-');
                $('#d_tarif_per_jam').text(formatRupiah(a.tarif_per_jam));
                $('#d_nominal_lembur').text(formatRupiah(a.nominal_lembur));
            } else {
                $('#d_lembur_section').hide();
            }

            const $tbody = $('#table_detail_view tbody');
            $tbody.html('');
            detail.forEach((d, i) => {
                $tbody.append(`
                    <tr>
                        <td>${i+1}</td>
                        <td>${d.nama_absen || ('Jenis #' + d.id_jenis_absen)}</td>
                        <td>${d.waktu_mulai ? formatter.formatDate(d.waktu_mulai) : '-'}</td>
                        <td>${d.waktu_selesai ? formatter.formatDate(d.waktu_selesai) : '-'}</td>
                        <td>${d.durasi_jam ?? '0.00'}</td>
                        <td>${d.lokasi_pulang ?? '-'}</td>
                    </tr>
                `);
            });

            $('#form_detail').modal('show');
        } catch (err) {
            ErrorHandler.handleError(err);
        }
    }
</script>
