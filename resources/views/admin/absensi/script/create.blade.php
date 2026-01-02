<script defer>
    function buildDetailRow(idx, prefix = '') {
        const jenisOptions = `@isset($jenisAbsenOptions)
            @foreach($jenisAbsenOptions as $j)
                <option value="{{ $j['id_jenis_absen'] }}">{{ $j['nama_absen'] ?? ('Jenis #' . $j['id_jenis_absen']) }}</option>
            @endforeach
        @endisset`;

        return `
            <tr>
                <td class="text-muted">${idx}</td>
                <td>
                    <select class="form-select form-select-sm ${prefix}detail_jenis" name="${prefix}detail[id_jenis_absen][]" data-control="select2">
                        <option value="">-- pilih --</option>
                        ${jenisOptions}
                    </select>
                </td>
                <td><input type="text" class="form-control form-control-sm ${prefix}detail_mulai" name="${prefix}detail[waktu_mulai][]" placeholder="YYYY-MM-DD HH:mm:ss"></td>
                <td><input type="text" class="form-control form-control-sm ${prefix}detail_selesai" name="${prefix}detail[waktu_selesai][]" placeholder="YYYY-MM-DD HH:mm:ss"></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm ${prefix}detail_durasi" name="${prefix}detail[durasi_jam][]" value="0.00"></td>
                <td><input type="text" class="form-control form-control-sm" name="${prefix}detail[lokasi_pulang][]" placeholder="Lokasi pulang"></td>
                <td>
                    <button type="button" class="btn btn-sm btn-light-danger btn_remove_row">Hapus</button>
                </td>
            </tr>
        `;
    }

    function reindexTable($tbody) {
        $tbody.find('tr').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function computeDuration($row, durasiSelector, mulaiSelector, selesaiSelector) {
        const mulai = $row.find(mulaiSelector).val();
        const selesai = $row.find(selesaiSelector).val();
        if (!mulai || !selesai) return;

        const start = new Date(mulai.replace(' ', 'T'));
        const end = new Date(selesai.replace(' ', 'T'));
        if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

        const diffH = (end.getTime() - start.getTime()) / 3600000;
        if (diffH >= 0) $row.find(durasiSelector).val(diffH.toFixed(2));
    }

    $('#form_create').on('show.bs.modal', function () {
        $('#tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });

        // init select2
        $('#id_sdm, #id_jadwal_karyawan').select2({ dropdownParent: $('#form_create') });

        // default 1 row
        const $tbody = $('#table_detail_create tbody');
        $tbody.html(buildDetailRow(1));
        $tbody.find('[data-control="select2"]').select2({ dropdownParent: $('#form_create') });
        $tbody.find('.detail_mulai, .detail_selesai').flatpickr({ enableTime: true, enableSeconds: true, dateFormat: 'Y-m-d H:i:S' });

        $('#btn_add_detail_row').off('click').on('click', function () {
            const idx = $tbody.find('tr').length + 1;
            $tbody.append(buildDetailRow(idx));
            $tbody.find('tr:last [data-control="select2"]').select2({ dropdownParent: $('#form_create') });
            $tbody.find('tr:last .detail_mulai, tr:last .detail_selesai').flatpickr({ enableTime: true, enableSeconds: true, dateFormat: 'Y-m-d H:i:S' });
        });

        $tbody.off('click', '.btn_remove_row').on('click', '.btn_remove_row', function () {
            $(this).closest('tr').remove();
            reindexTable($tbody);
        });

        $tbody.off('change', '.detail_mulai, .detail_selesai').on('change', '.detail_mulai, .detail_selesai', function () {
            const $row = $(this).closest('tr');
            computeDuration($row, '.detail_durasi', '.detail_mulai', '.detail_selesai');
        });

        $('#bt_submit_create').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: 'Data absensi akan disimpan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                allowOutsideClick: false,
            }).then((result) => {
                if (!result.value) return;

                DataManager.openLoading();

                const action = '{{ route('admin.absensi.store') }}';

                // serialize form (biar gampang kamu proses di backend)
                const form = document.getElementById('bt_submit_create');
                const formData = new FormData(form);

                DataManager.formData(action, formData).then(response => {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        setTimeout(() => location.reload(), 900);
                        return;
                    }

                    if (!response.success && response.errors) {
                        console.log('Validation Errors:', response.errors); // Debug: show errors in console
                        const v = new ValidationErrorFilter();
                        v.filterValidationErrors(response);
                        
                        // Show first error in Swal for better UX
                        const firstError = Object.values(response.errors).flat()[0];
                        Swal.fire('Warning', firstError || 'Validasi bermasalah', 'warning');
                        return;
                    }

                    Swal.fire('Peringatan', response.message || 'Gagal menyimpan', 'warning');
                }).catch(err => ErrorHandler.handleError(err));
            });
        });

    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
        $('#table_detail_create tbody').html('');
    });
</script>
