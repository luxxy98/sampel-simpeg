<script defer>
    async function openDetailDistribusi(id) {
        try {
            DataManager.openLoading();
            const url = '{{ route('admin.gaji.distribusi.show', ['id' => '___ID___']) }}'.replace('___ID___', id);
            const res = await DataManager.readData(url);
            Swal.close();

            if (!res.success) {
                Swal.fire('Peringatan', res.message || 'Gagal memuat detail', 'warning');
                return;
            }

            const d = res.data;

            $('#d_periode').text(d.periode_label || ('Periode #' + d.id_periode));
            $('#d_sdm').text(d.sdm_nama || ('SDM #' + d.id_sdm));
            $('#d_rekening').text(d.rekening_label || (d.id_rekening ? ('Rekening #' + d.id_rekening) : '-'));
            $('#d_jumlah').text(d.jumlah_transfer ?? '0.00');
            $('#d_status').text(d.status_transfer ?? '-');
            $('#d_tanggal').text(d.tanggal_transfer ? formatter.formatDate(d.tanggal_transfer) : '-');
            $('#d_catatan').text(d.catatan ?? '-');

            $('#modal_detail').modal('show');
        } catch (e) {
            ErrorHandler.handleError(e);
        }
    }
</script>
