@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Gaji</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">Komponen Gaji</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card mb-xl-8 mb-5 border-2 shadow">
            <div class="card-header">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder mb-1">Komponen Gaji</span>
                    <span class="text-muted fs-7">Master Jenis Komponen & Komponen per Jabatan</span>
                </h3>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light-primary"
                                data-bs-toggle="modal" data-bs-target="#modal_create_jenis">
                            + Jenis Komponen
                        </button>
                        <button type="button" class="btn btn-sm btn-primary"
                                data-bs-toggle="modal" data-bs-target="#modal_create_komponen">
                            + Komponen
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                    <li class="nav-item">
                        <a class="nav-link active fw-bolder" data-bs-toggle="tab" href="#tab_jenis">
                            Jenis Komponen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bolder" data-bs-toggle="tab" href="#tab_komponen">
                            Komponen per Jabatan
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- TAB 1: JENIS KOMPONEN --}}
                    <div class="tab-pane fade show active" id="tab_jenis" role="tabpanel">
                        <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed rounded-2">
                            <table id="table_jenis" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                                <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-75px ps-5">Aksi</th>
                                    <th class="min-w-250px">Nama Komponen</th>
                                    <th class="min-w-160px">Jenis</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 fw-bolder"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: KOMPONEN (PER JABATAN) --}}
                    <div class="tab-pane fade" id="tab_komponen" role="tabpanel">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="fw-bolder mb-1">Filter Jabatan</label>
                                <select id="filter_id_jabatan" class="form-select form-select-sm"
                                        data-control="select2" data-placeholder="Semua Jabatan" data-allow-clear="true">
                                    <option></option>
                                    @isset($jabatanOptions)
                                        @foreach($jabatanOptions as $j)
                                            <option value="{{ $j['id_jabatan'] }}">{{ $j['nama_jabatan'] ?? ('Jabatan #' . $j['id_jabatan']) }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bolder mb-1">Filter Jenis</label>
                                <select id="filter_jenis" class="form-select form-select-sm"
                                        data-control="select2" data-placeholder="Semua Jenis" data-allow-clear="true">
                                    <option></option>
                                    <option value="PENGHASILAN">PENGHASILAN</option>
                                    <option value="POTONGAN">POTONGAN</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-sm btn-light-primary w-100" id="btn_filter_komponen">
                                    Terapkan
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed rounded-2">
                            <table id="table_komponen" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                                <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-75px ps-5">Aksi</th>
                                    <th class="min-w-240px">Jabatan</th>
                                    <th class="min-w-260px">Komponen</th>
                                    <th class="min-w-160px">Jenis</th>
                                    <th class="min-w-160px">Nominal</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 fw-bolder"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @include('admin.gaji.komponen.view.create_jenis')
    @include('admin.gaji.komponen.view.edit_jenis')
    @include('admin.gaji.komponen.view.create_komponen')
    @include('admin.gaji.komponen.view.edit_komponen')
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

    @include('admin.gaji.komponen.script.list_jenis')
    @include('admin.gaji.komponen.script.create_jenis')
    @include('admin.gaji.komponen.script.edit_jenis')
    @include('admin.gaji.komponen.script.delete_jenis')

    @include('admin.gaji.komponen.script.list_komponen')
    @include('admin.gaji.komponen.script.create_komponen')
    @include('admin.gaji.komponen.script.edit_komponen')
    @include('admin.gaji.komponen.script.delete_komponen')
@endsection
