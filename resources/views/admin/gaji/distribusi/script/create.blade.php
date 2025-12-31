<script defer>
    $('#modal_create').on('show.bs.modal', function () {
        $('#id_periode, #id_gaji, #id_sdm, #id_rekening, #status_transfer').select2({ dropdownParent: $('#modal_create') });

        $('#tanggal_transfer').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        $('#form_create').off('submit').on('submit', function (e) {
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
                    id_periode: $('#id_periode').val(),
                    id_gaji: $('#id_gaji').val(),
                    id_sdm: $('#id_sdm').val(),
                    id_rekening: $('#id_rekening').val(),
                    jumlah_transfer: $('#jumlah_transfer').val(),
                    status_transfer: $('#status_transfer').val(),
                    tanggal_transfer: $('#tanggal_transfer').val(),
                    catatan: $('#catatan').val(),
                };

                DataManager.postData('{{ route('admin.gaji.distribusi.store') }}', input)
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
        $m.find('select').val('').trigger('change');
        $m.find('.invalid-feedback, .text-danger').remove();
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('#jumlah_transfer').val('0.00');
    });
</script>
