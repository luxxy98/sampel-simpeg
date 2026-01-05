@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Gaji</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">Tarif Lembur</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">
            <div class="card mb-xl-8 mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Tarif Lembur</span>
                        <span class="text-muted fs-7">Kelola tarif lembur per jam</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                                data-bs-toggle="modal" data-bs-target="#modal_create">
                            <i class="bi bi-plus"></i> Tambah Tarif
                        </button>
                    </div>
                </div>

                <div class="card-body p-5">
                    <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                                <th class="min-w-75px ps-5">Aksi</th>
                                <th class="min-w-150px">Nama Tarif</th>
                                <th class="min-w-150px text-end">Tarif Per Jam</th>
                                <th class="min-w-200px">Keterangan</th>
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
                    <h5 class="modal-title fw-bolder">Tambah Tarif Lembur</h5>
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
                            <label class="form-label fw-bolder">Nama Tarif <span class="text-danger">*</span></label>
                            <input type="text" name="nama_tarif" class="form-control form-control-sm" placeholder="Contoh: Lembur Biasa" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Tarif Per Jam (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="tarif_per_jam" class="form-control form-control-sm" min="0" placeholder="50000" required>
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
                    <h5 class="modal-title fw-bolder">Edit Tarif Lembur</h5>
                    <button type="button" class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="form_edit">
                    <input type="hidden" id="edit_id" name="id_tarif">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Nama Tarif <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_tarif" name="nama_tarif" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bolder">Tarif Per Jam (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="edit_tarif_per_jam" name="tarif_per_jam" class="form-control form-control-sm" min="0" required>
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
        ajax: '{{ route("admin.gaji.tarif-lembur.list") }}',
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'nama_tarif' },
            { data: 'tarif_per_jam', className: 'text-end' },
            { data: 'keterangan' },
        ]
    });

    function openEditTarifLembur(data) {
        $('#edit_id').val(data.id_tarif);
        $('#edit_nama_tarif').val(data.nama_tarif);
        $('#edit_tarif_per_jam').val(data.tarif_per_jam);
        $('#edit_keterangan').val(data.keterangan);
        $('#modal_edit').modal('show');
    }

    function deleteTarifLembur(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data tarif lembur akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (!r.value) return;
            DataManager.openLoading();
            DataManager.deleteData('{{ route("admin.gaji.tarif-lembur.destroy", ["id" => "___"]) }}'.replace('___', id))
                .then(res => {
                    Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                    if (res.success) table.ajax.reload();
                }).catch(e => ErrorHandler.handleError(e));
        });
    }

    $('#form_create').on('submit', function(e) {
        e.preventDefault();
        DataManager.openLoading();
        DataManager.formData('{{ route("admin.gaji.tarif-lembur.store") }}', new FormData(this))
            .then(res => {
                Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                if (res.success) { $('#modal_create').modal('hide'); table.ajax.reload(); this.reset(); }
            }).catch(e => ErrorHandler.handleError(e));
    });

    $('#form_edit').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        DataManager.openLoading();
        DataManager.formData('{{ route("admin.gaji.tarif-lembur.update", ["id" => "___"]) }}'.replace('___', id), new FormData(this))
            .then(res => {
                Swal.fire(res.success ? 'Success' : 'Error', res.message, res.success ? 'success' : 'error');
                if (res.success) { $('#modal_edit').modal('hide'); table.ajax.reload(); }
            }).catch(e => ErrorHandler.handleError(e));
    });
</script>
@endsection
