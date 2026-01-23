@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}"/>
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Log</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">Activity Log</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Activity Log (Audit)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table_activity" class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Event</th>
                            <th>Model</th>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>IP</th>
                            <th>URL</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="form_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Activity Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="detail_json" class="bg-light p-3 rounded" style="white-space: pre-wrap;"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>

    <script>
        const listUrl = @json(route('admin.log.activity.list'));
        const showBaseUrl = @json(url('admin/log/activity/show'));

        const table = $('#table_activity').DataTable({
            processing: true,
            serverSide: false,
            ajax: listUrl,
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'event', name: 'event' },
                { data: 'auditable_type', name: 'auditable_type' },
                { data: 'auditable_id', name: 'auditable_id' },
                { data: 'user_id', name: 'user_id' },
                { data: 'ip_address', name: 'ip_address' },
                { data: 'url', name: 'url' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
        });

        // Handle tombol detail dari TransactionService->actionButton()
        $(document).on('click', 'button[data-bs-target="#form_detail"]', function () {
            const id = $(this).data('id');
            $.get(`${showBaseUrl}/${id}`, function (res) {
                $('#detail_json').text(JSON.stringify(res, null, 2));
            }).fail(function (xhr) {
                $('#detail_json').text(xhr.responseText ?? 'Gagal memuat data');
            });
        });
    </script>
@endsection
