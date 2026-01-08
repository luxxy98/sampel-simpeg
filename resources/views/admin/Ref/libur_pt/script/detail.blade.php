<script defer>
    $("#form_detail").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");
        const detail = '{{ route('admin.ref.libur-pt.show', [':id']) }}';

        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#detail_tanggal").text(response.data.tanggal);
                    $("#detail_keterangan").text(response.data.keterangan);
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
