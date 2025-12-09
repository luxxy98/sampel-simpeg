<script defer>
    function load_periode_gaji() {
        $.fn.dataTable.ext.errMode = 'none';
        const table = $('#table-periode-gaji').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            ajax: {
                url: "{{ route('admin.gaji.periode.datatable') }}", // sesuaikan
                type: 'GET'
            },
            dom: "lBfrtip",
            stateSave: true,
            stateDuration: -1,
            pageLength: 10,
            lengthMenu: [
                [10, 15, 20, 25],
                [10, 15, 20, 25]
            ],
            buttons: [
                { extend: 'colvis', text: 'Kolom' },
                { extend: 'excelHtml5', title: 'Periode Gaji' },
                { extend: 'print', title: 'Periode Gaji' }
            ],
            columns: [
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                { data: 'tahun', name: 'tahun' },
                { data: 'bulan', name: 'bulan' },
                { data: 'tanggal_mulai', name: 'tanggal_mulai' },
                { data: 'tanggal_selesai', name: 'tanggal_selesai' },
                { data: 'status', name: 'status' },
                { data: 'status_peninjauan', name: 'status_peninjauan' },
                { data: 'tanggal_penggajian', name: 'tanggal_penggajian' },
            ]
        });

        function performOptimizedSearch(query) {
            try {
                if (query.length >= 3 || query.length === 0) {
                    table.search(query).draw();
                }
            } catch (e) {
                console.error(e);
            }
        }

        $('#table-periode-gaji_filter input').unbind().on('input', function () {
            performOptimizedSearch($(this).val());
        });
    }

    load_periode_gaji();
</script>