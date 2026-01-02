<script defer>
    function openEditKomponenGaji(payload) {
        // payload contoh: { id_gaji_komponen, id_jabatan, id_jenis_komponen, nominal }
        $('#edit_id_gaji_komponen').val(payload.id_gaji_komponen);
        $('#edit_id_jabatan').val(payload.id_jabatan).trigger('change');
        $('#edit_id_jenis_komponen').val(payload.id_jenis_komponen).trigger('change');
        $('#edit_nominal').val(payload.nominal);
        $('#modal_edit_komponen').modal('show');
    }

    $('#modal_edit_komponen').on('show.bs.modal', function () {
        $('#edit_id_jabatan, #edit_id_jenis_komponen').select2({ dropdownParent: $('#modal_edit_komponen') });

        $('#form_edit_komponen').off('submit').on('submit', function (e) {
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
                const id = $('#edit_id_gaji_komponen').val();
                const url = '{{ route('admin.gaji.komponen.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

                const input = {
                    id_jabatan: $('#edit_id_jabatan').val(),
                    id_jenis_komponen: $('#edit_id_jenis_komponen').val(),
                    nominal: $('#edit_nominal').val(),
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
