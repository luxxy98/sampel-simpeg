@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Pegawai</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">SPPD</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">

            <div class="card mb-xl-8 mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Data SPPD</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end">
                            <a type="button" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                               data-bs-toggle="modal" data-bs-target="#form_create" title="Tambah SPPD">
                                Tambah SPPD
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">

                    <div class="notice d-flex border-primary mb-4 rounded border border-dashed p-4 shadow bg-hover-light-dark">
                        <div class="d-flex flex-stack fs-sm-8 fs-lg-6 w-100">
                            <div class="row w-100 g-2">
                                <div class="col-md-3">
                                    <label class="fw-bolder mb-1">Status</label>
                                    <select id="filter_status" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Semua status">
                                        <option></option>
                                        <option value="draft">Draft</option>
                                        <option value="diajukan">Diajukan</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bolder mb-1">Pegawai (SDM)</label>
                                    <select id="filter_id_sdm" class="form-select form-select-sm fs-sm-8 fs-lg-6" data-control="select2" data-placeholder="Semua pegawai">
                                        <option></option>
                                        @foreach($sdmOptions as $r)
                                            <option value="{{ $r['id_sdm'] }}">{{ $r['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mt-3 d-flex gap-2">
                                    <button type="button" id="btn_filter" class="btn btn-sm btn-dark fs-sm-8 fs-lg-6">Terapkan</button>
                                    <button type="button" id="btn_reset" class="btn btn-sm btn-secondary fs-sm-8 fs-lg-6">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                                <th class="min-w-110px ps-5">Aksi</th>
                                <th class="min-w-220px">Nama</th>
                                <th class="min-w-140px">Nomor Surat</th>
                                <th class="min-w-110px">Berangkat</th>
                                <th class="min-w-110px">Pulang</th>
                                <th class="min-w-200px">Tujuan</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-140px">Total Biaya</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-800 fw-bolder fs-sm-8 fs-lg-6"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @include('admin.sppd.view.detail')
    @include('admin.sppd.view.create')
    @include('admin.sppd.view.edit')
    @include('admin.sppd.view.approve')
    @include('admin.sppd.view.submit')
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

    @include('admin.sppd.script.list')
    @include('admin.sppd.script.create')
    @include('admin.sppd.script.edit')
    @include('admin.sppd.script.detail')
    @include('admin.sppd.script.approve')
    @include('admin.sppd.script.submit')
    @include('admin.sppd.script.delete')
@endsection
