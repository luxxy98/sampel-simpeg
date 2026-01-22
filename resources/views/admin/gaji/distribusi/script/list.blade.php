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

    // Quick action: update status transfer
    function updateStatusDistribusi(id, status) {
        const title = status === 'SUCCESS' ? 'Konfirmasi Transfer Sukses' : 'Konfirmasi Transfer Gagal';
        const text = status === 'SUCCESS' 
            ? 'Apakah transfer sudah berhasil dilakukan?' 
            : 'Tandai transfer ini sebagai gagal?';
        const icon = status === 'SUCCESS' ? 'question' : 'warning';

        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonText: 'Ya, Konfirmasi',
            cancelButtonText: 'Batal',
            confirmButtonColor: status === 'SUCCESS' ? '#28a745' : '#dc3545',
        }).then((result) => {
            if (result.isConfirmed) {
                DataManager.openLoading();
                DataManager.postData(`{{ url('admin/gaji/distribusi/update-status') }}/${id}`, { status: status })
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tableDistribusi.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(err => ErrorHandler.handleError(err));
            }
        });
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

