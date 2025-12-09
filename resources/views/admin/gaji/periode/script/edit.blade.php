<script>
    function openEditPeriode(id) {
        $.get("{{ url('admin/gaji/periode') }}/" + id, function (res) {
            $('#edit_id_periode').val(res.id_periode);
            $('#edit_tahun').val(res.tahun);
            $('#edit_bulan').val(res.bulan);
            $('#edit_tanggal_mulai').val(res.tanggal_mulai);
            $('#edit_tanggal_selesai').val(res.tanggal_selesai);
            $('#edit_status').val(res.status);
            $('#edit_status_peninjauan').val(res.status_peninjauan);
            $('#edit_tanggal_penggajian').val(res.tanggal_penggajian);

            $('#form_edit_periode').modal('show');
        });
    }

    $('#bt_submit_edit_periode').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const id = $('#edit_id_periode').val();
        const btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/gaji/periode') }}/" + id,
            method: "POST", // pakai POST + _method=PUT
            data: form.serialize(),
            success: function () {
                $('#form_edit_periode').modal('hide');
                $('#table-periode-gaji').DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    form.find('.invalid-feedback').text('').hide();
                    form.find('.is-invalid').removeClass('is-invalid');

                    $.each(errors, function (key, msgs) {
                        const input = form.find('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.closest('.mb-3').find('.invalid-feedback')
                            .text(msgs[0]).show();
                    });
                }
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
</script>