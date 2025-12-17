<script defer>
    function openEditPeriode(payload) {
        // payload contoh: { id_periode, tahun, bulan, tanggal_mulai, tanggal_selesai, tanggal_penggajian, status, status_peninjauan }
        $('#edit_id_periode').val(payload.id_periode);
        $('#edit_tahun').val(payload.tahun);
        $('#edit_bulan').val(payload.bulan).trigger('change');
        $('#edit_tanggal_mulai').val(payload.tanggal_mulai);
        $('#edit_tanggal_selesai').val(payload.tanggal_selesai);
        $('#edit_tanggal_penggajian').val(payload.tanggal_penggajian);
        $('#edit_status').val(payload.status).trigger('change');
        $('#edit_status_peninjauan').val(payload.status_peninjauan).trigger('change');
        $('#form_edit').modal('show');
    }

    $('#form_edit').on('show.bs.modal', function () {
        $('#edit_bulan, #edit_status, #edit_status_peninjauan').select2({ dropdownParent: $('#form_edit') });

        $('#edit_tanggal_mulai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#edit_tanggal_selesai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#edit_tanggal_penggajian').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });

        $('#bt_submit_edit').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Update periode?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((r) => {
                if (!r.value) return;

                DataManager.openLoading();
                const id = $('#edit_id_periode').val();
                const url = '{{ route('admin.gaji.periode.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

                const input = {
                    tahun: $('#edit_tahun').val(),
                    bulan: $('#edit_bulan').val(),
                    tanggal_mulai: $('#edit_tanggal_mulai').val(),
                    tanggal_selesai: $('#edit_tanggal_selesai').val(),
                    tanggal_penggajian: $('#edit_tanggal_penggajian').val(),
                    status: $('#edit_status').val(),
                    status_peninjauan: $('#edit_status_peninjauan').val(),
                };

                DataManager.postData(url, input)
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
                        Swal.fire('Peringatan', res.message || 'Gagal update', 'warning');
                    })
                    .catch(err => ErrorHandler.handleError(err));
            });
        });
    });
</script>
