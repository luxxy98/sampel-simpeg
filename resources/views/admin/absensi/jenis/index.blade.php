@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Absensi</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">Jenis Absen</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card mb-xl-8 mb-5 border-2 shadow">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="fw-bolder">Master Jenis Absen</span>
                </h3>
                <div class="card-toolbar">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#form_create">
                        Tambah Jenis
                    </button>
                </div>
            </div>

            <div class="card-body p-5">
                <div class="table-responsive mb-8 shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                    <table id="example" class="table table-sm align-middle table-row-bordered table-row-solid gs-0 gy-2">
                        <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0 fs-sm-8 fs-lg-6">
                            <th class="min-w-75px ps-5">Aksi</th>
                            <th class="min-w-200px">Nama Absen</th>
                            <th class="min-w-140px">Kategori</th>
                            <th class="min-w-120px">Potong Gaji</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 fw-bolder fs-sm-8 fs-lg-6"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.absensi.jenis.view.create')
    @include('admin.absensi.jenis.view.edit')
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

    @include('admin.absensi.jenis.script.list')
    @include('admin.absensi.jenis.script.create')
    @include('admin.absensi.jenis.script.edit')
    @include('admin.absensi.jenis.script.delete')
@endsection
