<script defer>
    const tablePeriodeGaji = $('#example').DataTable({
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
        ajax: { url: '{{ route('admin.gaji.periode.list') }}', cache: false },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'tahun', name: 'tahun' },
            { data: 'bulan', name: 'bulan' },
            { data: 'tanggal_mulai', name: 'tanggal_mulai', render: (d) => d ? formatter.formatDate(d) : '' },
            { data: 'tanggal_selesai', name: 'tanggal_selesai', render: (d) => d ? formatter.formatDate(d) : '' },
            { data: 'tanggal_penggajian', name: 'tanggal_penggajian', render: (d) => d ? formatter.formatDate(d) : '-' },
            { data: 'status', name: 'status' },
            { data: 'status_peninjauan', name: 'status_peninjauan' },
        ],
    });
</script>
