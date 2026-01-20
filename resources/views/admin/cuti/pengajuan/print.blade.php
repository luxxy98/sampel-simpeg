@php
    // ===== Header instansi (tanpa logo) =====
    $instansi = config('app.org_name', config('app.name', 'INSTANSI'));
    $unit     = config('app.org_unit', 'UNIT / BAGIAN');
    $alamat   = config('app.org_address', 'Alamat instansi');
    $telp     = config('app.org_phone', null);
    $email    = config('app.org_email', null);
    $kota     = $data->kota_surat ?? config('app.org_city', null);

    // ===== Data cuti =====
    $nama       = $data->nama ?? '-';
    $jenis      = $data->nama_jenis ?? '-';
    $alasan     = $data->alasan ?? '-';
    $approver   = $data->approver_name ?? '____________________';

    $jumlahHari = $data->jumlah_hari ?? '-';

    $tglMulai   = $data->tanggal_mulai ?? null;
    $tglSelesai = $data->tanggal_selesai ?? null;
    $tglAjukan  = $data->tanggal_pengajuan ?? null;
    $tglSetuju  = $data->tanggal_persetujuan ?? null;

    $fmt = function ($v) {
        if (!$v) return '-';
        try { return \Carbon\Carbon::parse($v)->locale('id')->translatedFormat('d F Y'); }
        catch (\Throwable $e) { return (string) $v; }
    };

    $periode = ($tglMulai || $tglSelesai)
        ? trim($fmt($tglMulai) . ' s.d. ' . $fmt($tglSelesai))
        : '-';

    $kontak = trim(implode(' · ', array_filter([
        $telp ? "Telp. {$telp}" : null,
        $email ? $email : null,
    ])));
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Izin Cuti</title>

    <style>
        :root{
            --ink:#111827;
            --muted:#6B7280;
            --soft:#E5E7EB;
            --paper:#ffffff;
            --bg:#f3f4f6;

            /* A4 */
            --a4w:210mm;
            --a4h:297mm;

            /* Margin presisi */
            --mt:20mm;
            --mr:18mm;
            --mb:20mm;
            --ml:22mm;

            /* Jarak tanda tangan (dipendekkan) */
            --sign-gap: 28mm; /* sebelumnya 55mm */
        }

        @page{ size:A4; margin: var(--mt) var(--mr) var(--mb) var(--ml); }

        html, body{ height:100%; }
        body{
            margin:0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.55;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Paper view */
        .toolbar{
            width: var(--a4w);
            margin: 16px auto 0;
            display:flex;
            justify-content:flex-end;
            gap:8px;
        }
        .btn{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            border: 1px solid var(--soft);
            background: #fff;
            padding: 8px 10px;
            border-radius: 10px;
            cursor:pointer;
        }

        .page{
            width: var(--a4w);
            min-height: var(--a4h);
            margin: 14px auto 22px;
            background: var(--paper);
            box-shadow: 0 10px 30px rgba(0,0,0,.12);
            border-radius: 10px;
            box-sizing: border-box;
            padding: var(--mt) var(--mr) var(--mb) var(--ml);
        }

        /* Kop */
        .kop{
            text-align:center;
            border-bottom: 3px double var(--ink);
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop .l1{
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.35px;
            font-size: 12.5pt;
        }
        .kop .l2{
            margin-top:2px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.25px;
            font-size: 12pt;
        }
        .kop .l3{
            margin-top:6px;
            font-size: 10pt;
            color: var(--muted);
        }

        /* Tanggal surat (kanan atas) */
        .date{
            text-align:right;
            margin: 6px 0 8px;
            font-size: 12pt;
        }

        /* Judul */
        .title{
            text-align:center;
            margin: 8px 0 10px;
        }
        .title .t{
            font-size: 13pt;
            font-weight:700;
            text-transform:uppercase;
            text-decoration: underline;
            letter-spacing:.4px;
            margin: 0;
        }

        /* Paragraf */
        .p{
            margin: 10px 0;
            text-align: justify;
            text-indent: 10mm;
        }

        /* Info */
        .info{
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 10px;
            font-size: 12pt;
        }
        .info td{ padding: 3px 0; vertical-align: top; }
        .info .key{ width: 52mm; }
        .info .sep{ width: 6mm; text-align:center; }
        .info .val{ width: auto; }

        /* Alasan */
        .box{
            border: 1px solid var(--soft);
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 6px;
        }
        .box .lbl{ font-weight:700; margin-bottom: 6px; }
        .box .txt{ white-space: pre-wrap; text-align: justify; }

        /* Tanda tangan */
        .sign-wrap{
            margin-top: 14px;
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 18mm;
        }
        .sign{
            text-align:center;
            font-size: 12pt;
        }
        .sign .place-date{ margin-bottom: 8px; }
        .sign .role{ margin-bottom: var(--sign-gap); }
        .sign .name{ font-weight:700; text-decoration: underline; }

        .footer{
            margin-top: 10mm;
            border-top: 1px solid var(--soft);
            padding-top: 6px;
            font-size: 9.5pt;
            color: var(--muted);
            display:flex;
            justify-content: space-between;
            gap: 10px;
        }

        @media print{
            body{ background:#fff; }
            .toolbar{ display:none !important; }
            .page{
                margin:0;
                width:auto;
                min-height:auto;
                border-radius:0;
                box-shadow:none;
                padding:0; /* margin diatur oleh @page */
            }
        }
    </style>
</head>

<body>

<div class="toolbar">
    <button class="btn" onclick="window.print()">Print</button>
    <button class="btn" onclick="window.close()">Close</button>
</div>

<div class="page">

    <div class="kop">
        <div class="l1">{{ $instansi }}</div>
        <div class="l2">{{ $unit }}</div>
        <div class="l3">
            {{ $alamat }}@if($kontak) · {{ $kontak }}@endif
        </div>
    </div>

    <div class="date">
        {{ $kota ? ($kota . ', ') : '' }}{{ $fmt($tglSetuju) }}
    </div>

    <div class="title">
        <div class="t">SURAT IZIN CUTI</div>
    </div>

    <div class="p">
        Yang bertanda tangan di bawah ini memberikan izin cuti kepada pegawai dengan identitas sebagai berikut:
    </div>

    <table class="info">
        <tr><td class="key">Nama</td><td class="sep">:</td><td class="val">{{ $nama }}</td></tr>
        <tr><td class="key">Jenis Cuti</td><td class="sep">:</td><td class="val">{{ $jenis }}</td></tr>
        <tr><td class="key">Periode Cuti</td><td class="sep">:</td><td class="val">{{ $periode }}</td></tr>
        <tr><td class="key">Jumlah Hari</td><td class="sep">:</td><td class="val">{{ $jumlahHari }} hari</td></tr>
        <tr><td class="key">Tanggal Pengajuan</td><td class="sep">:</td><td class="val">{{ $fmt($tglAjukan) }}</td></tr>
        <tr><td class="key">Tanggal Persetujuan</td><td class="sep">:</td><td class="val">{{ $fmt($tglSetuju) }}</td></tr>
    </table>

    <div class="box">
        <div class="lbl">Keterangan</div>
        <div class="txt">{{ $alasan }}</div>
    </div>

    <div class="p" style="margin-top: 12px;">
        Demikian surat izin cuti ini dibuat untuk dipergunakan sebagaimana mestinya.
    </div>

    <div class="sign-wrap">
        <div class="sign">
            <div class="place-date">
                {{ $kota ? ($kota . ', ') : '' }}{{ $fmt($tglAjukan) }}
            </div>
            <div class="role">Pemohon,</div>
            <div class="name">{{ $nama }}</div>
        </div>

        <div class="sign">
            <div class="place-date">
                {{ $kota ? ($kota . ', ') : '' }}{{ $fmt($tglSetuju) }}
            </div>
            <div class="role">Menyetujui,</div>
            <div class="name">{{ $approver }}</div>
        </div>
    </div>

    <div class="footer">
        <div>Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</div>
        <div>{{ $instansi }}</div>
    </div>

</div>
</body>
</html>
