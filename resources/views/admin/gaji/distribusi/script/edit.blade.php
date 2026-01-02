<script defer>
    function openEditDistribusi(payload) {
        // payload contoh:
        // { id_distribusi, id_periode, id_gaji, id_sdm, id_rekening, jumlah_transfer, status_transfer, tanggal_transfer, catatan }

        $('#edit_id_distribusi').val(payload.id_distribusi);
        $('#edit_id_periode').val(payload.id_periode).trigger('change');
        $('#edit_id_gaji').val(payload.id_gaji).trigger('change');
        $('#edit_id_sdm').val(payload.id_sdm).trigger('change');
        $('#edit_id_rekening').val(payload.id_rekening).trigger('change');

        $('#edit_jumlah_transfer').val(payload.jumlah_transfer);
        $('#edit_status_transfer').val(payload.status_transfer).trigger('change');
        $('#edit_tanggal_transfer').val(payload.tanggal_transfer);
        $('#edit_catatan').val(payload.catatan);

        $('#modal_edit').modal('show');
    }

    $('#modal_edit').on('show.bs.modal', function () {
        $('#edit_id_periode, #edit_id_gaji, #edit_id_sdm, #edit_id_rekening, #edit_status_transfer')
            .select2({ dropdownParent: $('#modal_edit') });

        $('#edit_tanggal_transfer').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        $('#form_edit').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Update data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((r) => {
                if (!r.value) return;

                DataManager.openLoading();

                const id = $('#edit_id_distribusi').val();
                const url = '{{ route('admin.gaji.distribusi.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

                const input = {
                    id_periode: $('#edit_id_periode').val(),
                    id_gaji: $('#edit_id_gaji').val(),
                    id_sdm: $('#edit_id_sdm').val(),
                    id_rekening: $('#edit_id_rekening').val(),
                    jumlah_transfer: $('#edit_jumlah_transfer').val(),
                    status_transfer: $('#edit_status_transfer').val(),
                    tanggal_transfer: $('#edit_tanggal_transfer').val(),
                    catatan: $('#edit_catatan').val(),
                };

                DataManager.postData(url, input)
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

                        Swal.fire('Peringatan', res.message || 'Gagal update', 'warning');
                    })
                    .catch(err => ErrorHandler.handleError(err));
            });
        });
    });
</script>
