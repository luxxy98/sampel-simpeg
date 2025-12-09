@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Penggajian</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-200 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">Periode Gaji</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">
            <div class="card mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Periode Penggajian</span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                                    data-bs-toggle="modal" data-bs-target="#form_create_periode"
                                    title="Tambah Periode">
                                Tambah Periode
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    <div
                        class="notice d-flex border-primary border-1 border-dashed p-4 rounded shadow bg-hover-light-dark mb-5">
                        <div class="d-flex flex-stack fs-sm-8 fs-lg-6">
                            <div class="row">
                                <span class="text-gray-700">
                                    Periode gaji berisi rentang tanggal, status proses penggajian,
                                    dan tanggal penggajian untuk setiap bulan.
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="table-responsive mb-8 shadow border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        <div class="table-responsive">
                            <table id="table-periode-gaji"
                                   class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                                <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                                    <th class="min-w-75px ps-5">Aksi</th>
                                    <th class="min-w-80px">Tahun</th>
                                    <th class="min-w-80px">Bulan</th>
                                    <th class="min-w-150px">Tanggal Mulai</th>
                                    <th class="min-w-150px">Tanggal Selesai</th>
                                    <th class="min-w-120px">Status</th>
                                    <th class="min-w-140px">Status Peninjauan</th>
                                    <th class="min-w-150px">Tanggal Penggajian</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 fw-bolder fs-sm-8 fs-lg-6">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> {{-- card-body --}}
            </div>
        </div>
    </div>

    @include('admin.gaji.periode.view.create')
    @include('admin.gaji.periode.view.edit')
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

    @include('admin.gaji.periode.script.list')
    @include('admin.gaji.periode.script.create')
    @include('admin.gaji.periode.script.edit')
@endsection