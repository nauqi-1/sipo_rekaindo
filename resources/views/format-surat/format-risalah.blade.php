<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Risalah {{ $risalah->nomor_risalah }} </title>
    <style>
        @page {
            size: A4;
            margin: 30mm 15mm 30mm 15mm;
            header: pageHeader;
            footer: pageFooter;
        }

        div.pageHeader {
            position: fixed;
            top: -25mm;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
        }

        div.pageFooter {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
        }

        body {
            font-family: Verdana, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .fill {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
        }

        .fill th, .fill td {
            border: 1.5px solid black;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        .fill th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .page-break {
            page-break-before: always;
        }

        .signature {
            margin-top: 20mm;
            text-align: right;
            margin-right: 50px;
        }

        .nowrap {
            white-space: nowrap;
        }

        td ol {
            margin: 0;
            padding-left: 16px;
        }
    </style>
</head>
<body>
    <div class="pageHeader">
        <img src="{{ public_path('img/bheader.png') }}" style="width: 100%;">
    </div>

    <div class="container">
        <div class="title">
            <h5><u>Risalah Rapat</u><br>No: {{ $risalah->nomor_risalah }}</h5>
        </div>

        <div class="letter">
            <table style="font-size: 12px; margin-bottom: 20px;">
                <tr><td style="width: 100px;">Hari, tanggal</td><td style="width: 10px;">:</td><td>{{ $risalah->tgl_dibuat->translatedFormat('d F Y') }}</td></tr>
                <tr><td>Tempat</td><td>:</td><td>{{ $risalah->tempat }}</td></tr>
                <tr><td>Waktu</td><td>:</td><td>{{ $risalah->waktu_mulai }} s/d {{ $risalah->waktu_selesai }}</td></tr>
                <tr><td>Agenda</td><td>:</td><td>{{ $risalah->agenda }}</td></tr>
                <tr><td>Daftar Hadir</td><td>:</td><td>Daftar Hadir Seperti Yang Sudah Terlampir.</td></tr>
            </table>
        </div>

        <table class="fill">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Topik</th>
                    <th style="width: 35%;">Pembahasan</th>
                    <th style="width: 25%;">Tindak Lanjut</th>
                    <th style="width: 20%;">Target</th>
                    <th style="width: 17%;">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($risalah->risalahDetails as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->topik }}</td>
                    <td>
                        @foreach(explode(';', $detail->pembahasan) as $poin)
                            {{ trim($poin) }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach(explode(';', $detail->tindak_lanjut) as $poin)
                            {{ trim($poin) }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach(explode(';', $detail->target) as $poin)
                            {{ trim($poin) }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach(explode(';', $detail->pic) as $poin)
                            {{ trim($poin) }}<br>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature">
            <p>Madiun, {{ $risalah->tgl_dibuat->translatedFormat('d F Y') }}</p>
            <p>Manager {{ $risalah->divisi->nm_divisi }}</p>
            @if(!empty($risalah->qr_approved_by))
                                    <div style="text-align: right; margin-top: 10px; margin-right: 15px;">
                                        <img src="data:image/png;base64,{{ $risalah->qr_approved_by }}" width="100" alt="QR Code">
                                    </div>
                                @endif
            <p>{{ $risalah->nama_bertandatangan }}</p>
        </div>
    </div>

    <div class="pageFooter">
        <img src="{{ public_path('img/bfooter.png') }}" style="width: 100%;">
    </div>
</body>
</html>
