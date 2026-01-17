<script defer>
    function badgeStatus(st) {
        if (st === 'draft') return '<span class="badge badge-secondary">Draft</span>';
        if (st === 'diajukan') return '<span class="badge badge-warning">Diajukan</span>';
        if (st === 'disetujui') return '<span class="badge badge-success">Disetujui</span>';
        if (st === 'ditolak') return '<span class="badge badge-danger">Ditolak</span>';
        if (st === 'selesai') return '<span class="badge badge-success">Selesai</span>';
        return st ?? '-';
    }
    function rupiah(x) {
        const n = parseInt(x ?? 0);
        if (isNaN(n)) return '0';
        return n.toLocaleString('id-ID');
    }

    $("#form_detail").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");
        const detail = '{{ route('admin.sppd.show', [':id']) }}';

        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    const d = response.data;

                    $("#detail_nama").text(d.nama ?? '-');
                    $("#detail_nomor_surat").text(d.nomor_surat ?? '-');
                    $("#detail_tanggal_surat").text(d.tanggal_surat ?? '-');
                    $("#detail_berangkat").text(d.tanggal_berangkat ?? '-');
                    $("#detail_pulang").text(d.tanggal_pulang ?? '-');
                    $("#detail_tujuan").text(d.tujuan ?? '-');
                    $("#detail_instansi").text(d.instansi_tujuan ?? '-');
                    $("#detail_transport").text(d.transportasi ?? '-');
                    $("#detail_maksud").text(d.maksud_tugas ?? '-');

                    $("#detail_bt").text(rupiah(d.biaya_transport));
                    $("#detail_bp").text(rupiah(d.biaya_penginapan));
                    $("#detail_uh").text(rupiah(d.uang_harian));
                    $("#detail_bl").text(rupiah(d.biaya_lainnya));
                    $("#detail_total").text(rupiah(d.total_biaya));

                    $("#detail_status").html(badgeStatus(d.status));
                    $("#detail_catatan").text(d.catatan ?? '-');

                    $("#null_data").hide();
                    $("#show_data").show();
                } else {
                    $("#null_data").show();
                    $("#show_data").hide();
                    Swal.fire('Peringatan', response.message, 'warning');
                }
            }).catch(error => ErrorHandler.handleError(error));
    });
</script>
