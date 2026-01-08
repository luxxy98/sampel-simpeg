<script defer>
    const tableJadwalKerja = $('#table_jadwal_kerja').DataTable({
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
        ajax: { url: '{{ route('admin.absensi.jadwal-kerja.list') }}', cache: false },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_jadwal', name: 'nama_jadwal' },
            { data: 'jam_masuk', name: 'jam_masuk' },
            { data: 'jam_pulang', name: 'jam_pulang' },
            { data: 'keterangan', name: 'keterangan' },
        ],
    });
</script>
