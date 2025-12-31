<script defer>
    async function deleteAbsensi(id_absensi) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data absensi akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
        }).then(async (result) => {
            if (!result.value) return;

            try {
                DataManager.openLoading();
                const url = '{{ route('admin.absensi.destroy', ['id' => '___ID___']) }}'.replace('___ID___', id_absensi);
                const res = await DataManager.deleteData(url);

                Swal.close();

                if (res.success) {
                    Swal.fire('Success', res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                    return;
                }
                Swal.fire('Peringatan', res.message || 'Gagal hapus', 'warning');
            } catch (err) {
                ErrorHandler.handleError(err);
            }
        });
    }
</script>
