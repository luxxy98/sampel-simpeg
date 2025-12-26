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
                <option value="{{ $j['id_jenis_absen'] }}">{{ $j['nama_absen'] }}</option>
            @endforeach
        @endisset`;

        (payload.detail || []).forEach((d, i) => {
            $tbody.append(`
                <tr>
                    <td>${i+1}</td>
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

        $tbody.find('.edit_detail_mulai, .edit_detail_selesai').flatpickr({ enableTime: true, dateFormat: 'Y-m-d H:i:S' });

        $tbody.off('click', '.btn_remove_row').on('click', '.btn_remove_row', function () {
            $(this).closest('tr').remove();
        });
    }
</script>
