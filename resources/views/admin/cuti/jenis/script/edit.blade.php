<script defer>
    $("#form_edit").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");

        $('#edit_status').select2({ dropdownParent: $('#form_edit'), width: '100%' });

        const detail = '{{ route('admin.cuti.jenis.show', [':id']) }}';
        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#edit_id").val(response.data.id_jenis_cuti);
                    $("#edit_nama_jenis").val(response.data.nama_jenis);

                    const maks = response.data.maks_hari_per_tahun ?? '';
                    $("#edit_maks_hari_per_tahun").val(maks);

                    $("#edit_status").val(response.data.status ?? 'active').trigger('change');
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

                    const maks = $("#edit_maks_hari_per_tahun").val();
                    const input = {
                        nama_jenis: $("#edit_nama_jenis").val(),
                        maks_hari_per_tahun: (maks === '' ? null : maks),
                        status: $("#edit_status").val(),
                    };

                    const update = '{{ route('admin.cuti.jenis.update', [':id']) }}';
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
