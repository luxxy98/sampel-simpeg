<script defer>
    function fillEditAbsensi(payload) {
        // payload contoh:
        // { absensi: {...}, detail: [...] }

        $('#edit_id_absensi').val(payload.absensi.id_absensi);
        $('#edit_tanggal').val(payload.absensi.tanggal);
        $('#edit_id_sdm').val(payload.absensi.id_sdm).trigger('change');
        $('#edit_id_jadwal_karyawan').val(payload.absensi.id_jadwal_karyawan).trigger('change');

        $('#edit_total_jam_kerja').val(payload.absensi.total_jam_kerja);
        $('#edit_total_terlambat').val(payload.absensi.total_terlambat);
        $('#edit_total_pulang_awal').val(payload.absensi.total_pulang_awal);

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
                        <input type="hidden" name="detail[id_detail][]" value="${d.id_detail || ''}">
                    </td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_mulai" name="detail[waktu_mulai][]" value="${d.waktu_mulai || ''}"></td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_selesai" name="detail[waktu_selesai][]" value="${d.waktu_selesai || ''}"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm edit_detail_durasi" name="detail[durasi_jam][]" value="${d.durasi_jam || '0.00'}"></td>
                    <td><input type="text" class="form-control form-control-sm" name="detail[lokasi_pulang][]" value="${d.lokasi_pulang || ''}"></td>
                    <td><button type="button" class="btn btn-sm btn-light-danger btn_remove_row">Hapus</button></td>
                </tr>
            `);
        });

        // init select2 + set selected value
        $tbody.find('[data-control="select2"]').select2({ dropdownParent: $('#form_edit') });
        (payload.detail || []).forEach((d, i) => {
            $tbody.find('tr').eq(i).find('select.edit_detail_jenis').val(d.id_jenis_absen).trigger('change');
        });

        $tbody.find('.edit_detail_mulai, .edit_detail_selesai').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        $tbody.off('click', '.btn_remove_row').on('click', '.btn_remove_row', function () {
            $(this).closest('tr').remove();
            $tbody.find('tr').each((i, tr) => $(tr).find('td:first').text(i+1));
        });

        $tbody.off('change', '.edit_detail_mulai, .edit_detail_selesai').on('change', '.edit_detail_mulai, .edit_detail_selesai', function () {
            const $row = $(this).closest('tr');
            const mulai = $row.find('.edit_detail_mulai').val();
            const selesai = $row.find('.edit_detail_selesai').val();
            if (!mulai || !selesai) return;
            const start = new Date(mulai.replace(' ', 'T'));
            const end = new Date(selesai.replace(' ', 'T'));
            const diffH = (end.getTime() - start.getTime()) / 3600000;
            if (diffH >= 0) $row.find('.edit_detail_durasi').val(diffH.toFixed(2));
        });
    }

    $('#form_edit').on('show.bs.modal', function () {
        $('#edit_tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#edit_id_sdm, #edit_id_jadwal_karyawan').select2({ dropdownParent: $('#form_edit') });

        $('#btn_add_detail_row_edit').off('click').on('click', function () {
            const $tbody = $('#table_detail_edit tbody');
            const idx = $tbody.find('tr').length + 1;

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
                        <input type="hidden" name="detail[id_detail][]" value="">
                    </td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_mulai" name="detail[waktu_mulai][]" value=""></td>
                    <td><input type="text" class="form-control form-control-sm edit_detail_selesai" name="detail[waktu_selesai][]" value=""></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm edit_detail_durasi" name="detail[durasi_jam][]" value="0.00"></td>
                    <td><input type="text" class="form-control form-control-sm" name="detail[lokasi_pulang][]" value=""></td>
                    <td><button type="button" class="btn btn-sm btn-light-danger btn_remove_row">Hapus</button></td>
                </tr>
            `);

            $tbody.find('tr:last [data-control="select2"]').select2({ dropdownParent: $('#form_edit') });
            $tbody.find('tr:last .edit_detail_mulai, tr:last .edit_detail_selesai').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });
        });

        $('#bt_submit_edit').off('submit').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kamu yakin?',
                text: 'Data absensi akan diupdate.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal',
                allowOutsideClick: false,
            }).then((result) => {
                if (!result.value) return;

                DataManager.openLoading();

                const id = $('#edit_id_absensi').val();
                const action = '{{ route('admin.absensi.update', ['id' => '___ID___']) }}'.replace('___ID___', id);

                const form = document.getElementById('bt_submit_edit');
                const formData = new FormData(form);

                DataManager.formData(action, formData).then(response => {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        setTimeout(() => location.reload(), 900);
                        return;
                    }

                    if (!response.success && response.errors) {
                        const v = new ValidationErrorFilter();
                        v.filterValidationErrors(response);
                        Swal.fire('Warning', 'Validasi bermasalah', 'warning');
                        return;
                    }

                    Swal.fire('Peringatan', response.message || 'Gagal update', 'warning');
                }).catch(err => ErrorHandler.handleError(err));
            });
        });

    }).on('hidden.bs.modal', function () {
        const $m = $(this);
        $m.find('form').trigger('reset');
        $m.find('select').val('').trigger('change');
        $m.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $m.find('.invalid-feedback, .valid-feedback, .text-danger').remove();
        $('#table_detail_edit tbody').html('');
    });
</script>
