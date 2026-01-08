<script defer>
    function openEditJadwalKaryawan(payload) {
        $('#id_jadwal_karyawan_edit').val(payload.id_jadwal_karyawan);

        const $sdm = $('#id_sdm_edit');
        const $jadwal = $('#id_jadwal_edit');

        _renderSdmOptions($sdm);
        _renderJadwalOptions($jadwal);

        $sdm.val(payload.id_sdm).trigger('change');
        $jadwal.val(payload.id_jadwal).trigger('change');

        $('#tanggal_mulai_edit').val(payload.tanggal_mulai);
        $('#tanggal_selesai_edit').val(payload.tanggal_selesai);
        $('#form_edit').modal('show');
    }

    $('#form_edit').on('show.bs.modal', function () {
        $('#id_sdm_edit').select2({ dropdownParent: $('#form_edit') });
        $('#id_jadwal_edit').select2({ dropdownParent: $('#form_edit') });

        $('#bt_submit_edit_jadwal_karyawan').off('submit').on('submit', function (e) {
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
                const id = $('#id_jadwal_karyawan_edit').val();
                const input = {
                    id_sdm: $('#id_sdm_edit').val(),
                    id_jadwal: $('#id_jadwal_edit').val(),
                    tanggal_mulai: $('#tanggal_mulai_edit').val(),
                    tanggal_selesai: $('#tanggal_selesai_edit').val(),
                };

                const url = '{{ route('admin.absensi.jadwal-karyawan.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

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