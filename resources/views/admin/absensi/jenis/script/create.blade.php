<script defer>
    $('#form_create').on('show.bs.modal', function () {
        $('#kategori, #potong_gaji').select2({ dropdownParent: $('#form_create') });

        $('#bt_submit_create_jenis').off('submit').on('submit', function (e) {
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
                    nama_absen: $('#nama_absen').val(),
                    kategori: $('#kategori').val(),
                    potong_gaji: $('#potong_gaji').val(),
                };

                DataManager.postData('{{ route('admin.absensi.jenis.store') }}', input)
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success');
                            setTimeout(() => location.reload(), 800);
                            return;
                        }
                        if (!res.success && res.errors) {
                            const v = new ValidationErrorFilter();
                            v.filterValidationErrors(res);
                            const firstError = Object.values(res.errors).flat()[0];
                            Swal.fire('Warning', firstError || 'Validasi bermasalah', 'warning');
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
        $m.find('select').val('').trigger('change');
        $m.find('.invalid-feedback, .text-danger').remove();
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    });
</script>
