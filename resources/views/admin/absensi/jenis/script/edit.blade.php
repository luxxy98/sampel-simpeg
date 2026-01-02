<script defer>
    function openEditJenis(payload) {
        // payload: { id_jenis_absen, nama_absen, kategori, potong_gaji }
        $('#edit_id_jenis_absen').val(payload.id_jenis_absen);
        $('#edit_nama_absen').val(payload.nama_absen);
        $('#edit_kategori').val(payload.kategori).trigger('change');
        $('#edit_potong_gaji').val(payload.potong_gaji).trigger('change');
        $('#form_edit').modal('show');
    }

    $('#form_edit').on('show.bs.modal', function () {
        $('#edit_kategori, #edit_potong_gaji').select2({ dropdownParent: $('#form_edit') });

        $('#bt_submit_edit_jenis').off('submit').on('submit', function (e) {
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
                const id = $('#edit_id_jenis_absen').val();
                const input = {
                    nama_absen: $('#edit_nama_absen').val(),
                    kategori: $('#edit_kategori').val(),
                    potong_gaji: $('#edit_potong_gaji').val(),
                };

                const url = '{{ route('admin.absensi.jenis.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

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
                            const firstError = Object.values(res.errors).flat()[0];
                            Swal.fire('Warning', firstError || 'Validasi bermasalah', 'warning');
                            return;
                        }
                        Swal.fire('Peringatan', res.message || 'Gagal update', 'warning');
                    })
                    .catch(err => ErrorHandler.handleError(err));
            });
        });
    });
</script>
