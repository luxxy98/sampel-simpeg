<script defer>
    // Buka modal edit (dipanggil dari tombol action)
    function openEditJadwalKaryawan(payload) {
        $('#id_jadwal_karyawan_edit').val(payload.id_jadwal_karyawan);

        // set value select (options sudah ada dari blade)
        $('#id_sdm_edit').val(payload.id_sdm).trigger('change');
        $('#id_jadwal_edit').val(payload.id_jadwal).trigger('change');

        // set tanggal
        $('#tanggal_mulai_edit').val(payload.tanggal_mulai);
        $('#tanggal_selesai_edit').val(payload.tanggal_selesai);

        // show modal
        $('#form_edit').modal('show');
    }

    // Inisialisasi select2 ketika modal tampil (aman untuk modal bootstrap)
    $('#form_edit').on('shown.bs.modal', function () {
        const $modal = $('#form_edit');

        // destroy dulu biar tidak double init
        if ($('#id_sdm_edit').hasClass("select2-hidden-accessible")) {
            $('#id_sdm_edit').select2('destroy');
        }
        if ($('#id_jadwal_edit').hasClass("select2-hidden-accessible")) {
            $('#id_jadwal_edit').select2('destroy');
        }

        $('#id_sdm_edit').select2({ dropdownParent: $modal, width: '100%' });
        $('#id_jadwal_edit').select2({ dropdownParent: $modal, width: '100%' });
    });

    // Handle submit edit (dibinding sekali, tidak nempel berulang)
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
                        Swal.fire('Success', res.message || 'Berhasil update', 'success');

                        // kalau ada datatable, reload saja. kalau tidak ada, reload halaman.
                        try {
                            if (window.table && typeof window.table.ajax !== 'undefined') {
                                window.table.ajax.reload(null, false);
                                $('#form_edit').modal('hide');
                                return;
                            }
                        } catch (e) {}

                        setTimeout(() => location.reload(), 600);
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
</script>
