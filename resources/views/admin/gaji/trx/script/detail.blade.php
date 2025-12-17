<script defer>
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
            $('#d_total_penghasilan').text(trx.total_penghasilan ?? '0.00');
            $('#d_total_potongan').text(trx.total_potongan ?? '0.00');
            $('#d_thp').text(trx.total_take_home_pay ?? '0.00');

            const $tbody = $('#table_detail_gaji tbody');
            $tbody.html('');
            detail.forEach((d, i) => {
                $tbody.append(`
                    <tr>
                        <td>${i+1}</td>
                        <td>${d.nama_komponen || ('Komponen #' + (d.id_gaji_komponen ?? '-'))}</td>
                        <td>${d.nominal ?? '0.00'}</td>
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
