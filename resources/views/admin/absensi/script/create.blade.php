<script defer>
    // Data jadwal dengan jam_masuk dan jam_pulang
    const jadwalData = @json($jadwalOptions ?? []);
    
    // Data tarif lembur
    const tarifLemburData = @json($tarifLemburOptions ?? []);
    
    // Data hari libur
    const holidayDates = @json($holidayDates ?? []);
    
    // ID jenis absen HADIR (untuk filter perhitungan)
    const HADIR_IDS = [
        @foreach($jenisAbsenOptions ?? [] as $j)
            @if(strtoupper($j['nama_absen'] ?? '') === 'HADIR')
                {{ $j['id_jenis_absen'] }},
            @endif
        @endforeach
    ];

    // URL untuk check holiday via AJAX
    const CHECK_HOLIDAY_URL = '{{ route("admin.absensi.check-holiday") }}';

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

    function getJadwalById(id) {
        return jadwalData.find(j => j.id_jadwal_karyawan == id);
    }

    function parseTimeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const parts = timeStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }

    function extractTimeFromDatetime(datetimeStr) {
        if (!datetimeStr) return null;
        const match = datetimeStr.match(/(\d{2}):(\d{2})/);
        if (match) {
            return parseInt(match[1]) * 60 + parseInt(match[2]);
        }
        return null;
    }
    
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function checkHoliday(tanggal) {
        if (!tanggal) {
            updateHolidayUI(false, null, null);
            return;
        }
        
        // Quick client-side check first
        const isSunday = new Date(tanggal).getDay() === 0;
        const isInHolidayList = holidayDates.includes(tanggal);
        
        if (isSunday || isInHolidayList) {
            // Do AJAX to get full info
            $.get(CHECK_HOLIDAY_URL, { tanggal: tanggal }, function(response) {
                if (response.success && response.data) {
                    updateHolidayUI(
                        response.data.is_hari_libur,
                        response.data.holiday_name,
                        response.data
                    );
                }
            });
        } else {
            // Also do AJAX to ensure we get correct tarif
            $.get(CHECK_HOLIDAY_URL, { tanggal: tanggal }, function(response) {
                if (response.success && response.data) {
                    updateHolidayUI(
                        response.data.is_hari_libur,
                        response.data.holiday_name,
                        response.data
                    );
                }
            }).fail(function() {
                // Fallback: use default tarif biasa
                updateHolidayUI(false, null, {
                    id_tarif_lembur: tarifLemburData[0]?.id_tarif || null,
                    nama_tarif: tarifLemburData[0]?.nama_tarif || '-',
                    tarif_per_jam: tarifLemburData[0]?.tarif_per_jam || 0
                });
            });
        }
    }
    
    function updateHolidayUI(isHoliday, holidayName, tarifData) {
        // Update holiday badge
        if (isHoliday) {
            $('#holiday_badge').show();
            $('#holiday_name').text(holidayName || 'Hari Libur');
        } else {
            $('#holiday_badge').hide();
        }
        
        // Update hidden fields
        $('#is_hari_libur').val(isHoliday ? 1 : 0);
        
        if (tarifData) {
            $('#id_tarif_lembur').val(tarifData.id_tarif_lembur || '');
            $('#tarif_lembur_info').val(tarifData.nama_tarif || '-');
            $('#tarif_per_jam').val(tarifData.tarif_per_jam || 0);
            $('#tarif_per_jam_display').val(formatRupiah(tarifData.tarif_per_jam || 0));
        }
        
        // Recalculate nominal lembur
        calculateNominalLembur();
    }
    
    function calculateNominalLembur() {
        const totalLembur = parseFloat($('#total_lembur').val()) || 0;
        const tarifPerJam = parseFloat($('#tarif_per_jam').val()) || 0;
        const nominal = totalLembur * tarifPerJam;
        
        $('#nominal_lembur').val(nominal);
        $('#nominal_lembur_display').val(formatRupiah(nominal));
    }

    function recalculateTotals() {
        const jadwalId = $('#id_jadwal_karyawan').val();
        const jadwal = getJadwalById(jadwalId);
        
        if (!jadwal) {
            $('#total_jam_kerja').val('0.00');
            $('#total_terlambat').val('0.00');
            $('#total_pulang_awal').val('0.00');
            $('#total_lembur').val('0.00');
            calculateNominalLembur();
            return;
        }

        const jamMasukMenit = parseTimeToMinutes(jadwal.jam_masuk);
        const jamPulangMenit = parseTimeToMinutes(jadwal.jam_pulang);

        let totalJamKerja = 0;
        let totalTerlambat = 0;
        let totalPulangAwal = 0;
        let totalLembur = 0;

        $('#table_detail_create tbody tr').each(function() {
            const $row = $(this);
            const jenisAbsen = $row.find('.detail_jenis').val();
            const durasi = parseFloat($row.find('.detail_durasi').val()) || 0;
            const waktuMulai = $row.find('.detail_mulai').val();
            const waktuSelesai = $row.find('.detail_selesai').val();

            // Hitung hanya untuk jenis HADIR
            if (HADIR_IDS.includes(parseInt(jenisAbsen))) {
                totalJamKerja += durasi;

                // Hitung keterlambatan (jam datang > jam masuk jadwal)
                const jamDatangMenit = extractTimeFromDatetime(waktuMulai);
                if (jamDatangMenit !== null && jamDatangMenit > jamMasukMenit) {
                    totalTerlambat += (jamDatangMenit - jamMasukMenit) / 60;
                }

                // Hitung pulang awal (jam pulang < jam pulang jadwal)
                let jamPulangAktualMenit = extractTimeFromDatetime(waktuSelesai);
                
                // Handle waktu setelah tengah malam (misal 01:00 = 25 jam dalam konteks shift malam)
                if (jamPulangAktualMenit !== null && jamPulangAktualMenit < 7 * 60) {
                    jamPulangAktualMenit += 24 * 60; // Tambah 24 jam jika pulang setelah tengah malam
                }
                
                if (jamPulangAktualMenit !== null) {
                    if (jamPulangAktualMenit < jamPulangMenit) {
                        // Pulang lebih awal dari jadwal
                        totalPulangAwal += (jamPulangMenit - jamPulangAktualMenit) / 60;
                    } else if (jamPulangAktualMenit > jamPulangMenit) {
                        // Lembur: pulang lebih dari jadwal
                        totalLembur += (jamPulangAktualMenit - jamPulangMenit) / 60;
                    }
                }
            }
        });

        $('#total_jam_kerja').val(totalJamKerja.toFixed(2));
        $('#total_terlambat').val(totalTerlambat.toFixed(2));
        $('#total_pulang_awal').val(totalPulangAwal.toFixed(2));
        $('#total_lembur').val(totalLembur.toFixed(2));
        
        // Calculate nominal lembur after updating total lembur
        calculateNominalLembur();
    }

    $('#form_create').on('show.bs.modal', function () {
        $('#tanggal').flatpickr({ 
            dateFormat: 'Y-m-d', 
            altInput: true, 
            altFormat: 'd/m/Y',
            onChange: function(selectedDates, dateStr) {
                checkHoliday(dateStr);
            }
        });

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
            recalculateTotals();
        });

        // Recalculate when time changes
        $tbody.off('change', '.detail_mulai, .detail_selesai').on('change', '.detail_mulai, .detail_selesai', function () {
            const $row = $(this).closest('tr');
            computeDuration($row, '.detail_durasi', '.detail_mulai', '.detail_selesai');
            recalculateTotals();
        });

        // Recalculate when jenis absen changes
        $tbody.off('change', '.detail_jenis').on('change', '.detail_jenis', function () {
            recalculateTotals();
        });

        // Recalculate when jadwal changes
        $('#id_jadwal_karyawan').off('change.calc').on('change.calc', function() {
            recalculateTotals();
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
        
        // Reset holiday UI
        $('#holiday_badge').hide();
        $('#is_hari_libur').val(0);
        $('#id_tarif_lembur').val('');
        $('#tarif_lembur_info').val('-');
        $('#tarif_per_jam').val(0);
        $('#tarif_per_jam_display').val('Rp 0');
        $('#nominal_lembur').val(0);
        $('#nominal_lembur_display').val('Rp 0');
    });
</script>

