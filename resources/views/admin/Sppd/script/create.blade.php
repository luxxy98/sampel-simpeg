<script defer>
    $('#form_create').on('show.bs.modal', function () {
        $('#id_sdm').select2({ dropdownParent: $('#form_create'), width: '100%' });

        $('#bt_submit_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: 'Simpan data SPPD?',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                cancelButtonColor: '#dd3333',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                focusCancel: true
            }).then((result) => {
                if (result.value) {
                    DataManager.openLoading();

                    const input = {
                        id_sdm: $('#id_sdm').val(),
                        nomor_surat: $('#nomor_surat').val(),
                        tanggal_surat: $('#tanggal_surat').val(),
                        tanggal_berangkat: $('#tanggal_berangkat').val(),
                        tanggal_pulang: $('#tanggal_pulang').val(),
                        tujuan: $('#tujuan').val(),
                        instansi_tujuan: $('#instansi_tujuan').val(),
                        maksud_tugas: $('#maksud_tugas').val(),
                        transportasi: $('#transportasi').val(),
                        biaya_transport: $('#biaya_transport').val(),
                        biaya_penginapan: $('#biaya_penginapan').val(),
                        uang_harian: $('#uang_harian').val(),
                        biaya_lainnya: $('#biaya_lainnya').val(),
                    };

                    const action = '{{ route('admin.sppd.store') }}';
                    DataManager.postData(action, input).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }

                        if (!response.success && response.errors) {
                            const validationErrorFilter = new ValidationErrorFilter();
                            validationErrorFilter.filterValidationErrors(response);
                            Swal.fire('Warning', 'validasi bermasalah', 'warning');
                        }

                        if (!response.success && !response.errors) {
                            Swal.fire('Peringatan', response.message, 'warning');
                        }
                    }).catch(error => ErrorHandler.handleError(error));
                }
            });
        });
    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select, textarea').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
    });
</script>
