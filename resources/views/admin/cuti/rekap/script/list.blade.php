<script defer>
    let rekapTable = null;

    function load_data() {
        $.fn.dataTable.ext.errMode = 'none';

        $('#filter_status').select2({ width: '100%' });
        $('#filter_id_sdm').select2({ width: '100%' });
        $('#filter_id_jenis_cuti').select2({ width: '100%' });

        rekapTable = $('#example').DataTable({
            dom: 'lBfrtip',
            stateSave: true,
            stateDuration: -1,
            pageLength: 10,
            lengthMenu: [[10, 15, 20, 25],[10, 15, 20, 25]],
            buttons: [
                {
                    extend: 'colvis',
                    className: 'btn btn-sm btn-dark rounded-2',
                    columns: ':not(.noVis)'
                },
                { extend: 'csv', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
                { extend: 'excel', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
            ],
            processing: true,
            serverSide: true,
            responsive: true,
            searchHighlight: true,
            ajax: {
                url: '{{ route('admin.cuti.rekap.list') }}',
                cache: false,
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.id_sdm = $('#filter_id_sdm').val();
                    d.id_jenis_cuti = $('#filter_id_jenis_cuti').val();
                }
            },
            order: [],
            ordering: true,
            columns: [
                { data: 'nama', name: 'nama' },
                { data: 'nama_jenis', name: 'nama_jenis' },
                { data: 'jumlah_pengajuan', name: 'jumlah_pengajuan' },
                { data: 'total_hari', name: 'total_hari' },
                { data: 'min_tanggal_mulai', name: 'min_tanggal_mulai' },
                { data: 'max_tanggal_selesai', name: 'max_tanggal_selesai' },
            ]
        });

        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) rekapTable.search(query).draw();
        }, 1000);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });

        $('#btn_filter').on('click', function () {
            rekapTable.ajax.reload();
        });

        $('#btn_reset').on('click', function () {
            $('#filter_status').val('disetujui').trigger('change');
            $('#filter_id_sdm').val('').trigger('change');
            $('#filter_id_jenis_cuti').val('').trigger('change');
            rekapTable.ajax.reload();
        });
    }

    load_data();
</script>
