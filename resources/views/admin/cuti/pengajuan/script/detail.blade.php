<script defer>
    $("#form_detail").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");
        const detail = '{{ route('admin.cuti.pengajuan.show', [':id']) }}';

        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#detail_nama").text(response.data.nama ?? '-');
                    $("#detail_jenis").text(response.data.nama_jenis ?? '-');
                    $("#detail_mulai").text(response.data.tanggal_mulai ?? '-');
                    $("#detail_selesai").text(response.data.tanggal_selesai ?? '-');
                    $("#detail_hari").text(response.data.jumlah_hari ?? '-');

                    const st = response.data.status ?? '-';
                    $("#detail_status").html(
                        st === 'diajukan' ? '<span class="badge badge-warning">Diajukan</span>' :
                        (st === 'disetujui' ? '<span class="badge badge-success">Disetujui</span>' :
                        (st === 'ditolak' ? '<span class="badge badge-danger">Ditolak</span>' : st))
                    );

                    $("#detail_tgl_pengajuan").text(response.data.tanggal_pengajuan ?? '-');
                    $("#detail_tgl_persetujuan").text(response.data.tanggal_persetujuan ?? '-');
                    $("#detail_approved_by").text(response.data.approver_name ?? '-');
                    $("#detail_alasan").text(response.data.alasan ?? '-');
                    $("#detail_catatan").text(response.data.catatan ?? '-');

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
