<script defer>
    window.cutiTable = null;

    function badgeStatus(st) {
        if (st === 'diajukan') return '<span class="badge badge-warning">Diajukan</span>';
        if (st === 'disetujui') return '<span class="badge badge-success">Disetujui</span>';
        if (st === 'ditolak') return '<span class="badge badge-danger">Ditolak</span>';
        return st ?? '-';
    }

    function load_data() {
        $.fn.dataTable.ext.errMode = 'none';

        $('#filter_status').select2({ width: '100%' });
        $('#filter_id_sdm').select2({ width: '100%' });

        window.cutiTable = $('#example').DataTable({
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
                url: '{{ route('admin.cuti.pengajuan.list') }}',
                cache: false,
                data: function (d) {
                    d.tanggal_mulai = $('#filter_tanggal_mulai').val();
                    d.tanggal_selesai = $('#filter_tanggal_selesai').val();
                    d.status = $('#filter_status').val();
                    d.id_sdm = $('#filter_id_sdm').val();
                }
            },
            order: [],
            ordering: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'nama_jenis', name: 'nama_jenis' },
                { data: 'tanggal_mulai', name: 'tanggal_mulai' },
                { data: 'tanggal_selesai', name: 'tanggal_selesai' },
                { data: 'jumlah_hari', name: 'jumlah_hari' },
                { data: 'status', name: 'status', render: function(data){ return badgeStatus(data); } },
                { data: 'tanggal_pengajuan', name: 'tanggal_pengajuan' },
            ]
        });

        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) window.cutiTable.search(query).draw();
        }, 1000);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });

        $('#btn_filter').on('click', function () {
            window.cutiTable.ajax.reload();
        });

        $('#btn_reset').on('click', function () {
            $('#filter_tanggal_mulai').val('');
            $('#filter_tanggal_selesai').val('');
            $('#filter_status').val('').trigger('change');
            $('#filter_id_sdm').val('').trigger('change');
            window.cutiTable.ajax.reload();
        });
    }

    load_data();
</script>
