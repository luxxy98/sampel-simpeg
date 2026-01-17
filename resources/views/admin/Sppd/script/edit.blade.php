<script defer>
    $("#form_edit").on("show.bs.modal", function (e) {
        const button = $(e.relatedTarget);
        const id = button.data("id");

        $('#edit_id_sdm').select2({ dropdownParent: $('#form_edit'), width: '100%' });

        const detail = '{{ route('admin.sppd.show', [':id']) }}';
        DataManager.fetchData(detail.replace(':id', id))
            .then(function (response) {
                if (response.success) {
                    const d = response.data;
                    $("#edit_id").val(d.id_sppd);
                    $("#edit_id_sdm").val(d.id_sdm).trigger('change');
                    $("#edit_nomor_surat").val(d.nomor_surat ?? '');
                    $("#edit_tanggal_surat").val(d.tanggal_surat ?? '');
                    $("#edit_tanggal_berangkat").val(d.tanggal_berangkat ?? '');
                    $("#edit_tanggal_pulang").val(d.tanggal_pulang ?? '');
                    $("#edit_tujuan").val(d.tujuan ?? '');
                    $("#edit_instansi_tujuan").val(d.instansi_tujuan ?? '');
                    $("#edit_transportasi").val(d.transportasi ?? '');
                    $("#edit_maksud_tugas").val(d.maksud_tugas ?? '');

                    $("#edit_biaya_transport").val(d.biaya_transport ?? 0);
                    $("#edit_biaya_penginapan").val(d.biaya_penginapan ?? 0);
                    $("#edit_uang_harian").val(d.uang_harian ?? 0);
                    $("#edit_biaya_lainnya").val(d.biaya_lainnya ?? 0);
                } else {
                    Swal.fire('Warning', response.message, 'warning');
                }
            }).catch(error => ErrorHandler.handleError(error));

        $("#bt_submit_edit").off('submit').on("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: "Update data SPPD?",
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
                        id_sdm: $("#edit_id_sdm").val(),
                        nomor_surat: $("#edit_nomor_surat").val(),
                        tanggal_surat: $("#edit_tanggal_surat").val(),
                        tanggal_berangkat: $("#edit_tanggal_berangkat").val(),
                        tanggal_pulang: $("#edit_tanggal_pulang").val(),
                        tujuan: $("#edit_tujuan").val(),
                        instansi_tujuan: $("#edit_instansi_tujuan").val(),
                        maksud_tugas: $("#edit_maksud_tugas").val(),
                        transportasi: $("#edit_transportasi").val(),
                        biaya_transport: $("#edit_biaya_transport").val(),
                        biaya_penginapan: $("#edit_biaya_penginapan").val(),
                        uang_harian: $("#edit_uang_harian").val(),
                        biaya_lainnya: $("#edit_biaya_lainnya").val(),
                    };

                    const update = '{{ route('admin.sppd.update', [':id']) }}';
                    DataManager.putData(update.replace(':id', id), input).then(response => {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        }

                        if (!response.success && response.errors) {
                            const validationErrorFilter = new ValidationErrorFilter("edit_");
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
