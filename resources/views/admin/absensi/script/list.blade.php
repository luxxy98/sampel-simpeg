<script defer>
    function load_absensi() {
        $.fn.dataTable.ext.errMode = 'none';
        const table = $('#table-absensi').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            ajax: {
                url: "{{ route('admin.absensi.datatable') }}", // sesuaikan route
                type: "GET"
            },
            dom: "lBfrtip",
            stateSave: true,
            stateDuration: -1,
            pageLength: 10,
            lengthMenu: [
                [10, 15, 20, 25],
                [10, 15, 20, 25]
            ],
            buttons: [{
                    extend: 'colvis',
                    collectionLayout: 'fixed columns',
                    text: 'Kolom'
                },
                {
                    extend: 'excelHtml5',
                    title: 'Data Absensi'
                },
                {
                    extend: 'print',
                    title: 'Data Absensi'
                }
            ],
            columns: [{
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'nama_sdm',
                    name: 'nama_sdm'
                },
                {
                    data: 'jadwal',
                    name: 'jadwal'
                },
                {
                    data: 'total_jam_kerja',
                    name: 'total_jam_kerja'
                },
                {
                    data: 'total_terlambat',
                    name: 'total_terlambat'
                },
                {
                    data: 'total_pulang_awal',
                    name: 'total_pulang_awal'
                },
            ]
        });

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
                    $('#table-absensi').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    let msg = 'Gagal menghapus';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                }
            });
        }



        // search debounce
        function performOptimizedSearch(query) {
            try {
                if (query.length >= 3 || query.length === 0) {
                    table.search(query).draw();
                }
            } catch (e) {
                console.error(e);
            }
        }

        $('#table-absensi_filter input').unbind().on('input', function() {
            performOptimizedSearch($(this).val());
        });
    }

    load_absensi();
</script>
