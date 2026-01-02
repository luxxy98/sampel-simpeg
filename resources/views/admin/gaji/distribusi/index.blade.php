@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Gaji</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">Distribusi Transfer</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card mb-xl-8 mb-5 border-2 shadow">
            <div class="card-header">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder mb-1">Distribusi Transfer Gaji</span>
                    <span class="text-muted fs-7">Status transfer per periode / SDM / rekening</span>
                </h3>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary"
                                data-bs-toggle="modal" data-bs-target="#modal_create">
                            + Tambah Distribusi
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                {{-- FILTER --}}
                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="text-primary fw-bold border-bottom border-primary pb-2 mb-4">
                            Filter Data
                        </h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bolder mb-1">Periode</label>
                        <select id="filter_id_periode" class="form-select form-select-sm"
                                data-control="select2" data-placeholder="Semua Periode" data-allow-clear="true">
                            <option></option>
                            @isset($periodeOptions)
                                @foreach($periodeOptions as $p)
                                    <option value="{{ $p['id_periode'] }}">
                                        {{ ($p['tahun'] ?? '') . '-' . str_pad(($p['bulan'] ?? 0), 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bolder mb-1">Status Transfer</label>
                        <select id="filter_status" class="form-select form-select-sm"
                                data-control="select2" data-placeholder="Semua Status" data-allow-clear="true">
                            <option></option>
                            <option value="PENDING">PENDING</option>
                            <option value="SUCCESS">SUCCESS</option>
                            <option value="FAILED">FAILED</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button class="btn btn-sm btn-light-primary w-100" id="btn_filter_reload">
                            Terapkan
                        </button>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                    <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                        <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-75px ps-5">Aksi</th>
                            <th class="min-w-200px">Periode</th>
                            <th class="min-w-240px">SDM</th>
                            <th class="min-w-240px">Rekening</th>
                            <th class="min-w-170px">Jumlah Transfer</th>
                            <th class="min-w-140px">Status</th>
                            <th class="min-w-190px">Tanggal Transfer</th>
                            <th class="min-w-260px">Catatan</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 fw-bolder"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.gaji.distribusi.view.create')
    @include('admin.gaji.distribusi.view.edit')
    @include('admin.gaji.distribusi.view.detail')
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/lodash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/print.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>

    @include('admin.gaji.distribusi.script.list')
    @include('admin.gaji.distribusi.script.create')
    @include('admin.gaji.distribusi.script.edit')
    @include('admin.gaji.distribusi.script.detail')
    @include('admin.gaji.distribusi.script.delete')
@endsection
