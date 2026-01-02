<script defer>
    $('#modal_create_jenis').on('show.bs.modal', function () {
        $('#jenis').select2({ dropdownParent: $('#modal_create_jenis') });

        $('#form_create_jenis').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((r) => {
                if (!r.value) return;

                DataManager.openLoading();
                const input = {
                    nama_komponen: $('#nama_komponen').val(),
                    jenis: $('#jenis').val(),
                };

                DataManager.postData('{{ route('admin.gaji.jenis-komponen.store') }}', input)
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success');
                            setTimeout(() => location.reload(), 700);
                            return;
                        }

                        if (!res.success && res.errors) {
                            const v = new ValidationErrorFilter();
                            v.filterValidationErrors(res);
                            Swal.fire('Warning', 'Validasi bermasalah', 'warning');
                            return;
                        }

                        Swal.fire('Peringatan', res.message || 'Gagal simpan', 'warning');
                    })
                    .catch(err => ErrorHandler.handleError(err));
            });
        });
    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select').val('PENGHASILAN').trigger('change');
        $m.find('.invalid-feedback, .text-danger').remove();
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    });
</script>
