<script defer>
    $('#form_create').on('show.bs.modal', function () {
        $('#bulan, #status, #status_peninjauan').select2({ dropdownParent: $('#form_create') });

        $('#tanggal_mulai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#tanggal_selesai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#tanggal_penggajian').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });

        $('#bt_submit_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan periode?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((r) => {
                if (!r.value) return;

                DataManager.openLoading();
                const input = {
                    tahun: $('#tahun').val(),
                    bulan: $('#bulan').val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_selesai: $('#tanggal_selesai').val(),
                    tanggal_penggajian: $('#tanggal_penggajian').val(),
                    status: $('#status').val(),
                    status_peninjauan: $('#status_peninjauan').val(),
                };

                DataManager.postData('{{ route('admin.gaji.periode.store') }}', input)
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success');
                            setTimeout(() => location.reload(), 800);
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
    });
</script>
