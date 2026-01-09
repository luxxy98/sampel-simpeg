<script defer>
    // submit AJAX (biar tidak pindah ke /store)
    $(document).on('submit', '#bt_submit_create_jadwal_karyawan', function (e) {
        e.preventDefault();

        const $form = $(this);

        const payload = {
            id_sdm: $form.find('[name="id_sdm"]').val(),
            id_jadwal: $form.find('[name="id_jadwal"]').val(),
            tanggal_mulai: $form.find('[name="tanggal_mulai"]').val(),
            tanggal_selesai: $form.find('[name="tanggal_selesai"]').val(),
        };

        Swal.fire({
            title: 'Simpan data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            allowOutsideClick: false
        }).then((r) => {
            if (!r.value) return;

            // pakai DataManager bila ada
            if (typeof DataManager !== 'undefined' && typeof DataManager.postData === 'function') {
                DataManager.openLoading();

                DataManager.postData("{{ route('admin.absensi.jadwal-karyawan.store') }}", payload)
                    .then(res => {
                        Swal.close();

                        if (res?.success) {
                            Swal.fire('Success', res.message || 'Berhasil disimpan', 'success');
                            $('#form_create').modal('hide');

                            // reload datatable (pastikan variabel table global)
                            if (window.jadwalKaryawanTable) {
                                window.jadwalKaryawanTable.ajax.reload(null, false);
                            }
                            return;
                        }

                        if (res?.errors) {
                            const first = Object.values(res.errors).flat()[0];
                            Swal.fire('Warning', first || 'Validasi gagal', 'warning');
                            return;
                        }

                        Swal.fire('Warning', res?.message || 'Gagal simpan', 'warning');
                    })
                    .catch(err => {
                        Swal.close();
                        if (typeof ErrorHandler !== 'undefined') return ErrorHandler.handleError(err);
                        console.error(err);
                        Swal.fire('Error', 'Terjadi error, cek console/log.', 'error');
                    });

                return;
            }

            // fallback ajax biasa
            $.ajax({
                url: "{{ route('admin.absensi.jadwal-karyawan.store') }}",
                method: "POST",
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content')
                },
                success: function (res) {
                    if (res?.success) {
                        Swal.fire('Success', res.message || 'Berhasil disimpan', 'success');
                        $('#form_create').modal('hide');
                        window.jadwalKaryawanTable?.ajax?.reload(null, false);
                    } else {
                        Swal.fire('Warning', res?.message || 'Gagal simpan', 'warning');
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    Swal.fire('Error', xhr.responseJSON?.message || xhr.statusText, 'error');
                }
            });
        });
    });
</script>
