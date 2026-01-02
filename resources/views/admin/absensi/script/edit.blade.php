<script defer>
    async function openEditAbsensi(id_absensi) {
        try {
            DataManager.openLoading();
            const url = '{{ route('admin.absensi.show', ['id' => '___ID___']) }}'.replace('___ID___', id_absensi);
            const res = await DataManager.readData(url);
            Swal.close();

            if (!res.success) {
                Swal.fire('Peringatan', res.message || 'Gagal memuat data', 'warning');
                return;
            }

            fillEditAbsensi(res.data);
            $('#form_edit').modal('show');
        } catch (err) {
            ErrorHandler.handleError(err);
        }
    }

    function fillEditAbsensi(payload) {
        const abs = payload.absensi;
        $('#edit_id_absensi').val(abs.id_absensi);
        $('#edit_tanggal').val(abs.tanggal);
        $('#edit_id_sdm').val(abs.id_sdm).trigger('change');
        $('#edit_id_jadwal_karyawan').val(abs.id_jadwal_karyawan).trigger('change');

        $('#edit_total_jam_kerja').val(abs.total_jam_kerja);
        $('#edit_total_terlambat').val(abs.total_terlambat);
        $('#edit_total_pulang_awal').val(abs.total_pulang_awal);

        const $tbody = $('#table_detail_edit tbody');
        $tbody.html('');

        const jenisOptions = `@isset($jenisAbsenOptions)
            @foreach($jenisAbsenOptions as $j)
                <option value="{{ $j['id_jenis_absen'] }}">{{ $j['nama_absen'] ?? ('Jenis #' . $j['id_jenis_absen']) }}</option>
            @endforeach
        @endisset`;

        (payload.detail || []).forEach((d, i) => {
            $tbody.append(`
                <tr>
                    <td class="text-muted">${i+1}</td>
                    <td>
                        <select class="form-select form-select-sm edit_detail_jenis" name="detail[id_jenis_absen][]" data-control="select2">
                            <option value="">-- pilih --</option>
                            ${jenisOptions}
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_mulai" name="detail[waktu_mulai][]" value="${d.waktu_mulai || ''}"></td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_selesai" name="detail[waktu_selesai][]" value="${d.waktu_selesai || ''}"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm edit_detail_durasi" name="detail[durasi_jam][]" value="${d.durasi_jam || '0.00'}"></td>
                    <td><input type="text" class="form-control form-control-sm" name="detail[lokasi_pulang][]" value="${d.lokasi_pulang || ''}"></td>
                    <td><button type="button" class="btn btn-sm btn-light-danger btn_remove_row">Hapus</button></td>
                </tr>
            `);
        });

        $tbody.find('[data-control="select2"]').select2({ dropdownParent: $('#form_edit') });
        (payload.detail || []).forEach((d, i) => {
            $tbody.find('tr').eq(i).find('select.edit_detail_jenis').val(d.id_jenis_absen).trigger('change');
        });

        $tbody.find('.edit_detail_mulai, .edit_detail_selesai').flatpickr({ enableTime: true, enableSeconds: true, dateFormat: 'Y-m-d H:i:S' });

        $tbody.off('click', '.btn_remove_row').on('click', '.btn_remove_row', function () {
            $(this).closest('tr').remove();
            reindexTable($tbody); // Reuse function from create if available, or define it here
        });

        // Re-bind change for duration calculation if needed
         $tbody.off('change', '.edit_detail_mulai, .edit_detail_selesai').on('change', '.edit_detail_mulai, .edit_detail_selesai', function () {
            const $row = $(this).closest('tr');
            computeDuration($row, '.edit_detail_durasi', '.edit_detail_mulai', '.edit_detail_selesai');
        });
    }

    $('#bt_submit_edit_absensi').off('submit').on('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Kamu yakin?',
            text: 'Perubahan akan disimpan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
        }).then((result) => {
            if (!result.value) return;

            DataManager.openLoading();

            const id = $('#edit_id_absensi').val();
            const action = '{{ route('admin.absensi.update', ['id' => '___ID___']) }}'.replace('___ID___', id);
            const form = document.getElementById('bt_submit_edit_absensi');
            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            DataManager.formData(action, formData).then(response => {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success');
                    $('#form_edit').modal('hide');
                    if (tableAbsensi) tableAbsensi.ajax.reload(null, false);
                } else if (response.errors) {
                    const v = new ValidationErrorFilter();
                    v.filterValidationErrors(response);
                    Swal.fire('Warning', 'Validasi bermasalah', 'warning');
                } else {
                    Swal.fire('Peringatan', response.message || 'Gagal menyimpan', 'warning');
                }
            }).catch(err => ErrorHandler.handleError(err));
        });
    });

    $('#form_edit').on('shown.bs.modal', function () {
        $('#edit_tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#edit_id_sdm, #edit_id_jadwal_karyawan').select2({ dropdownParent: $('#form_edit') });

       // Add row functionality for edit
       $('#btn_add_detail_row_edit').off('click').on('click', function () {
            const $tbody = $('#table_detail_edit tbody');
             const idx = $tbody.find('tr').length + 1;
             // Reuse buildDetailRow if possible or inline
             // simplified inline for edit context to avoid mess
             const jenisOptions = `@isset($jenisAbsenOptions)
                @foreach($jenisAbsenOptions as $j)
                    <option value="{{ $j['id_jenis_absen'] }}">{{ $j['nama_absen'] ?? ('Jenis #' . $j['id_jenis_absen']) }}</option>
                @endforeach
            @endisset`;

            $tbody.append(`
                <tr>
                    <td class="text-muted">${idx}</td>
                    <td>
                        <select class="form-select form-select-sm edit_detail_jenis" name="detail[id_jenis_absen][]" data-control="select2">
                            <option value="">-- pilih --</option>
                            ${jenisOptions}
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_mulai" name="detail[waktu_mulai][]" placeholder="YYYY-MM-DD HH:mm:ss"></td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_selesai" name="detail[waktu_selesai][]" placeholder="YYYY-MM-DD HH:mm:ss"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm edit_detail_durasi" name="detail[durasi_jam][]" value="0.00"></td>
                    <td><input type="text" class="form-control form-control-sm" name="detail[lokasi_pulang][]" placeholder="Lokasi pulang"></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-light-danger btn_remove_row">Hapus</button>
                    </td>
                </tr>
            `);
             $tbody.find('tr:last [data-control="select2"]').select2({ dropdownParent: $('#form_edit') });
             $tbody.find('tr:last .edit_detail_mulai, tr:last .edit_detail_selesai').flatpickr({ enableTime: true, enableSeconds: true, dateFormat: 'Y-m-d H:i:S' });
        });
    });
</script>
