<script defer>
    function load_data() {
        $.fn.dataTable.ext.errMode = 'none';
        const table = $('#example').DataTable({
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
            ajax: { url: '{{ route('admin.ref.libur-nasional.list') }}', cache: false },
            order: [],
            ordering: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'keterangan', name: 'keterangan' },
            ]
        });

        const performOptimizedSearch = _.debounce(function (query) {
            if (query.length >= 3 || query.length === 0) table.search(query).draw();
        }, 1000);

        $('#example_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });
    }

    load_data();
</script>
