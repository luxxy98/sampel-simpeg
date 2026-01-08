<script defer>
    function openEditJadwalKerja(payload) {
        $('#id_jadwal_edit').val(payload.id_jadwal);
        $('#nama_jadwal_edit').val(payload.nama_jadwal);
        $('#jam_masuk_edit').val(payload.jam_masuk);
        $('#jam_pulang_edit').val(payload.jam_pulang);
        $('#keterangan_edit').val(payload.keterangan);
        $('#form_edit').modal('show');
    }

    $('#form_edit').on('show.bs.modal', function () {
        $('#bt_submit_edit_jadwal').off('submit').on('submit', function (e) {
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
                const id = $('#id_jadwal_edit').val();
                const input = {
                    nama_jadwal: $('#nama_jadwal_edit').val(),
                    jam_masuk: $('#jam_masuk_edit').val(),
                    jam_pulang: $('#jam_pulang_edit').val(),
                    keterangan: $('#keterangan_edit').val(),
                };

                const url = '{{ route('admin.absensi.jadwal-kerja.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

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