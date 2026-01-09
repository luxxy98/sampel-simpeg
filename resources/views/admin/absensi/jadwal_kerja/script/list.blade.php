<script defer>
    // Pastikan table tidak dobel-initialize
    if (window.tableJadwalKerja && $.fn.DataTable.isDataTable('#table_jadwal_kerja')) {
        window.tableJadwalKerja.destroy();
        $('#table_jadwal_kerja tbody').empty();
    }

    window.tableJadwalKerja = $('#table_jadwal_kerja').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        stateSave: true,
        stateDuration: -1,
        pageLength: 10,
        ajax: {
            url: '{{ route('admin.absensi.jadwal-kerja.list') }}',
            type: 'GET',
            cache: false
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_jadwal', name: 'nama_jadwal' },
            { data: 'jam_masuk', name: 'jam_masuk' },
            { data: 'jam_pulang', name: 'jam_pulang' },
            { data: 'keterangan', name: 'keterangan' },
        ],
    });
</script>
