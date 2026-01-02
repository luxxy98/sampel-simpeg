<script defer>
    $('#filter_id_periode, #filter_status').select2();

    let tableDistribusi;

    // Format number sebagai rupiah
    function formatRupiahDistribusi(num) {
        if (num === null || num === undefined) return 'Rp 0';
        const number = parseFloat(num);
        if (isNaN(number)) return 'Rp 0';
        return 'Rp ' + number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

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
            { 
                data: 'jumlah_transfer', 
                name: 'jumlah_transfer',
                className: 'text-end fw-bold text-success',
                render: function(data) { return formatRupiahDistribusi(data); }
            },
            { data: 'status_transfer', name: 'status_transfer' },
            { data: 'tanggal_transfer', name: 'tanggal_transfer' },
            { data: 'catatan', name: 'catatan' },
        ],
    });
</script>
