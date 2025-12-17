<script defer>
    let tableAbsensi;

    function initFilterAbsensi() {
        $('#filter_tanggal_mulai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
        $('#filter_tanggal_selesai').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });

        $('#btn_filter_reload').on('click', function () {
            if (tableAbsensi) tableAbsensi.ajax.reload();
        });
    }

    function load_data_absensi() {
        $.fn.dataTable.ext.errMode = 'none';

        tableAbsensi = $('#example').DataTable({
            dom: 'lBfrtip',
            stateSave: true,
            stateDuration: -1,
            pageLength: 10,
            lengthMenu: [[10, 15, 20, 25], [10, 15, 20, 25]],
            buttons: [
                { extend: 'colvis', className: 'btn btn-sm btn-dark rounded-2', columns: ':not(.noVis)' },
                { extend: 'csv', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
                { extend: 'excel', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' }
            ],
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('admin.absensi.list') }}',
                cache: false,
                data: function (d) {
                    d.tanggal_mulai = $('#filter_tanggal_mulai').val();
                    d.tanggal_selesai = $('#filter_tanggal_selesai').val();
                    d.id_sdm = $('#filter_id_sdm').val();
                }
            },
            order: [],
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal', render: (d) => d ? formatter.formatDate(d) : '' },
                { data: 'sdm', name: 'sdm' },
                { data: 'jadwal', name: 'jadwal' },
                { data: 'total_jam_kerja', name: 'total_jam_kerja' },
                { data: 'total_terlambat', name: 'total_terlambat' },
                { data: 'total_pulang_awal', name: 'total_pulang_awal' },
            ],
        });

        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) tableAbsensi.search(query).draw();
        }, 1000);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });
    }

    initFilterAbsensi();
    load_data_absensi();
</script>
