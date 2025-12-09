<script>
    $('#bt_submit_create_periode').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.gaji.periode.store') }}", // sesuaikan
            method: "POST",
            data: form.serialize(),
            success: function () {
                $('#form_create_periode').modal('hide');
                form[0].reset();
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