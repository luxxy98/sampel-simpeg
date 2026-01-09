<script defer>
    window.jadwalKaryawanTable = null;

    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';

        window.jadwalKaryawanTable = $('#table_jadwal_karyawan').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: false,
            ajax: {
                url: '{{ route('admin.absensi.jadwal-karyawan.list') }}',
                type: 'GET',
                cache: false,
                error: function (xhr, error, thrown) {
                    console.error('DT Error:', xhr.responseText);
                    Swal.fire('Error', 'Gagal memuat data Jadwal Karyawan. Cek console/log.', 'error');
                }
            },
            columns: [
                { data: 'action', orderable: false, searchable: false },
                { data: 'sdm', orderable: false, searchable: true },
                { data: 'jadwal', orderable: false, searchable: true },
                { data: 'tanggal_mulai', name: 'tanggal_mulai' },
                { data: 'tanggal_selesai', name: 'tanggal_selesai' },
            ],
        });
    });
</script>
