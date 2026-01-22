<script defer>
    function formatRupiah(angka) {
        if (angka === null || angka === undefined) return 'Rp 0';
        const num = parseFloat(angka);
        const isNegative = num < 0;
        const absNum = Math.abs(num);
        const formatted = absNum.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        return (isNegative ? '-Rp ' : 'Rp ') + formatted;
    }

    function getKomponenName(detail) {
        if (detail.nama_komponen) {
            return detail.nama_komponen;
        }
        if (detail.keterangan) {
            const parts = detail.keterangan.split(':');
            if (parts.length > 0) {
                return parts[0].trim();
            }
        }
        return 'Komponen Lainnya';
    }

    async function openDetailGaji(id_gaji) {
        try {
            DataManager.openLoading();
            const url = '{{ route('admin.gaji.trx.show', ['id' => '___ID___']) }}'.replace('___ID___', id_gaji);
            const res = await DataManager.readData(url);
            Swal.close();

            if (!res.success) {
                Swal.fire('Peringatan', res.message || 'Gagal memuat detail', 'warning');
                return;
            }

            const trx = res.data.trx;
            const detail = res.data.detail || [];

            // Info Karyawan
            $('#d_sdm').text(trx.sdm_nama || ('SDM #' + trx.id_sdm));
            $('#d_jabatan_unit').text(trx.jabatan || '');

            // Periode
            const bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const periodeText = trx.bulan && trx.tahun 
                ? `${bulanNama[trx.bulan]} ${trx.tahun}` 
                : trx.periode_label;
            $('#d_periode').text(periodeText);

            // Status
            let statusBadge;
            if (trx.status === 'DISETUJUI') {
                statusBadge = '<span class="badge bg-success">DISETUJUI</span>';
            } else if (trx.status === 'DRAFT') {
                statusBadge = '<span class="badge bg-warning text-dark">DRAFT</span>';
            } else if (trx.status === 'DIBATALKAN') {
                statusBadge = '<span class="badge bg-danger">DIBATALKAN</span>';
            } else {
                statusBadge = '<span class="badge bg-secondary">-</span>';
            }
            $('#d_status').html(statusBadge);

            // Ringkasan Gaji
            $('#d_total_penghasilan').text(formatRupiah(trx.total_penghasilan));
            $('#d_total_potongan').text(formatRupiah(trx.total_potongan));
            
            // Calculate Uang Lembur from detail (find items with 'Lembur' in keterangan)
            const uangLembur = detail
                .filter(d => d.keterangan && d.keterangan.toLowerCase().includes('lembur'))
                .reduce((sum, d) => sum + (parseFloat(d.nominal) || 0), 0);
            $('#d_uang_lembur').text(formatRupiah(uangLembur));
            $('#d_thp').text(formatRupiah(trx.total_take_home_pay));

            // Hide timestamps section (data not available)
            $('#d_created_at').parent().parent().parent().hide();

            // Rincian Komponen
            const $tbody = $('#table_detail_gaji tbody');
            $tbody.html('');
            detail.forEach((d, i) => {
                const nominalClass = parseFloat(d.nominal) < 0 ? 'text-danger' : 'text-success';
                $tbody.append(`
                    <tr>
                        <td>${i+1}</td>
                        <td>${getKomponenName(d)}</td>
                        <td class="text-end ${nominalClass} fw-bold">${formatRupiah(d.nominal)}</td>
                        <td class="text-muted small">${d.keterangan ?? '-'}</td>
                    </tr>
                `);
            });

            $('#form_detail').modal('show');
        } catch (e) {
            ErrorHandler.handleError(e);
        }
    }
</script>
