@extends('admin.layouts.index')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/buttons.dataTables.min.css') }}">
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Absensi</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">Jadwal Karyawan</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Jadwal Karyawan</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#form_create">
                    <i class="bi bi-plus"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body">
            @include('admin.absensi.jadwal_karyawan.view.list')
        </div>
    </div>

    @include('admin.absensi.jadwal_karyawan.view.create')
    @include('admin.absensi.jadwal_karyawan.view.edit')
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>

    <script>
        // Options dari controller
        const sdmOptions = @json($sdmOptions ?? []);
        const jadwalOptions = @json($jadwalOptions ?? []);
    </script>

    @include('admin.absensi.jadwal_karyawan.script.list')
    @include('admin.absensi.jadwal_karyawan.script.create')
    @include('admin.absensi.jadwal_karyawan.script.edit')
    @include('admin.absensi.jadwal_karyawan.script.delete')
@endsection
