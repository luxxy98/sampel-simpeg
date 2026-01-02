@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Absensi</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">Data Absensi</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">
            <div class="card mb-xl-8 mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Absensi</span>
                        <span class="text-muted fs-7">Harian / per karyawan</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-sm btn-light-primary fs-sm-8 fs-lg-6"
                               href="{{ route('admin.absensi.jenis.index') }}">
                                Master Jenis Absen
                            </a>
                            <button type="button" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                                    data-bs-toggle="modal" data-bs-target="#form_create">
                                Tambah Absensi
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

                        <div class="col-md-3 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Mulai</label>
                            <input type="text" id="filter_tanggal_mulai" class="form-control form-control-sm" placeholder="YYYY-MM-DD">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="fw-bolder mb-1">Tanggal Selesai</label>
                            <input type="text" id="filter_tanggal_selesai" class="form-control form-control-sm" placeholder="YYYY-MM-DD">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bolder mb-1">SDM</label>
                            <select id="filter_id_sdm" class="form-select form-select-sm" data-control="select2" data-placeholder="Semua SDM" data-allow-clear="true">
                                <option></option>
                                @isset($sdmOptions)
                                    @foreach($sdmOptions as $opt)
                                        <option value="{{ $opt['id_sdm'] }}">{{ $opt['nama'] ?? ('SDM #' . $opt['id_sdm']) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-8 mt-1">Opsional: isi dari controller untuk dropdown.</div>
                        </div>

                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button class="btn btn-sm btn-light-primary w-100" id="btn_filter_reload">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                                <th class="min-w-75px ps-5">Aksi</th>
                                <th class="min-w-120px">Tanggal</th>
                                <th class="min-w-220px">SDM</th>
                                <th class="min-w-150px">Jadwal</th>
                                <th class="min-w-130px">Total Jam Kerja</th>
                                <th class="min-w-130px">Terlambat</th>
                                <th class="min-w-130px">Pulang Awal</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-800 fw-bolder fs-sm-8 fs-lg-6"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @include('admin.absensi.view.detail')
    @include('admin.absensi.view.create')
    @include('admin.absensi.view.edit')
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/lodash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.colReorder.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/print.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>

    @include('admin.absensi.script.list')
    @include('admin.absensi.script.create')
    @include('admin.absensi.script.edit')
    @include('admin.absensi.script.detail')
    @include('admin.absensi.script.delete')
@endsection
