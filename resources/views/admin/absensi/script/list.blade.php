<script defer>
    let tableAbsensi;

    function initFilterAbsensi() {
        $('#filter_tanggal_mulai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#filter_tanggal_selesai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#filter_id_sdm').select2({ allowClear: true, placeholder: 'Semua SDM' });

        $('#btn_filter_reload').off('click').on('click', function (e) {
            e.preventDefault();
            console.log('Filter clicked - reloading table...');
            if (tableAbsensi) tableAbsensi.ajax.reload();
        });
    }

    function load_data_absensi() {
        $.fn.dataTable.ext.errMode = 'none';

        console.log('Loading DataTable...');

        tableAbsensi = $('#example').DataTable({
            dom: 'lBfrtip',
            stateSave: false, // Disabled to prevent cached filter issues
            lengthMenu: [[10, 15, 20, 25], [10, 15, 20, 25]],
            buttons: [
                { extend: 'colvis', className: 'btn btn-sm btn-dark rounded-2', columns: ':not(.noVis)' },
                { extend: 'csv', className: 'btn btn-sm btn-dark rounded-2', title: 'Data Absensi' },
                { extend: 'excel', className: 'btn btn-sm btn-dark rounded-2', title: 'Data Absensi' }
            ],
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('admin.absensi.list') }}',
                cache: false,
                data: function (d) {
                    d.tanggal_mulai = $('#filter_tanggal_mulai').val() || '';
                    d.tanggal_selesai = $('#filter_tanggal_selesai').val() || '';
                    d.id_sdm = $('#filter_id_sdm').val() || '';
                    console.log('Sending filter params:', d.tanggal_mulai, d.tanggal_selesai, d.id_sdm);
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTables Error:', xhr, error, thrown);
                    console.error('Response:', xhr.responseText);
                    alert('Gagal memuat data: ' + (xhr.responseJSON?.message || thrown || error));
                }
            },
            order: [], // Default no ordering, let server decide or user click
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { 
                    data: 'tanggal', 
                    name: 'tanggal',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '';
                    }
                },
                { data: 'sdm', name: 'sdm', orderable: false, searchable: false },
                { data: 'jadwal', name: 'jadwal', orderable: false, searchable: false },
                { data: 'total_jam_kerja', name: 'total_jam_kerja' },
                { data: 'total_terlambat', name: 'total_terlambat' },
                { data: 'total_pulang_awal', name: 'total_pulang_awal' },
            ],
            initComplete: function() {
                console.log('DataTable initialized successfully');
            }
        });

        // Search Debounce
        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) {
                tableAbsensi.search(query).draw();
            }
        }, 800);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });
    }

    function deleteAbsensi(id) {
        if (!confirm('Yakin ingin menghapus data absensi ini?')) return;

        $.ajax({
            url: "{{ url('admin/absensi') }}/" + id,
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                alert(res.message || 'Berhasil menghapus');
                if (tableAbsensi) tableAbsensi.ajax.reload(null, false);
            },
            error: function(xhr) {
                let msg = 'Gagal menghapus';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alert(msg);
            }
        });
    }

    $(document).ready(function() {
        initFilterAbsensi();
        load_data_absensi();
    });
</script>
