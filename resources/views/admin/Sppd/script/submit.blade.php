<script defer>
    $("#form_submit").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");
        $("#submit_id").val(id);

        $("#bt_submit_submit").off('submit').on("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: "Ajukan SPPD ini?",
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Batal',
                focusCancel: true,
            }).then((result) => {
                if (result.value) {
                    DataManager.openLoading();

                    const submit = '{{ route('admin.sppd.submit', [':id']) }}';
                    DataManager.postData(submit.replace(':id', id), {}).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire('Warning', response.message, 'warning');
                        }
                    }).catch(error => ErrorHandler.handleError(error));
                }
            })
        });
    }).on("hidden.bs.modal", function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
    });

    function selesaiConfirmation(id) {
        Swal.fire({
            title: 'Kamu yakin?',
            text: "Tandai SPPD sebagai selesai?",
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            cancelButtonColor: '#dd3333',
            confirmButtonText: 'Ya, Selesai!',
        }).then((result) => {
            if (result.value) {
                DataManager.openLoading();
                const selesai = '{{ route('admin.sppd.selesai', [':id']) }}';
                DataManager.postData(selesai.replace(':id', id), {}).then(response => {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire('Oops...', response.message, 'error');
                    }
                }).catch(error => ErrorHandler.handleError(error));
            }
        });
    }
</script>
