@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Referensi</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">Hari Libur</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">
            <div class="card mb-xl-8 mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Hari Libur</span>
                        <span class="text-muted fs-7">Kelola data hari libur nasional dan cuti bersama</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                                data-bs-toggle="modal" data-bs-target="#modal_create">
                            <i class="bi bi-plus"></i> Tambah Hari Libur
                        </button>
                    </div>
                </div>

                <div class="card-body p-5">
                    <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                                <th class="min-w-75px ps-5">Aksi</th>
                                <th class="min-w-120px">Tanggal</th>
                                <th class="min-w-200px">Nama</th>
                                <th class="min-w-250px">Keterangan</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-800 fw-bolder fs-sm-8 fs-lg-6"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="modal_create" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder">Tambah Hari Libur</h5>
                    <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="form_create">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Tanggal <span class="text-danger">*</span></label>
                            <input type="text" id="create_tanggal" name="tanggal" class="form-control form-control-sm" placeholder="YYYY-MM-DD" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Nama Hari Libur <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Tahun Baru Imlek" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Keterangan</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="modal_edit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder">Edit Hari Libur</h5>
                    <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="form_edit">
                    <input type="hidden" id="edit_id" name="id_hari_libur">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Tanggal <span class="text-danger">*</span></label>
                            <input type="text" id="edit_tanggal" name="tanggal" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Nama Hari Libur <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama" name="nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Keterangan</label>
                            <textarea id="edit_keterangan" name="keterangan" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>

<script defer>
    let table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.referensi.hari-libur.list") }}',
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'tanggal' },
            { data: 'nama' },
            { data: 'keterangan' },
        ]
    });

    // Initialize flatpickr for date inputs
    $('#modal_create').on('shown.bs.modal', function() {
        $('#create_tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
    });

    $('#modal_edit').on('shown.bs.modal', function() {
        $('#edit_tanggal').flatpickr({ dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
    });

    function openEditHariLibur(data) {
        $('#edit_id').val(data.id_hari_libur);
        $('#edit_tanggal').val(data.tanggal);
        $('#edit_nama').val(data.nama);
        $('#edit_keterangan').val(data.keterangan);
        $('#modal_edit').modal('show');
    }

    function deleteHariLibur(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data hari libur akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (!r.value) return;
            DataManager.openLoading();
            DataManager.deleteData('{{ route("admin.referensi.hari-libur.destroy", ["id" => "___"]) }}'.replace('___', id))
                .then(res => {
                    Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                    if (res.success) table.ajax.reload();
                }).catch(e => ErrorHandler.handleError(e));
        });
    }

    $('#form_create').on('submit', function(e) {
        e.preventDefault();
        DataManager.openLoading();
        DataManager.formData('{{ route("admin.referensi.hari-libur.store") }}', new FormData(this))
            .then(res => {
                Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                if (res.success) { $('#modal_create').modal('hide'); table.ajax.reload(); this.reset(); }
            }).catch(e => ErrorHandler.handleError(e));
    });

    $('#form_edit').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        DataManager.openLoading();
        DataManager.formData('{{ route("admin.referensi.hari-libur.update", ["id" => "___"]) }}'.replace('___', id), new FormData(this))
            .then(res => {
                Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                if (res.success) { $('#modal_edit').modal('hide'); table.ajax.reload(); }
            }).catch(e => ErrorHandler.handleError(e));
    });
</script>
@endsection
