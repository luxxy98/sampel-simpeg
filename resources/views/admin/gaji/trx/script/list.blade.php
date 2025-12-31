<script defer>
    $('#filter_id_periode, #filter_status').select2();

    let tableGajiTrx;

    $('#btn_filter_reload').on('click', function () {
        if (tableGajiTrx) tableGajiTrx.ajax.reload();
    });

    tableGajiTrx = $('#example').DataTable({
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
            url: '{{ route('admin.gaji.trx.list') }}',
            cache: false,
            data: function (d) {
                d.id_periode = $('#filter_id_periode').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'periode', name: 'periode' },
            { data: 'sdm', name: 'sdm' },
            { data: 'total_penghasilan', name: 'total_penghasilan' },
            { data: 'total_potongan', name: 'total_potongan' },
            { data: 'total_take_home_pay', name: 'total_take_home_pay' },
            { data: 'status', name: 'status' },
        ],
    });
</script>
