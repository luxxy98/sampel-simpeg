<script>
function generateGaji(id) {
    Swal.fire({
        title: 'Generate Gaji?',
        text: 'Proses ini akan menghitung ulang gaji semua pegawai untuk periode ini.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Generate!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#50cd89',
        cancelButtonColor: '#f1416c',
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu, sedang menghitung gaji pegawai.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('admin/gaji/periode/generate') }}/" + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    $('#example').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    Swal.fire({
                        title: 'Gagal!',
                        text: msg,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
