@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}"/>
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Log</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">Database Log</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Database Query Log</h5>
            <div class="text-muted small">
                Aktifkan logging via <code>DB_QUERY_LOG_ENABLED=true</code> di <code>.env</code>.
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table_db" class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Conn</th>
                            <th>ms</th>
                            <th>Method</th>
                            <th>Route</th>
                            <th>User</th>
                            <th>IP</th>
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
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail DB Query</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="detail_db_json" class="bg-light p-3 rounded" style="white-space: pre-wrap;"></pre>
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
        const listUrl = @json(route('admin.log.database.list'));
        const showBaseUrl = @json(url('admin/log/database/show'));

        const table = $('#table_db').DataTable({
            processing: true,
            serverSide: false,
            ajax: listUrl,
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'connection_name', name: 'connection_name' },
                { data: 'time_ms', name: 'time_ms' },
                { data: 'method', name: 'method' },
                { data: 'route_name', name: 'route_name' },
                { data: 'user_name', name: 'user_name' },
                { data: 'ip_address', name: 'ip_address' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
        });

        $(document).on('click', 'button[data-bs-target="#form_detail"]', function () {
            const id = $(this).data('id');
            $.get(`${showBaseUrl}/${id}`, function (res) {
                $('#detail_db_json').text(JSON.stringify(res, null, 2));
            }).fail(function (xhr) {
                $('#detail_db_json').text(xhr.responseText ?? 'Gagal memuat data');
            });
        });
    </script>
@endsection
