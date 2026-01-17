<script defer>
    let sppdTable = null;

    function badgeStatus(st) {
        if (st === 'draft') return '<span class="badge badge-secondary">Draft</span>';
        if (st === 'diajukan') return '<span class="badge badge-warning">Diajukan</span>';
        if (st === 'disetujui') return '<span class="badge badge-success">Disetujui</span>';
        if (st === 'ditolak') return '<span class="badge badge-danger">Ditolak</span>';
        if (st === 'selesai') return '<span class="badge badge-success">Selesai</span>';
        return st ?? '-';
    }

    function rupiah(x) {
        const n = parseInt(x ?? 0);
        if (isNaN(n)) return '0';
        return n.toLocaleString('id-ID');
    }

    function load_data() {
        $.fn.dataTable.ext.errMode = 'none';

        $('#filter_status').select2({ width: '100%' });
        $('#filter_id_sdm').select2({ width: '100%' });

        sppdTable = $('#example').DataTable({
            dom: 'lBfrtip',
            stateSave: true,
            stateDuration: -1,
            pageLength: 10,
            lengthMenu: [[10, 15, 20, 25],[10, 15, 20, 25]],
            buttons: [
                { extend: 'colvis', className: 'btn btn-sm btn-dark rounded-2', columns: ':not(.noVis)' },
                { extend: 'csv', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
                { extend: 'excel', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
            ],
            processing: true,
            serverSide: true,
            responsive: true,
            searchHighlight: true,
            ajax: {
                url: '{{ route('admin.sppd.list') }}',
                cache: false,
                data: function (d) {
                    d.status = $('#filter_status').val();
                    d.id_sdm = $('#filter_id_sdm').val();
                }
            },
            order: [],
            ordering: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'nomor_surat', name: 'nomor_surat', render: (d)=> d ?? '-' },
                { data: 'tanggal_berangkat', name: 'tanggal_berangkat' },
                { data: 'tanggal_pulang', name: 'tanggal_pulang' },
                { data: 'tujuan', name: 'tujuan' },
                { data: 'status', name: 'status', render: (d)=> badgeStatus(d) },
                { data: 'total_biaya', name: 'total_biaya', render: (d)=> rupiah(d) },
            ]
        });

        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) sppdTable.search(query).draw();
        }, 1000);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });

        $('#btn_filter').on('click', function () { sppdTable.ajax.reload(); });

        $('#btn_reset').on('click', function () {
            $('#filter_status').val('').trigger('change');
            $('#filter_id_sdm').val('').trigger('change');
            sppdTable.ajax.reload();
        });
    }

    load_data();
</script>
