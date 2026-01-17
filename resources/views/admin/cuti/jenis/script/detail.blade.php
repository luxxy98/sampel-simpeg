<script defer>
    $("#form_detail").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");
        const detail = '{{ route('admin.cuti.jenis.show', [':id']) }}';

        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#detail_nama_jenis").text(response.data.nama_jenis ?? '-');

                    const maks = (response.data.maks_hari_per_tahun === null || response.data.maks_hari_per_tahun === '' || typeof response.data.maks_hari_per_tahun === 'undefined')
                        ? '-'
                        : response.data.maks_hari_per_tahun;
                    $("#detail_maks_hari_per_tahun").text(maks);

                    const st = response.data.status ?? '-';
                    $("#detail_status").html(st === 'active'
                        ? '<span class="badge badge-success">Aktif</span>'
                        : (st === 'block'
                            ? '<span class="badge badge-secondary">Non Aktif</span>'
                            : st
                        )
                    );

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
