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
    <li class="breadcrumb-item text-dark">Master Jadwal Kerja</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="content flex-column-fluid">
            <div class="card mb-xl-8 mb-5 border-2 shadow">
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder mb-1">Master Jadwal Kerja</span>
                        <span class="text-muted fs-7">Pengaturan shift / jam kerja</span>
                    </h3>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-primary fs-sm-8 fs-lg-6"
                                    data-bs-toggle="modal"
                                    data-bs-target="#form_create">
                                <i class="bi bi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    {{-- LIST / TABLE --}}
                    <div class="table-responsive shadow p-4 mx-0 border-hover-dark border-primary border-1 border-dashed fs-sm-8 fs-lg-6 rounded-2">
                        @include('admin.absensi.jadwal_kerja.view.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @include('admin.absensi.jadwal_kerja.view.create')
    @include('admin.absensi.jadwal_kerja.view.edit')
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>

    @include('admin.absensi.jadwal_kerja.script.list')
    @include('admin.absensi.jadwal_kerja.script.create')
    @include('admin.absensi.jadwal_kerja.script.edit')
    @include('admin.absensi.jadwal_kerja.script.delete')
@endsection
