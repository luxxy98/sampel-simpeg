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

            // Format rupiah
            function formatRp(num) {
                if (num === null || num === undefined) return 'Rp 0';
                const number = parseFloat(num);
                if (isNaN(number)) return 'Rp 0';
                return 'Rp ' + number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }

            $('#d_periode').text(d.periode_label || ('Periode #' + d.id_periode));
            $('#d_sdm').text(d.sdm_nama || ('SDM #' + d.id_sdm));
            $('#d_rekening').text(d.rekening_label || (d.id_rekening ? ('Rekening #' + d.id_rekening) : '-'));
            $('#d_jumlah').html('<span class="text-success fw-bold">' + formatRp(d.jumlah_transfer) + '</span>');
            $('#d_status').text(d.status_transfer ?? '-');
            $('#d_tanggal').text(d.tanggal_transfer ? formatter.formatDate(d.tanggal_transfer) : '-');
            $('#d_catatan').text(d.catatan ?? '-');

            $('#modal_detail').modal('show');
        } catch (e) {
            ErrorHandler.handleError(e);
        }
    }
</script>
