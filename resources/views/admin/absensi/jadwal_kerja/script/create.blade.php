<script defer>
    function normalizeTimeToSeconds(value) {
        if (!value) return value;
        return value.length === 5 ? (value + ':00') : value; // HH:MM -> HH:MM:SS
    }

    // delegated binding: tidak peduli modal dibuka kapan, tetap nempel
    $(document).on('submit', '#bt_submit_create_jadwal', function (e) {
        e.preventDefault();

        const $form = $(this);

        // AMBIL DARI name="" (lebih aman daripada id)
        const payload = {
            nama_jadwal: $form.find('[name="nama_jadwal"]').val(),
            jam_masuk: normalizeTimeToSeconds($form.find('[name="jam_masuk"]').val()),
            jam_pulang: normalizeTimeToSeconds($form.find('[name="jam_pulang"]').val()),
            keterangan: $form.find('[name="keterangan"]').val(),
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

            // pakai DataManager kalau ada (sesuai template project kamu)
            if (typeof DataManager !== 'undefined' && typeof DataManager.postData === 'function') {
                DataManager.openLoading();
                DataManager.postData('{{ route('admin.absensi.jadwal-kerja.store') }}', payload)
                    .then(res => {
                        Swal.close();

                        if (res?.success) {
                            Swal.fire('Success', res.message || 'Berhasil disimpan', 'success');
                            $('#form_create').modal('hide');
                            window.tableJadwalKerja?.ajax?.reload(null, false);
                            return;
                        }

                        // tampilkan pesan validasi yang benar
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

            // fallback AJAX biasa
            $.ajax({
                url: '{{ route('admin.absensi.jadwal-kerja.store') }}',
                method: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content')
                },
                success: function (res) {
                    if (res?.success) {
                        Swal.fire('Success', res.message || 'Berhasil disimpan', 'success');
                        $('#form_create').modal('hide');
                        window.tableJadwalKerja?.ajax?.reload(null, false);
                    } else {
                        Swal.fire('Warning', res?.message || 'Gagal simpan', 'warning');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || xhr.statusText;
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
