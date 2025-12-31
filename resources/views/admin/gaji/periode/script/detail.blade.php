<script defer>
    async function openDetailPeriode(id_periode) {
        try {
            DataManager.openLoading();
            const url = '{{ route('admin.gaji.periode.show', ['id' => '___ID___']) }}'.replace('___ID___', id_periode);
            const res = await DataManager.readData(url);
            Swal.close();

            if (!res.success) {
                Swal.fire('Peringatan', res.message || 'Gagal memuat detail', 'warning');
                return;
            }

            const p = res.data;
            $('#d_tahun').text(p.tahun ?? '-');
            $('#d_bulan').text(p.bulan ?? '-');
            $('#d_status').text(p.status ?? '-');
            $('#d_mulai').text(p.tanggal_mulai ? formatter.formatDate(p.tanggal_mulai) : '-');
            $('#d_selesai').text(p.tanggal_selesai ? formatter.formatDate(p.tanggal_selesai) : '-');
            $('#d_penggajian').text(p.tanggal_penggajian ? formatter.formatDate(p.tanggal_penggajian) : '-');
            $('#d_peninjauan').text(p.status_peninjauan ?? '-');

            $('#form_detail').modal('show');
        } catch (e) {
            ErrorHandler.handleError(e);
        }
    }
</script>
