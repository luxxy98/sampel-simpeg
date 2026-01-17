<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak SPPD</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        .wrap { width: 800px; margin: 0 auto; }
        .title { text-align:center; margin-bottom: 12px; }
        .title h2 { margin: 0; font-size: 16px; text-decoration: underline; }
        .title .no { margin-top: 6px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 6px 4px; vertical-align: top; }
        .box { border: 1px solid #111; padding: 10px; margin-top: 10px; }
        .sign { margin-top: 30px; width: 100%; }
        .sign td { padding: 6px 4px; vertical-align: top; }
        .right { text-align: right; }
        @media print { .noprint { display:none; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="noprint" style="margin: 10px 0;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="title">
        <h2>SURAT PERJALANAN DINAS (SPPD)</h2>
        <div class="no">
            Nomor: {{ $data->nomor_surat ?? '-' }}
        </div>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 180px;">Nama Pegawai</td>
            <td style="width: 10px;">:</td>
            <td>{{ $data->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Surat</td>
            <td>:</td>
            <td>{{ $data->tanggal_surat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Berangkat</td>
            <td>:</td>
            <td>{{ $data->tanggal_berangkat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Pulang</td>
            <td>:</td>
            <td>{{ $data->tanggal_pulang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>:</td>
            <td>{{ $data->tujuan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Instansi Tujuan</td>
            <td>:</td>
            <td>{{ $data->instansi_tujuan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Transportasi</td>
            <td>:</td>
            <td>{{ $data->transportasi ?? '-' }}</td>
        </tr>
    </table>

    <div class="box">
        <div style="font-weight: bold; margin-bottom: 6px;">Maksud Tugas</div>
        <div>{{ $data->maksud_tugas ?? '-' }}</div>
    </div>

    <div class="box">
        <div style="font-weight: bold; margin-bottom: 6px;">Rincian Biaya (Ringkas)</div>
        <table>
            <tr><td style="width: 180px;">Biaya Transport</td><td style="width:10px;">:</td><td class="right">{{ number_format((int)($data->biaya_transport ?? 0), 0, ',', '.') }}</td></tr>
            <tr><td>Biaya Penginapan</td><td>:</td><td class="right">{{ number_format((int)($data->biaya_penginapan ?? 0), 0, ',', '.') }}</td></tr>
            <tr><td>Uang Harian</td><td>:</td><td class="right">{{ number_format((int)($data->uang_harian ?? 0), 0, ',', '.') }}</td></tr>
            <tr><td>Biaya Lainnya</td><td>:</td><td class="right">{{ number_format((int)($data->biaya_lainnya ?? 0), 0, ',', '.') }}</td></tr>
            <tr><td style="font-weight:bold;">Total</td><td>:</td><td class="right" style="font-weight:bold;">{{ number_format((int)($data->total_biaya ?? 0), 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <table class="sign">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div style="text-align:center;">
                    {{ date('Y-m-d') }}<br>
                    Menyetujui,<br><br><br><br>
                    ( ____________________ )
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
