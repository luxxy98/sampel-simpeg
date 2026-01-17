<script defer>
    $("#form_edit").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");

        $('#edit_id_sdm').select2({ dropdownParent: $('#form_edit'), width: '100%' });
        $('#edit_id_jenis_cuti').select2({ dropdownParent: $('#form_edit'), width: '100%' });

        const detail = '{{ route('admin.cuti.pengajuan.show', [':id']) }}';
        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#edit_id").val(response.data.id_cuti);
                    $("#edit_id_sdm").val(response.data.id_sdm).trigger('change');
                    $("#edit_id_jenis_cuti").val(response.data.id_jenis_cuti).trigger('change');
                    $("#edit_tanggal_mulai").val(response.data.tanggal_mulai ?? '');
                    $("#edit_tanggal_selesai").val(response.data.tanggal_selesai ?? '');
                    $("#edit_alasan").val(response.data.alasan ?? '');
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
                        id_sdm: $("#edit_id_sdm").val(),
                        id_jenis_cuti: $("#edit_id_jenis_cuti").val(),
                        tanggal_mulai: $("#edit_tanggal_mulai").val(),
                        tanggal_selesai: $("#edit_tanggal_selesai").val(),
                        alasan: $("#edit_alasan").val(),
                    };

                    const update = '{{ route('admin.cuti.pengajuan.update', [':id']) }}';
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
