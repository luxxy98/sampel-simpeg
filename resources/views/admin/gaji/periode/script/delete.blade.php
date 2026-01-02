<script defer>
    function deletePeriode(id) {
        Swal.fire({
            title: 'Hapus periode?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            allowOutsideClick: false
        }).then(async (r) => {
            if (!r.value) return;

            try {
                DataManager.openLoading();
                const url = '{{ route('admin.gaji.periode.destroy', ['id' => '___ID___']) }}'.replace('___ID___', id);
                const res = await DataManager.deleteData(url);
                Swal.close();

                if (res.success) {
                    Swal.fire('Success', res.message, 'success');
                    setTimeout(() => location.reload(), 700);
                    return;
                }
                Swal.fire('Peringatan', res.message || 'Gagal hapus', 'warning');
            } catch (e) {
                ErrorHandler.handleError(e);
            }
        });
    }
</script>
