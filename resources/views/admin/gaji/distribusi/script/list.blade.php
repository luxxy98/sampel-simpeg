<script defer>
    $('#filter_id_periode, #filter_status').select2();

    let tableDistribusi;

    $('#btn_filter_reload').on('click', function () {
        if (tableDistribusi) tableDistribusi.ajax.reload();
    });

    tableDistribusi = $('#example').DataTable({
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
        ajax: {
            url: '{{ route('admin.gaji.distribusi.list') }}',
            cache: false,
            data: function (d) {
                d.id_periode = $('#filter_id_periode').val();
                d.status_transfer = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'periode', name: 'periode' },
            { data: 'sdm', name: 'sdm' },
            { data: 'rekening', name: 'rekening' },
            { data: 'jumlah_transfer', name: 'jumlah_transfer' },
            { data: 'status_transfer', name: 'status_transfer' },
            { data: 'tanggal_transfer', name: 'tanggal_transfer' },
            { data: 'catatan', name: 'catatan' },
        ],
    });
</script>
