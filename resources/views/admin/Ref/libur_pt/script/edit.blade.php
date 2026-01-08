<script defer>
    $("#form_edit").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");

        if ($('#edit_tanggal')[0]?._flatpickr) $('#edit_tanggal')[0]._flatpickr.destroy();
        const fp = $('#edit_tanggal').flatpickr({
            dateFormat: 'Y-m-d',
            altFormat: 'd/m/Y',
            allowInput: false,
            altInput: true,
        });

        const detail = '{{ route('admin.ref.libur-pt.show', [':id']) }}';
        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    fp.setDate(response.data.tanggal, true);
                    $("#edit_keterangan").val(response.data.keterangan);
                } else {
                    Swal.fire('Warning', response.message, 'warning');
                }
            }).catch(error => ErrorHandler.handleError(error));

        $("#bt_submit_edit").off('submit').on("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: "Apakah datanya benar dan apa yang anda inginkan?",
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                focusCancel: true,
            }).then((result) => {
                if (result.value) {
                    DataManager.openLoading();

                    const input = {
                        tanggal: $("#edit_tanggal").val(),
                        keterangan: $("#edit_keterangan").val(),
                    };

                    const update = '{{ route('admin.ref.libur-pt.update', [':id']) }}';
                    DataManager.putData(update.replace(':id', id), input).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }

                        if (!response.success && response.errors) {
                            const validationErrorFilter = new ValidationErrorFilter("edit_");
                            validationErrorFilter.filterValidationErrors(response);
                            Swal.fire('Peringatan', 'Isian Anda belum lengkap atau tidak valid.', 'warning');
                        }

                        if (!response.success && !response.errors) {
                            Swal.fire('Warning', response.message, 'warning');
                        }
                    }).catch(error => ErrorHandler.handleError(error));
                }
            })
        });
    }).on("hidden.bs.modal", function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select, textarea').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
    });
</script>
