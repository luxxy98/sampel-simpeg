<script defer>
    $('#filter_id_jabatan, #filter_jenis').select2();

    let tableKomponenGaji;

    $('#btn_filter_komponen').on('click', function () {
        if (tableKomponenGaji) tableKomponenGaji.ajax.reload();
    });

    tableKomponenGaji = $('#table_komponen').DataTable({
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
            url: '{{ route('admin.gaji.komponen.list') }}',
            cache: false,
            data: function (d) {
                d.id_jabatan = $('#filter_id_jabatan').val();
                d.jenis = $('#filter_jenis').val(); // PENGHASILAN / POTONGAN
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'jabatan', name: 'jabatan' },
            { data: 'nama_komponen', name: 'nama_komponen' },
            { data: 'jenis', name: 'jenis' },
            { data: 'nominal', name: 'nominal' },
        ],
    });
</script>
