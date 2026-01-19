<script defer>
    $('#form_create').on('show.bs.modal', function () {
        $('#id_sdm').select2({ dropdownParent: $('#form_create'), width: '100%' });
        $('#id_jenis_cuti').select2({ dropdownParent: $('#form_create'), width: '100%' });

        $('#bt_submit_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: 'Apakah datanya benar dan apa yang anda inginkan?',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                focusCancel: true
            }).then((result) => {
                if (result.value) {
                    DataManager.openLoading();

                    const input = {
                        id_sdm: $('#id_sdm').val(),
                        id_jenis_cuti: $('#id_jenis_cuti').val(),
                        tanggal_mulai: $('#tanggal_mulai').val(),
                        tanggal_selesai: $('#tanggal_selesai').val(),
                        alasan: $('#alasan').val(),
                    };

                    const action = '{{ route('admin.cuti.pengajuan.store') }}';
                    DataManager.postData(action, input).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }

                        if (!response.success && response.errors) {
                            const validationErrorFilter = new ValidationErrorFilter();
                            validationErrorFilter.filterValidationErrors(response);
                            
                            // Ambil pesan error pertama dari response.errors
                            const firstError = Object.values(response.errors)[0];
                            const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                            Swal.fire('Validasi Gagal', errorMessage, 'warning');
                        }

                        if (!response.success && !response.errors) {
                            Swal.fire('Peringatan', response.message, 'warning');
                        }
                    }).catch(error => ErrorHandler.handleError(error));
                }
            });
        });
    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select, textarea').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
    });
</script>
