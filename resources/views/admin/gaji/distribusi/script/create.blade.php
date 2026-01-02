<script defer>
    // Data transaksi gaji untuk auto-fill
    const trxData = @json($trxOptions ?? []);

    function formatRupiahInput(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    $('#modal_create').on('show.bs.modal', function () {
        $('#id_periode, #id_gaji, #id_sdm, #id_rekening, #status_transfer').select2({ dropdownParent: $('#modal_create') });

        $('#tanggal_transfer').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        // Auto-fill saat memilih transaksi gaji
        $('#id_gaji').off('change').on('change', function() {
            const idGaji = $(this).val();
            if (!idGaji) {
                $('#jumlah_transfer').val('0');
                $('#info_gaji').html('');
                $('#id_rekening').val('').trigger('change');
                return;
            }

            const trx = trxData.find(t => t.id_gaji == idGaji);
            if (trx) {
                // Auto-fill jumlah transfer dengan take home pay
                $('#jumlah_transfer').val(trx.total_take_home_pay || 0);

                // Auto-select periode dan SDM
                $('#id_periode').val(trx.id_periode).trigger('change');
                $('#id_sdm').val(trx.id_sdm).trigger('change');

                // Auto-select rekening utama SDM
                if (trx.id_rekening) {
                    $('#id_rekening').val(trx.id_rekening).trigger('change');
                }

                // Tampilkan info gaji dengan rekening
                let rekeningInfo = trx.rekening_label ? `<br><small class="text-muted">Rekening: ${trx.rekening_label}</small>` : '<br><small class="text-danger">Belum ada rekening utama</small>';
                
                $('#info_gaji').html(`
                    <div class="alert alert-info py-2 px-3 mt-2">
                        <strong>${trx.nama_sdm || 'SDM'}</strong> - ${trx.jabatan || 'Belum ada jabatan'}${rekeningInfo}
                        <br><span class="fs-5 fw-bold text-success">Take Home Pay: Rp ${formatRupiahInput(trx.total_take_home_pay)}</span>
                    </div>
                `);
            }
        });

        $('#form_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((r) => {
                if (!r.value) return;

                DataManager.openLoading();

                const input = {
                    id_periode: $('#id_periode').val(),
                    id_gaji: $('#id_gaji').val(),
                    id_sdm: $('#id_sdm').val(),
                    id_rekening: $('#id_rekening').val(),
                    jumlah_transfer: $('#jumlah_transfer').val(),
                    status_transfer: $('#status_transfer').val(),
                    tanggal_transfer: $('#tanggal_transfer').val(),
                    catatan: $('#catatan').val(),
                };

                DataManager.postData('{{ route('admin.gaji.distribusi.store') }}', input)
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success');
                            setTimeout(() => location.reload(), 700);
                            return;
                        }

                        if (!res.success && res.errors) {
                            const v = new ValidationErrorFilter();
                            v.filterValidationErrors(res);
                            Swal.fire('Warning', 'Validasi bermasalah', 'warning');
                            return;
                        }

                        Swal.fire('Peringatan', res.message || 'Gagal simpan', 'warning');
                    })
                    .catch(err => ErrorHandler.handleError(err));
            });
        });
    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select').val('').trigger('change');
        $m.find('.invalid-feedback, .text-danger').remove();
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('#jumlah_transfer').val('0');
        $('#info_gaji').html('');
    });
</script>

