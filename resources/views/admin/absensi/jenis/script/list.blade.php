<script defer>
    const tableJenis = $('#example').DataTable({
        dom: 'lBfrtip',
        stateSave: true,
        stateDuration: -1,
        pageLength: 10,
        buttons: [
            { extend: 'csv', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' },
            { extend: 'excel', action: newexportaction, className: 'btn btn-sm btn-dark rounded-2' }
        ],
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: { url: '{{ route('admin.absensi.jenis.list') }}', cache: false },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_absen', name: 'nama_absen' },
            { data: 'kategori', name: 'kategori' },
            { data: 'potong_gaji', name: 'potong_gaji', render: (d) => d == 1 ? 'Ya' : 'Tidak' },
        ],
    });
</script>
