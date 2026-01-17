<script defer>
    $("#form_approve").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");

        $('#approve_status').select2({ dropdownParent: $('#form_approve'), width: '100%' });

        const detail = '{{ route('admin.cuti.pengajuan.show', [':id']) }}';
        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    $("#approve_id").val(response.data.id_cuti);
                    $("#approve_nama").text(response.data.nama ?? '-');
                    $("#approve_jenis").text(response.data.nama_jenis ?? '-');
                    $("#approve_mulai").text(response.data.tanggal_mulai ?? '-');
                    $("#approve_selesai").text(response.data.tanggal_selesai ?? '-');
                    $("#approve_hari").text(response.data.jumlah_hari ?? '-');

                    $("#approve_status").val('').trigger('change');
                    $("#approve_catatan").val('');
                } else {
                    Swal.fire('Warning', response.message, 'warning');
                }
            }).catch(error => ErrorHandler.handleError(error));

        $("#bt_submit_approve").off('submit').on("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: "Simpan keputusan untuk pengajuan ini?",
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                focusCancel: true,
            }).then((result) => {
                if (result.value) {
                    DataManager.openLoading();

                    const input = {
                        status: $("#approve_status").val(),
                        catatan: $("#approve_catatan").val(),
                    };

                    const approve = '{{ route('admin.cuti.pengajuan.approve', [':id']) }}';
                    DataManager.putData(approve.replace(':id', id), input).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }

                        if (!response.success && response.errors) {
                            const validationErrorFilter = new ValidationErrorFilter("approve_");
                            validationErrorFilter.filterValidationErrors(response);
                            Swal.fire('Peringatan', 'Isian Anda belum lengkap atau tidak valid.', 'warning');
                        }

                        if (!response.success && !response.errors) {
                            Swal.fire('Warning', response.message, 'warning');
                        }
                    }).catch(error => ErrorHandler.handleError(error));
                }
            })
        });
    }).on("hidden.bs.modal", function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select, textarea').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
    });
</script>
