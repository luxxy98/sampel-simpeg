<script>
    $('#bt_submit_create_absensi').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const btn = form.find('button[type="submit"]');

        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.absensi.store') }}", // sesuaikan route
            method: "POST",
            data: form.serialize(),
            success: function (res) {
                $('#form_create_absensi').modal('hide');
                form[0].reset();
                $('#table-absensi').DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                // tampilkan error validasi Laravel (422)
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    form.find('.invalid-feedback').text('').hide();
                    form.find('.is-invalid').removeClass('is-invalid');

                    $.each(errors, function (key, msgs) {
                        const input = form.find('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.closest('.d-flex, .mb-3').find('.invalid-feedback')
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