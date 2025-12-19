<script>
    // tombol aksi edit di kolom "Aksi" panggil fungsi ini
    function openEditAbsensi(id) {
        $.get("{{ url('admin/absensi') }}/" + id, function(res) {
            // res = data absensi (JSON)
            $('#edit_id_absensi').val(res.id_absensi);
            $('#edit_tanggal').val(res.tanggal);
            $('#edit_total_jam_kerja').val(res.total_jam_kerja);
            $('#edit_total_terlambat').val(res.total_terlambat);
            $('#edit_total_pulang_awal').val(res.total_pulang_awal);
            $('#edit_id_sdm').val(res.id_sdm);
            $('#edit_id_jadwal_karyawan').val(res.id_jadwal_karyawan);


            // opsional: isi select SDM & Jadwal
            // (bisa preloaded di Blade atau diambil dari res)

            $('#form_edit_absensi').modal('show');
        });
    }

    $('#bt_submit_edit_absensi').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const id = $('#edit_id_absensi').val();
        const btn = form.find('button[type="submit"]');

        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/absensi') }}/" + id,
            method: "POST",
            data: form.serialize(), // sudah ada _method=PUT
            success: function(res) {
                $('#form_edit_absensi').modal('hide');
                $('#table-absensi').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    form.find('.invalid-feedback').text('').hide();
                    form.find('.is-invalid').removeClass('is-invalid');

                    $.each(errors, function(key, msgs) {
                        const input = form.find('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.closest('.d-flex, .mb-3').find('.invalid-feedback')
                            .text(msgs[0]).show();
                    });
                }
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });
</script>
