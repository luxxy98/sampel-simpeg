<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak SPPD</title>

    <style>
        @page { size: A4; margin: 15mm; }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #efefef; /* background luar kertas */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 12px;
        }

        /* ======= Tampilan kertas A4 di tengah ======= */
        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 18px auto;           /* jarak dari atas layar */
            background: #fff;            /* kertas */
            box-shadow: 0 10px 30px rgba(0,0,0,.12);
            border-radius: 10px;
            overflow: hidden;
        }
        .content {
            padding: 16mm 15mm;          /* padding dalam kertas */
        }

        /* Saat print: hilangkan background & shadow */
        @media print {
            body { background: #fff; }
            .paper { margin: 0; box-shadow: none; border-radius: 0; }
            .toolbar { display: none !important; }
        }

        /* ======= Toolbar ======= */
        .toolbar {
            width: 210mm;
            margin: 18px auto 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .btn {
            border: 1px solid #111;
            background: #fff;
            padding: 8px 10px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn:hover { background: #f7f7f7; }
        .muted { color: #555; font-size: 11px; }

        /* ======= KOP / Header tanpa logo ======= */
        .kop {
            text-align: center;
            line-height: 1.25;
        }
        .kop .instansi {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .kop .unit {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .kop .addr {
            font-size: 10px;
            margin-top: 4px;
            color: #444;
        }
        .line-thick {
            border: 0;
            border-top: 3px solid #111;
            margin: 10px 0 0 0;
        }

        /* ======= Judul ======= */
        .title {
            margin-top: 12px;
            text-align: center;
        }
        .title h1 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            text-decoration: underline;
            letter-spacing: .2px;
        }
        .title .no {
            margin-top: 6px;
            font-size: 11px;
        }

        /* ======= Box ======= */
        .card {
            border: 1px solid #111;
            border-radius: 10px;
            padding: 12px 14px;
        }

        /* ======= Table ======= */
        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 5px 6px; vertical-align: top; }
        .meta td.label { width: 210px; }
        .meta td.sep { width: 10px; }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            margin: 0 0 8px 0;
        }
        .desc { line-height: 1.5; white-space: pre-line; }

        .biaya th, .biaya td {
            border: 1px solid #111;
            padding: 7px 8px;
        }
        .biaya th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .3px;
            background: #f3f3f3;
        }
        .biaya td.amount { text-align: right; white-space: nowrap; }
        .biaya tfoot td { font-weight: 800; background: #fafafa; }

        /* ======= Tanda tangan ======= */
        .sign-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 18px;
        }
        .sign-box {
            border: 1px solid #111;
            border-radius: 10px;
            padding: 10px 12px;
            min-height: 150px;
        }
        .sign-box .role {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .3px;
        }
        .sign-box .place-date { margin-top: 6px; font-size: 11px; color:#444; }
        .ttd-space { height: 72px; }
        .name-line {
            border-top: 1px solid #111;
            margin-top: 6px;
            padding-top: 6px;
            font-weight: 700;
            text-align: center;
        }

        .footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 10px;
            color: #444;
        }

        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
    </style>
</head>

<body>
@php
    $instansi = config('app.name', 'INSTANSI / PERUSAHAHAAN');
    $unit = 'SURAT PERJALANAN DINAS';

    $fmtDate = function ($v) { return $v ?: '-'; };
    $rupiah = function ($v) { return number_format((int)($v ?? 0), 0, ',', '.'); };
@endphp

<div class="toolbar">
    <div class="muted">Preview A4 • Saat print hasil mengikuti margin A4</div>
    <div style="display:flex; gap:8px;">
        <button class="btn" onclick="window.print()">Print</button>
        <button class="btn" onclick="window.close()">Close</button>
    </div>
</div>

<div class="paper">
    <div class="content">

        {{-- KOP TANPA LOGO --}}
        <div class="kop">
            <div class="instansi">{{ $instansi }}</div>
            <div class="unit">{{ $unit }}</div>
            <div class="addr">Dokumen diterbitkan melalui sistem SIMPEG</div>
        </div>
        <hr class="line-thick">

        {{-- JUDUL --}}
        <div class="title">
            <h1>SURAT PERJALANAN DINAS (SPPD)</h1>
            <div class="no">Nomor: <span class="fw-semibold">{{ $data->nomor_surat ?? '-' }}</span></div>
        </div>

        {{-- RINGKASAN --}}
        <div class="card mt-16">
            <table class="meta">
                <tr>
                    <td class="label fw-semibold">Nama Pegawai</td><td class="sep">:</td>
                    <td class="fw-bold">{{ $data->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Tanggal Surat</td><td class="sep">:</td>
                    <td>{{ $fmtDate($data->tanggal_surat ?? null) }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Tanggal Berangkat</td><td class="sep">:</td>
                    <td>{{ $fmtDate($data->tanggal_berangkat ?? null) }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Tanggal Pulang</td><td class="sep">:</td>
                    <td>{{ $fmtDate($data->tanggal_pulang ?? null) }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Tujuan</td><td class="sep">:</td>
                    <td>{{ $data->tujuan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Instansi Tujuan</td><td class="sep">:</td>
                    <td>{{ $data->instansi_tujuan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label fw-semibold">Moda Transportasi</td><td class="sep">:</td>
                    <td>{{ $data->transportasi ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- MAKSUD TUGAS --}}
        <div class="card mt-12">
            <div class="section-title">MAKSUD / TUJUAN PERJALANAN DINAS</div>
            <div class="desc">{{ $data->maksud_tugas ?? '-' }}</div>
        </div>

        {{-- RINCIAN BIAYA --}}
        <div class="mt-12">
            <div class="section-title">RINCIAN BIAYA (RINGKAS)</div>
            <table class="biaya">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Komponen</th>
                        <th style="width: 170px;" class="text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>Biaya Transport</td>
                        <td class="amount">{{ $rupiah($data->biaya_transport ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>Biaya Penginapan</td>
                        <td class="amount">{{ $rupiah($data->biaya_penginapan ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>Uang Harian</td>
                        <td class="amount">{{ $rupiah($data->uang_harian ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td>Biaya Lainnya</td>
                        <td class="amount">{{ $rupiah($data->biaya_lainnya ?? 0) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right">TOTAL</td>
                        <td class="amount">{{ $rupiah($data->total_biaya ?? 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- CATATAN --}}
        @if(!empty($data->catatan))
            <div class="card mt-12">
                <div class="section-title">CATATAN</div>
                <div class="desc">{{ $data->catatan }}</div>
            </div>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="sign-wrap">
            <div class="sign-box">
                <div class="role">Yang Melaksanakan</div>
                <div class="place-date">Tanda tangan & nama jelas</div>
                <div class="ttd-space"></div>
                <div class="name-line">{{ $data->nama ?? '(____________________)' }}</div>
            </div>

            <div class="sign-box">
                <div class="role">Pejabat Pemberi Tugas</div>
                <div class="place-date">{{ $fmtDate($data->tanggal_surat ?? null) }}</div>
                <div class="ttd-space"></div>
                <div class="name-line">(____________________)</div>
            </div>
        </div>

        <div class="footer">
            <div>Dicetak: {{ date('Y-m-d H:i') }} WIB</div>
            <div>SPD • {{ $data->nomor_surat ?? '-' }}</div>
        </div>

    </div>
</div>

</body>
</html>
