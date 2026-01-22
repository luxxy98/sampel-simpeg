<script defer>
    function formatRupiahInput(num) {
        if (!num) return '0';
        return parseFloat(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    $('#modal_create').on('show.bs.modal', function () {
        // Initialize Select2 with proper settings
        $('#id_periode').select2({ 
            dropdownParent: $('#modal_create'),
            placeholder: 'Pilih Periode Gaji',
            allowClear: false
        });
        $('#id_gaji').select2({ 
            dropdownParent: $('#modal_create'),
            placeholder: 'Pilih periode dulu...'
        });
        $('#id_rekening, #status_transfer').select2({ dropdownParent: $('#modal_create') });
        $('#tanggal_transfer').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        // Cascading: Saat pilih Periode, load Karyawan yang sesuai
        $('#id_periode').off('change').on('change', function() {
            const idPeriode = $(this).val();
            const $gajiSelect = $('#id_gaji');
            
            // Reset
            $gajiSelect.empty().append('<option value="">Memuat data...</option>');
            $('#info_gaji').html('');
            $('#id_sdm, #id_rekening').val('');
            $('#jumlah_transfer').val('0');
            
            if (!idPeriode) {
                $gajiSelect.empty().append('<option value="">Pilih periode dulu...</option>');
                return;
            }
            
            // Load karyawan berdasarkan periode via AJAX
            $.ajax({
                url: `{{ url('admin/gaji/distribusi/trx-by-periode') }}/${idPeriode}`,
                method: 'GET',
                success: function(res) {
                    $gajiSelect.empty().append('<option value="">Pilih karyawan...</option>');
                    
                    if (res.success && res.data && res.data.length > 0) {
                        res.data.forEach(function(trx) {
                            $gajiSelect.append(`<option value="${trx.id_gaji}" 
                                data-thp="${trx.total_take_home_pay || 0}"
                                data-sdm="${trx.id_sdm}"
                                data-nama="${trx.nama_sdm || ''}"
                                data-jabatan="${trx.jabatan || 'Belum ada jabatan'}"
                                data-rekening="${trx.id_rekening || ''}"
                                data-rekening-label="${trx.rekening_label || ''}"
                            >${trx.label}</option>`);
                        });
                    } else {
                        $gajiSelect.empty().append('<option value="">Tidak ada data gaji di periode ini</option>');
                        $('#info_gaji').html('<div class="alert alert-warning py-2">Tidak ada karyawan dengan data gaji di periode ini.</div>');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    $gajiSelect.empty().append('<option value="">Error memuat data</option>');
                    $('#info_gaji').html('<div class="alert alert-danger py-2">Gagal memuat data karyawan. Silakan coba lagi.</div>');
                }
            });
        });

        // Saat pilih karyawan, auto-fill data
        $('#id_gaji').off('change').on('change', function() {
            const $selected = $(this).find(':selected');
            const idGaji = $(this).val();
            
            if (!idGaji) {
                $('#info_gaji').html('');
                $('#id_sdm, #id_rekening').val('');
                $('#jumlah_transfer').val('0');
                return;
            }

            const thp = $selected.data('thp') || 0;
            const idSdm = $selected.data('sdm');
            const nama = $selected.data('nama');
            const jabatan = $selected.data('jabatan');
            const idRekening = $selected.data('rekening');
            const rekeningLabel = $selected.data('rekening-label');

            // Auto-fill
            $('#id_sdm').val(idSdm);
            $('#jumlah_transfer').val(thp);
            if (idRekening) {
                $('#id_rekening').val(idRekening).trigger('change');
            }

            // Info panel
            let rekeningInfo = rekeningLabel 
                ? `<br><small class="text-muted">Rekening: ${rekeningLabel}</small>` 
                : '<br><small class="text-danger">Belum ada rekening utama</small>';
            
            $('#info_gaji').html(`
                <div class="alert alert-info py-2 px-3">
                    <strong>${nama}</strong> - ${jabatan}${rekeningInfo}
                    <br><span class="fs-5 fw-bold text-success">Take Home Pay: Rp ${formatRupiahInput(thp)}</span>
                </div>
            `);
        });

        // Form submit
        $('#form_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            if (!$('#id_gaji').val()) {
                Swal.fire('Peringatan', 'Pilih karyawan terlebih dahulu', 'warning');
                return;
            }

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
                    id_rekening: $('#id_rekening').val() || null,
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
        $('#id_gaji').empty().append('<option value="">Pilih periode dulu...</option>');
    });
</script>
