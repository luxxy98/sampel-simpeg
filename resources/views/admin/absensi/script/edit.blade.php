<script defer>
    // Data jadwal dengan jam_masuk dan jam_pulang (shared with create)
    const jadwalDataEdit = @json($jadwalOptions ?? []);
    
    // Data tarif lembur
    const tarifLemburDataEdit = @json($tarifLemburOptions ?? []);
    
    // Data hari libur
    const holidayDatesEdit = @json($holidayDates ?? []);
    
    // ID jenis absen HADIR (untuk filter perhitungan)
    const HADIR_IDS_EDIT = [
        @foreach($jenisAbsenOptions ?? [] as $j)
            @if(strtoupper($j['nama_absen'] ?? '') === 'HADIR')
                {{ $j['id_jenis_absen'] }},
            @endif
        @endforeach
    ];

    // URL untuk check holiday via AJAX
    const CHECK_HOLIDAY_URL_EDIT = '{{ route("admin.absensi.check-holiday") }}';

    function getJadwalByIdEdit(id) {
        return jadwalDataEdit.find(j => j.id_jadwal_karyawan == id);
    }

    function parseTimeToMinutesEdit(timeStr) {
        if (!timeStr) return 0;
        const parts = timeStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function extractTimeFromDatetimeEdit(datetimeStr) {
        if (!datetimeStr) return null;
        const match = datetimeStr.match(/(\d{2}):(\d{2})/);
        if (match) {
            return parseInt(match[1]) * 60 + parseInt(match[2]);
        }
        return null;
    }
    
    function formatRupiahEdit(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function checkHolidayEdit(tanggal) {
        if (!tanggal) {
            updateHolidayUIEdit(false, null, null);
            return;
        }
        
        $.get(CHECK_HOLIDAY_URL_EDIT, { tanggal: tanggal }, function(response) {
            if (response.success && response.data) {
                updateHolidayUIEdit(
                    response.data.is_hari_libur,
                    response.data.holiday_name,
                    response.data
                );
            }
        }).fail(function() {
            updateHolidayUIEdit(false, null, {
                id_tarif_lembur: tarifLemburDataEdit[0]?.id_tarif || null,
                nama_tarif: tarifLemburDataEdit[0]?.nama_tarif || '-',
                tarif_per_jam: tarifLemburDataEdit[0]?.tarif_per_jam || 0
            });
        });
    }
    
    function updateHolidayUIEdit(isHoliday, holidayName, tarifData) {
        // Update holiday badge
        if (isHoliday) {
            $('#edit_holiday_badge').show();
            $('#edit_holiday_name').text(holidayName || 'Hari Libur');
        } else {
            $('#edit_holiday_badge').hide();
        }
        
        // Update hidden fields
        $('#edit_is_hari_libur').val(isHoliday ? 1 : 0);
        
        if (tarifData) {
            $('#edit_id_tarif_lembur').val(tarifData.id_tarif_lembur || '');
            $('#edit_tarif_lembur_info').val(tarifData.nama_tarif || '-');
            $('#edit_tarif_per_jam').val(tarifData.tarif_per_jam || 0);
            $('#edit_tarif_per_jam_display').val(formatRupiahEdit(tarifData.tarif_per_jam || 0));
        }
        
        // Recalculate nominal lembur
        calculateNominalLemburEdit();
    }
    
    function calculateNominalLemburEdit() {
        const totalLembur = parseFloat($('#edit_total_lembur').val()) || 0;
        const tarifPerJam = parseFloat($('#edit_tarif_per_jam').val()) || 0;
        const nominal = totalLembur * tarifPerJam;
        
        $('#edit_nominal_lembur').val(nominal);
        $('#edit_nominal_lembur_display').val(formatRupiahEdit(nominal));
    }

    function recalculateTotalsEdit() {
        const jadwalId = $('#edit_id_jadwal_karyawan').val();
        const jadwal = getJadwalByIdEdit(jadwalId);
        
        if (!jadwal) {
            $('#edit_total_jam_kerja').val('0.00');
            $('#edit_total_terlambat').val('0.00');
            $('#edit_total_pulang_awal').val('0.00');
            $('#edit_total_lembur').val('0.00');
            calculateNominalLemburEdit();
            return;
        }

        const jamMasukMenit = parseTimeToMinutesEdit(jadwal.jam_masuk);
        const jamPulangMenit = parseTimeToMinutesEdit(jadwal.jam_pulang);

        let totalJamKerja = 0;
        let totalTerlambat = 0;
        let totalPulangAwal = 0;
        let totalLembur = 0;

        $('#table_detail_edit tbody tr').each(function() {
            const $row = $(this);
            const jenisAbsen = $row.find('.edit_detail_jenis').val();
            const durasi = parseFloat($row.find('.edit_detail_durasi').val()) || 0;
            const waktuMulai = $row.find('.edit_detail_mulai').val();
            const waktuSelesai = $row.find('.edit_detail_selesai').val();

            // Hitung hanya untuk jenis HADIR
            if (HADIR_IDS_EDIT.includes(parseInt(jenisAbsen))) {
                totalJamKerja += durasi;

                // Hitung keterlambatan (jam datang > jam masuk jadwal)
                const jamDatangMenit = extractTimeFromDatetimeEdit(waktuMulai);
                if (jamDatangMenit !== null && jamDatangMenit > jamMasukMenit) {
                    totalTerlambat += (jamDatangMenit - jamMasukMenit) / 60;
                }

                // Hitung pulang awal dan lembur
                let jamPulangAktualMenit = extractTimeFromDatetimeEdit(waktuSelesai);
                
                // Handle waktu setelah tengah malam (misal 01:00 = 25 jam dalam konteks shift malam)
                if (jamPulangAktualMenit !== null && jamPulangAktualMenit < 7 * 60) {
                    jamPulangAktualMenit += 24 * 60;
                }
                
                if (jamPulangAktualMenit !== null) {
                    if (jamPulangAktualMenit < jamPulangMenit) {
                        totalPulangAwal += (jamPulangMenit - jamPulangAktualMenit) / 60;
                    } else if (jamPulangAktualMenit > jamPulangMenit) {
                        totalLembur += (jamPulangAktualMenit - jamPulangMenit) / 60;
                    }
                }
            }
        });

        $('#edit_total_jam_kerja').val(totalJamKerja.toFixed(2));
        $('#edit_total_terlambat').val(totalTerlambat.toFixed(2));
        $('#edit_total_pulang_awal').val(totalPulangAwal.toFixed(2));
        $('#edit_total_lembur').val(totalLembur.toFixed(2));
        
        // Calculate nominal lembur after updating total lembur
        calculateNominalLemburEdit();
    }

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
        $('#edit_total_lembur').val(abs.total_lembur || '0.00');
        
        // Set holiday fields
        $('#edit_is_hari_libur').val(abs.is_hari_libur ? 1 : 0);
        $('#edit_id_tarif_lembur').val(abs.id_tarif_lembur || '');
        $('#edit_tarif_lembur_info').val(abs.tarif_lembur_nama || '-');
        $('#edit_tarif_per_jam').val(abs.tarif_per_jam || 0);
        $('#edit_tarif_per_jam_display').val(formatRupiahEdit(abs.tarif_per_jam || 0));
        $('#edit_nominal_lembur').val(abs.nominal_lembur || 0);
        $('#edit_nominal_lembur_display').val(formatRupiahEdit(abs.nominal_lembur || 0));
        
        // Always check holiday status and refresh tarif lembur from server
        // This ensures the latest tarif data is loaded
        checkHolidayEdit(abs.tanggal);
        
        // Show holiday badge if applicable
        if (abs.is_hari_libur) {
            $('#edit_holiday_badge').show();
        } else {
            $('#edit_holiday_badge').hide();
        }

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
            reindexTable($tbody);
            recalculateTotalsEdit();
        });

        // Re-bind change for duration calculation
        $tbody.off('change', '.edit_detail_mulai, .edit_detail_selesai').on('change', '.edit_detail_mulai, .edit_detail_selesai', function () {
            const $row = $(this).closest('tr');
            computeDuration($row, '.edit_detail_durasi', '.edit_detail_mulai', '.edit_detail_selesai');
            recalculateTotalsEdit();
        });

        // Re-bind change for jenis absen
        $tbody.off('change', '.edit_detail_jenis').on('change', '.edit_detail_jenis', function () {
            recalculateTotalsEdit();
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
        $('#edit_tanggal').flatpickr({ 
            dateFormat: 'Y-m-d', 
            altInput: true, 
            altFormat: 'd/m/Y',
            onChange: function(selectedDates, dateStr) {
                checkHolidayEdit(dateStr);
            }
        });
        $('#edit_id_sdm, #edit_id_jadwal_karyawan').select2({ dropdownParent: $('#form_edit') });

        // Recalculate when jadwal changes
        $('#edit_id_jadwal_karyawan').off('change.calc').on('change.calc', function() {
            recalculateTotalsEdit();
        });

       // Add row functionality for edit
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

