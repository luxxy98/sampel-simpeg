<script defer>
    function _renderSdmOptions($select) {
        $select.empty();
        $select.append(new Option('- Pilih SDM -', '', true, false));
        (sdmOptions || []).forEach((o) => {
            const opt = new Option(o.nama, o.id_sdm, false, false);
            $select.append(opt);
        });
        $select.val('').trigger('change');
    }

    function _renderJadwalOptions($select) {
        $select.empty();
        $select.append(new Option('- Pilih Jadwal -', '', true, false));
        (jadwalOptions || []).forEach((o) => {
            const label = `${o.nama} (${o.jam_masuk} - ${o.jam_pulang})`;
            const val = o.id_jadwal_karyawan; // alias dari id_jadwal
            const opt = new Option(label, val, false, false);
            $select.append(opt);
        });
        $select.val('').trigger('change');
    }

    $('#form_create').on('show.bs.modal', function () {
        const $sdm = $('#id_sdm');
        const $jadwal = $('#id_jadwal');

        _renderSdmOptions($sdm);
        _renderJadwalOptions($jadwal);

        $sdm.select2({ dropdownParent: $('#form_create') });
        $jadwal.select2({ dropdownParent: $('#form_create') });

        $('#bt_submit_create_jadwal_karyawan').off('submit').on('submit', function (e) {
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
                    id_sdm: $sdm.val(),
                    id_jadwal: $jadwal.val(),
                    tanggal_mulai: $('#tanggal_mulai').val(),
                    tanggal_selesai: $('#tanggal_selesai').val(),
                };

                DataManager.postData('{{ route('admin.absensi.jadwal-karyawan.store') }}', input)
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