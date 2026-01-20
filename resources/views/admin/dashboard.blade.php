@extends('admin.layouts.index')

@section('css')
    <style>
        .kpi-card { transition: transform .15s ease, box-shadow .15s ease; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1.25rem rgba(0,0,0,.08) !important; }
        .kpi-icon {
            width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px;
        }
    </style>
@endsection

@section('list')
    <li class="breadcrumb-item text-muted">Dashboard</li>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex flex-wrap flex-stack mb-7">
            <div class="d-flex flex-column">
                <span class="text-gray-800 fw-bolder fs-2">
                    Halo, {{ $admin->nama ?? 'Admin' }}
                </span>
                <span class="text-gray-500 fw-semibold">
                    Ringkasan sistem hari ini — {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="{{ route('admin.person.index') }}" class="btn btn-sm btn-light-primary">
                    Kelola Person
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="btn btn-sm btn-light-success">
                    Input/Monitor Absensi
                </a>
                <a href="{{ route('admin.cuti.pengajuan.index') }}" class="btn btn-sm btn-light-warning">
                    Review Cuti
                </a>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-5 mb-7">
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card kpi-card border-2 shadow">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-gray-500 fw-semibold">Total Person</div>
                            <div class="text-gray-900 fw-bolder fs-2">{{ number_format($personTotal) }}</div>
                            <a href="{{ route('admin.person.index') }}" class="text-primary fw-semibold">Lihat data</a>
                        </div>
                        <div class="kpi-icon bg-light-primary">
                            <span class="bi bi-people fs-2 text-primary"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card kpi-card border-2 shadow">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-gray-500 fw-semibold">Total SDM</div>
                            <div class="text-gray-900 fw-bolder fs-2">{{ number_format($sdmTotal) }}</div>
                            <a href="{{ route('admin.sdm.sdm.index') }}" class="text-primary fw-semibold">Kelola SDM</a>
                        </div>
                        <div class="kpi-icon bg-light-info">
                            <span class="bi bi-person-badge fs-2 text-info"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card kpi-card border-2 shadow">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-gray-500 fw-semibold">Absensi Hari Ini</div>
                            <div class="text-gray-900 fw-bolder fs-2">{{ number_format($absensiToday) }}</div>
                            <a href="{{ route('admin.absensi.index') }}" class="text-primary fw-semibold">Buka absensi</a>
                        </div>
                        <div class="kpi-icon bg-light-success">
                            <span class="bi bi-calendar-check fs-2 text-success"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card kpi-card border-2 shadow">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-gray-500 fw-semibold">Pending Cuti / SPPD</div>
                            <div class="text-gray-900 fw-bolder fs-2">
                                {{ number_format($cutiPending) }} / {{ number_format($sppdPending) }}
                            </div>
                            <div class="d-flex gap-3">
                                <a href="{{ route('admin.cuti.pengajuan.index') }}" class="text-warning fw-semibold">Cuti</a>
                                <a href="{{ route('admin.sppd.index') }}" class="text-primary fw-semibold">SPPD</a>
                            </div>
                        </div>
                        <div class="kpi-icon bg-light-warning">
                            <span class="bi bi-inboxes fs-2 text-warning"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts + Summary --}}
        <div class="row g-5 mb-7">
            <div class="col-12 col-xxl-8">
                <div class="card border-2 shadow">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-bolder">Tren Absensi (14 hari)</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart_absensi" style="height: 300px;"></div>
                        @if(empty($absensiLabels) || empty($absensiSeries))
                            <div class="text-gray-500 mt-3">
                                Data absensi belum tersedia / tabel <code>absensi</code> belum ada.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-4">
                <div class="card border-2 shadow h-100">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-bolder">Status Cuti</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart_cuti" style="height: 300px;"></div>
                        @if(empty($cutiLabels) || empty($cutiSeries))
                            <div class="text-gray-500 mt-3">
                                Data cuti belum tersedia / tabel <code>cuti_pengajuan</code> belum ada.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Gaji + Aktivitas --}}
        <div class="row g-5">
            <div class="col-12 col-xxl-5">
                <div class="card border-2 shadow">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-bolder">Ringkasan Gaji (Periode Terakhir)</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500">Periode</span>
                                <span class="fw-bolder">{{ $gajiPeriodeLabel }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500">Jumlah Transaksi</span>
                                <span class="fw-bolder">{{ number_format($gajiTrxCount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500">Total THP</span>
                                <span class="fw-bolder">
                                    Rp {{ number_format($gajiThpSum, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="separator my-2"></div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.gaji.periode.index') }}" class="btn btn-sm btn-light-primary">Periode Gaji</a>
                                <a href="{{ route('admin.gaji.trx.index') }}" class="btn btn-sm btn-light-info">Transaksi</a>
                                <a href="{{ route('admin.gaji.distribusi.index') }}" class="btn btn-sm btn-light-success">Distribusi</a>
                            </div>

                            @if($gajiPeriodeLabel === '-')
                                <div class="text-gray-500">
                                    Data gaji belum tersedia / DB <code>simpeg_absensigaji</code> belum siap.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-7">
                <div class="card border-2 shadow">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="fw-bolder">Aktivitas Terakhir</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($recentAudits) && $recentAudits->count())
                            <div class="table-responsive">
                                <table class="table table-sm align-middle table-row-bordered">
                                    <thead class="text-gray-400 fw-bold text-uppercase fs-7">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Event</th>
                                            <th>Objek</th>
                                            <th>ID</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-800">
                                        @foreach($recentAudits as $a)
                                            <tr>
                                                <td class="text-gray-600">{{ \Carbon\Carbon::parse($a->created_at)->translatedFormat('d M H:i') }}</td>
                                                <td><span class="badge badge-light">{{ $a->event }}</span></td>
                                                <td class="text-gray-600">{{ class_basename($a->auditable_type) }}</td>
                                                <td class="text-gray-600">{{ $a->auditable_id }}</td>
                                                <td class="text-gray-600">{{ $a->user_id }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-gray-500">
                                Belum ada data aktivitas (tabel <code>audits</code> belum ada / kosong).
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('javascript')
    <script>
        (function () {
            // === Absensi Chart (ApexCharts sudah ada di plugins.bundle.js) ===
            const absensiLabels = @json($absensiLabels ?? []);
            const absensiSeries = @json($absensiSeries ?? []);

            if (absensiLabels.length && absensiSeries.length && typeof ApexCharts !== 'undefined') {
                const el = document.querySelector('#chart_absensi');
                const chart = new ApexCharts(el, {
                    chart: { type: 'area', height: 300, toolbar: { show: false } },
                    series: [{ name: 'Absensi', data: absensiSeries }],
                    xaxis: { categories: absensiLabels },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth' },
                    grid: { strokeDashArray: 4 },
                });
                chart.render();
            }

            // === Cuti Donut ===
            const cutiLabels = @json($cutiLabels ?? []);
            const cutiSeries = @json($cutiSeries ?? []);

            if (cutiLabels.length && cutiSeries.length && typeof ApexCharts !== 'undefined') {
                const el = document.querySelector('#chart_cuti');
                const chart = new ApexCharts(el, {
                    chart: { type: 'donut', height: 300 },
                    labels: cutiLabels,
                    series: cutiSeries,
                    legend: { position: 'bottom' }
                });
                chart.render();
            }
        })();
    </script>
@endsection
