<script defer>
    const tableJadwalKaryawan = $('#table_jadwal_karyawan').DataTable({
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
        ajax: { url: '{{ route('admin.absensi.jadwal-karyawan.list') }}', cache: false },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_sdm', name: 'nama_sdm' },
            { data: 'nama_jadwal', name: 'nama_jadwal' },
            { data: 'tanggal_mulai', name: 'tanggal_mulai' },
            { data: 'tanggal_selesai', name: 'tanggal_selesai' },
        ],
    });
</script>