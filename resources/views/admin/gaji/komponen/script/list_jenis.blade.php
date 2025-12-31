<script defer>
    let tableJenisKomponen;

    tableJenisKomponen = $('#table_jenis').DataTable({
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
        ajax: { url: '{{ route('admin.gaji.jenis-komponen.list') }}', cache: false },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_komponen', name: 'nama_komponen' },
            { data: 'jenis', name: 'jenis' },
        ],
    });
</script>
