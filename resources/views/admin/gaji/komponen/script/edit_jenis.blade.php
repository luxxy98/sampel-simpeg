<script defer>
    function openEditJenisKomponen(payload) {
        // payload contoh: { id_jenis_komponen, nama_komponen, jenis }
        $('#edit_id_jenis_komponen').val(payload.id_jenis_komponen);
        $('#edit_nama_komponen').val(payload.nama_komponen);
        $('#edit_jenis').val(payload.jenis).trigger('change');
        $('#modal_edit_jenis').modal('show');
    }

    $('#modal_edit_jenis').on('show.bs.modal', function () {
        $('#edit_jenis').select2({ dropdownParent: $('#modal_edit_jenis') });

        $('#form_edit_jenis').off('submit').on('submit', function (e) {
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
                const id = $('#edit_id_jenis_komponen').val();
                const url = '{{ route('admin.gaji.jenis-komponen.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

                const input = {
                    nama_komponen: $('#edit_nama_komponen').val(),
                    jenis: $('#edit_jenis').val(),
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
