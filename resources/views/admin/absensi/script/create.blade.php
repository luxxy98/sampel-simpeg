<script defer>
    // Data jadwal dengan jam_masuk dan jam_pulang
    let jadwalData = @json($jadwalOptions ?? []);
    
    // ID jenis absen HADIR (untuk filter perhitungan)
    const HADIR_IDS = [
        @foreach($jenisAbsenOptions ?? [] as $j)
            @if(strtoupper($j['nama_absen'] ?? '') === 'HADIR')
                {{ $j['id_jenis_absen'] }},
            @endif
        @endforeach
    ];

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
        // Format: "YYYY-MM-DD HH:mm:ss" or "YYYY-MM-DDTHH:mm:ss"
        const match = datetimeStr.match(/(\d{2}):(\d{2})/);
        if (match) {
            return parseInt(match[1]) * 60 + parseInt(match[2]);
        }
        return null;
    }

    function recalculateTotals() {
        const jadwalId = $('#id_jadwal_karyawan').val();
        const jadwal = getJadwalById(jadwalId);
        
        if (!jadwal) {
            $('#total_jam_kerja').val('0.00');
            $('#total_terlambat').val('0.00');
            $('#total_pulang_awal').val('0.00');
            $('#total_lembur').val('0.00');
            return;
        }

        const jamMasukMenit = parseTimeToMinutes(jadwal.jam_masuk);
        const jamPulangMenit = parseTimeToMinutes(jadwal.jam_pulang);
        
        // Hitung durasi jadwal normal (dalam jam)
        let durasiJadwalNormal = (jamPulangMenit - jamMasukMenit) / 60;
        // Handle overnight shift (misalnya 22:00 - 06:00 = 8 jam)
        if (durasiJadwalNormal <= 0) {
            durasiJadwalNormal += 24; // shift melewati tengah malam
        }

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

                // Hitung pulang awal dan lembur menggunakan full datetime comparison
                if (waktuMulai && waktuSelesai) {
                    const startDate = new Date(waktuMulai.replace(' ', 'T'));
                    const endDate = new Date(waktuSelesai.replace(' ', 'T'));
                    
                    // Buat jadwal pulang berdasarkan tanggal mulai
                    const scheduledEnd = new Date(startDate);
                    const pulangHours = Math.floor(jamPulangMenit / 60);
                    const pulangMinutes = jamPulangMenit % 60;
                    scheduledEnd.setHours(pulangHours, pulangMinutes, 0, 0);
                    
                    // Jika jadwal pulang <= jam masuk, berarti shift melewati tengah malam (misal 22:00-06:00)
                    if (jamPulangMenit <= jamMasukMenit) {
                        scheduledEnd.setDate(scheduledEnd.getDate() + 1);
                    }
                    
                    // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                    // Contoh: masuk 15:00, pulang 00:00 keesokan hari
                    let adjustedEndDate = new Date(endDate);
                    // Cek berdasarkan jam: jika jam selesai < jam mulai, berarti sudah lewat tengah malam
                    const endHour = endDate.getHours();
                    const startHour = startDate.getHours();
                    if (endHour < startHour && endDate.getTime() <= startDate.getTime()) {
                        // Waktu selesai di hari berikutnya
                        adjustedEndDate.setDate(adjustedEndDate.getDate() + 1);
                    }
                    
                    // Pulang awal: jika waktu selesai < jadwal pulang
                    if (adjustedEndDate < scheduledEnd) {
                        const diffMs = scheduledEnd.getTime() - adjustedEndDate.getTime();
                        totalPulangAwal += diffMs / 3600000; // convert ms to hours
                    }
                    
                    // Lembur: jika waktu selesai > jadwal pulang
                    if (adjustedEndDate > scheduledEnd) {
                        const diffMs = adjustedEndDate.getTime() - scheduledEnd.getTime();
                        totalLembur += diffMs / 3600000; // convert ms to hours
                    }
                }
            }
        });

        $('#total_jam_kerja').val(totalJamKerja.toFixed(2));
        $('#total_terlambat').val(totalTerlambat.toFixed(2));
        $('#total_pulang_awal').val(totalPulangAwal.toFixed(2));
        $('#total_lembur').val(totalLembur.toFixed(2));

        // Calculate nominal lembur based on tarif per jam
        const tarifPerJam = parseFloat($('#tarif_per_jam').val()) || 0;
        const nominalLembur = totalLembur * tarifPerJam;
        $('#nominal_lembur').val(nominalLembur);
        $('#nominal_lembur_display').val('Rp ' + new Intl.NumberFormat('id-ID').format(nominalLembur));
    }

    // Load holiday info and tarif lembur based on selected date
    function loadHolidayInfo(tanggal) {
        if (!tanggal) {
            $('#id_tarif_lembur').val('');
            $('#tarif_lembur_info').val('-');
            $('#tarif_per_jam').val('0');
            $('#tarif_per_jam_display').val('Rp 0');
            $('#is_hari_libur').val('0');
            $('#holiday_badge').hide();
            recalculateTotals();
            return;
        }

        $.ajax({
            url: "{{ route('admin.absensi.holiday-info') }}",
            method: "GET",
            data: { tanggal: tanggal },
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;
                    
                    // Set tarif lembur info
                    $('#id_tarif_lembur').val(data.id_tarif_lembur || '');
                    $('#tarif_lembur_info').val(data.nama_tarif || '-');
                    $('#tarif_per_jam').val(data.tarif_per_jam || 0);
                    $('#tarif_per_jam_display').val('Rp ' + new Intl.NumberFormat('id-ID').format(data.tarif_per_jam || 0));
                    $('#is_hari_libur').val(data.is_hari_libur ? '1' : '0');
                    
                    // Show/hide holiday badge
                    if (data.is_hari_libur && data.holiday_name) {
                        $('#holiday_name').text(data.holiday_name);
                        $('#holiday_badge').show();
                    } else {
                        $('#holiday_badge').hide();
                    }
                    
                    // Recalculate nominal lembur with new tarif
                    recalculateTotals();
                }
            },
            error: function (xhr) {
                console.error('Failed to load holiday info:', xhr);
            }
        });
    }

    $('#form_create').on('show.bs.modal', function () {
        $('#tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });

        // init select2
        $('#id_sdm, #id_jadwal_karyawan').select2({ dropdownParent: $('#form_create') });

        function fillJadwalSelect(options) {
    const $sel = $('#id_jadwal_karyawan');

    // replace options
    $sel.empty().append(new Option('', '', true, false));

    (options || []).forEach(o => {
        const text = `${o.nama_jadwal} (${o.jam_masuk} - ${o.jam_pulang}) | ${o.tanggal_mulai} s/d ${o.tanggal_selesai}`;
        $sel.append(new Option(text, o.id_jadwal_karyawan, false, false));
    });

    // update data untuk perhitungan total (terlambat/pulang awal)
    jadwalData = options || [];

    // auto pilih kalau cuma 1
    if ((options || []).length === 1) {
        $sel.val(String(options[0].id_jadwal_karyawan)).trigger('change');
    } else {
        $sel.val(null).trigger('change');
    }

    recalculateTotals();
}

function loadJadwalKaryawanBySdmTanggal() {
    const idSdm = $('#id_sdm').val();
    const tanggal = $('#tanggal').val(); // flatpickr menyimpan YYYY-MM-DD di input ini

    if (!idSdm || !tanggal) {
        fillJadwalSelect([]);
        return;
    }

    $.ajax({
        url: "{{ route('admin.absensi.jadwal-karyawan.options') }}",
        method: "GET",
        data: { id_sdm: idSdm, tanggal: tanggal },
        success: function (res) {
            const options = res?.data?.options || [];
            fillJadwalSelect(options);

            if (options.length === 0) {
                Swal.fire(
                    'Warning',
                    `Jadwal karyawan untuk SDM ini pada tanggal ${tanggal} belum diset. Silakan set di menu Jadwal Karyawan.`,
                    'warning'
                );
            }
        },
        error: function (xhr) {
            console.error(xhr);
            fillJadwalSelect([]);
            Swal.fire('Error', 'Gagal mengambil jadwal karyawan. Cek console/log.', 'error');
        }
    });
}

// trigger saat SDM berubah
$('#id_sdm').off('change.loadJadwal').on('change.loadJadwal', function () {
    loadJadwalKaryawanBySdmTanggal();
});

// trigger saat tanggal berubah (flatpickr)
const fp = $('#tanggal').data('flatpickr');
if (fp) {
    fp.set('onChange', function (selectedDates, dateStr) {
        loadJadwalKaryawanBySdmTanggal();
        loadHolidayInfo(dateStr);
    });
} else {
    $('#tanggal').off('change.loadJadwal').on('change.loadJadwal', function () {
        loadJadwalKaryawanBySdmTanggal();
        loadHolidayInfo($(this).val());
    });
}

// saat modal dibuka, kalau SDM & tanggal sudah terisi → langsung load
loadJadwalKaryawanBySdmTanggal();


        // Auto-resolve jadwal berdasarkan tabel assignment sdm_jadwal_karyawan
        const resolveJadwalUrl = '{{ route('admin.absensi.resolve-jadwal') }}';
        async function resolveJadwalCreate() {
            const idSdm = $('#id_sdm').val();
            const tgl = $('#tanggal').val();
            if (!idSdm || !tgl) return;

            try {
                const res = await DataManager.readData(resolveJadwalUrl, { id_sdm: idSdm, tanggal: tgl });
                if (res && res.success && res.data && res.data.id_jadwal_karyawan) {
                    $('#id_jadwal_karyawan').val(res.data.id_jadwal_karyawan).trigger('change');
                }
            } catch (e) {
                console.log('resolve jadwal gagal', e);
            }
        }

        $('#id_sdm').off('change.resolve').on('change.resolve', resolveJadwalCreate);
        $('#tanggal').off('change.resolve').on('change.resolve', resolveJadwalCreate);

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
    });
</script>
