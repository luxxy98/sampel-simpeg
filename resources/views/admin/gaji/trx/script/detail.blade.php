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
        // Jika ada nama_komponen dari join, gunakan itu
        if (detail.nama_komponen) {
            return detail.nama_komponen;
        }
        // Jika tidak ada (potongan), ambil dari keterangan
        if (detail.keterangan) {
            // Keterangan format: "Potongan ALPHA: 3 hari x Rp 225.806"
            // Ambil bagian sebelum ":"
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

            $('#d_periode').text(trx.periode_label || ('Periode #' + trx.id_periode));
            $('#d_sdm').text(trx.sdm_nama || ('SDM #' + trx.id_sdm));
            $('#d_total_penghasilan').text(formatRupiah(trx.total_penghasilan));
            $('#d_total_potongan').text(formatRupiah(trx.total_potongan));
            $('#d_thp').text(formatRupiah(trx.total_take_home_pay));
            
            // Calculate Uang Lembur from detail (find items with 'Lembur' in keterangan)
            const uangLembur = detail
                .filter(d => d.keterangan && d.keterangan.toLowerCase().includes('lembur'))
                .reduce((sum, d) => sum + (parseFloat(d.nominal) || 0), 0);
            $('#d_uang_lembur').text(formatRupiah(uangLembur));

            const $tbody = $('#table_detail_gaji tbody');
            $tbody.html('');
            detail.forEach((d, i) => {
                $tbody.append(`
                    <tr>
                        <td>${i+1}</td>
                        <td>${getKomponenName(d)}</td>
                        <td>${formatRupiah(d.nominal)}</td>
                        <td>${d.keterangan ?? '-'}</td>
                    </tr>
                `);
            });

            $('#form_detail').modal('show');
        } catch (e) {
            ErrorHandler.handleError(e);
        }
    }
</script>


