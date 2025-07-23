<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Risalah {{ $risalah->nomor_risalah }} </title>
    <style>
        @page {
            margin-top: 20px;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 0;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
        }

        main {
            margin-top: 5px;
            margin-bottom: 10px;
            text-align: center;
        }

        .content {
            width: 100%;
            margin: auto;
            text-align: center;
        }

        .letter {
            margin-left: 2cm;
            margin-right: 2cm;
            background-color: #ffffff;
            line-height: 0.7cm;
            position: relative;
            z-index: 1;
        }

        .header1 tr td:first-child {
            width: 20%;
        }

        .header2 table {
            margin-top: 15px;
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
            /* Ini biarkan auto jika tidak ingin fixed untuk tabel header2 */
        }

        .header2 th {
            width: 50%;
            border-top: 3px solid black;
            border-bottom: 3px solid black;
            text-align: left;
            font-weight: normal;
            padding: 10px;
            word-wrap: break-word;
            overflow: hidden;
        }

        .header2 th+th {
            border-left: 3px solid black;
        }

        .header2 td {
            padding: 0;
            margin: 0;
            text-align: left;
            white-space: nowrap;
            /* Cegah teks turun ke bawah */
        }

        .header2 td:first-child {
            width: 1%;
            text-align: left;
            padding-right: 10px;
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


        .contents {
            text-align: justify;
            line-height: 0.7cm;
        }

        .signature {
            margin-top: 5%;
            text-align: left !important;
            width: fit-content;
            margin-left: auto;
            margin-right: 3%;
        }

        .signature p {
            text-align: center;
            margin: 0;
        }

        .view-mode header img,
        .view-mode footer img,
        .view-mode .content {
            width: 50%;
            margin: auto;
        }

        .view-mode header,
        .view-mode footer {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: fixed;
            left: 0;
            z-index: 100;
        }

        .view-mode {
            overflow: hidden;
        }

        .view-mode header img {
            display: block;
            margin: 0 auto;
            width: 50%;
        }

        .view-mode .header1 {
            position: fixed;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            background-color: white;
            padding: 0;
            text-align: left;
            z-index: 1000;
        }

        .view-mode .header2 {
            position: relative;
            padding: 0;
            width: 39.5%;
            text-align: left;
        }

        .view-mode .fill {
            position: relative;
            width: 100%;
            /* Sesuaikan dengan kebutuhan layout utama Diva */
            margin-left: auto;
            margin-right: auto;
            text-align: justify;
            padding: 0;
        }

        .view-mode .collab {
            position: relative;
            margin-top: 1cm;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            text-align: justify;
            overflow-y: auto;
            max-height: calc(100vh - 9cm);

        }

        .pdf-mode header img,
        .pdf-mode footer img,
        .pdf-mode .content {
            width: 100%;
        }

        .pdf-mode .date {
            text-align: center;
            width: 100%;
        }

        .pdf-mode .header2 {
            margin-left: 2.5px;
        }

        .pdf-mode .header2 h4,
        .pdf-mode .header2 p {
            text-align: left;
            margin-left: 0;
        }

        .pdf-mode .fill {
            position: relative;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            text-align: justify;
            padding: 0;
            margin-top: 0;
        }

        .pdf-mode .collab {
            position: relative;
            width: 100%;
            margin-left: 2.5px;
            margin-right: auto;
            text-align: justify;
            overflow-y: auto;
            max-height: calc(100vh - 12cm);
            padding: 0;
            margin-top: 0;

        }

        .date {
            margin-top: 10%;
            display: flex;
            justify-content: center; /* posisikan ke tengah secara horizontal */
            text-align: center;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .header1 tr td {
            line-height: 1.2;
        }

        .header2 h4,
        .header2 p,
        .header2 table td {
            line-height: 1.5;
        }

        
    </style>
</head>
<body class="{{ isset($isPdf) && $isPdf ? 'pdf-mode' : 'view-mode' }}">
    <header>
        @if(isset($headerImage))
            <img src="{{ $headerImage }}" width="100%">
        @endif
    </header>

    <footer>
        @if(isset($footerImage))
            <img src="{{ $footerImage }}" width="100%">
        @endif
    </footer>

    <main>
        <div class="content">
            <div class="date">
                <div class="title">
                   <h5><u>Risalah Rapat</u><br>No: {{ $risalah->nomor_risalah }}</h5>
                </div>
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
            <div class="letter">
                <div class="collab">
                        <div class="header2">
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
            <div style="width: 40%; float: right; text-align: left; margin-right: 3%; line-height: 1.3; margin-top: 20px;">
                <p style="text-align: center; margin-bottom: 5px;">Madiun, {{ $risalah->tgl_dibuat->translatedFormat('d F Y') }}</p>
                @php
                    $userBertandatangan = \App\Models\User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$risalah->nama_bertandatangan])->first();
                    $namaJabatan = $userBertandatangan?->position?->nm_position;
                    $namaJabatanBersih = preg_replace('/\([^)]+\)\s*/', '', $namaJabatan);
                @endphp
                <p style="text-align: center; margin: 0;">{{ $namaJabatanBersih }}
                    {{ $userBertandatangan?->department?->name_department 
                        ?? $userBertandatangan?->divisi?->nm_divisi 
                        ?? '-' }}
                </p>
                @if(!empty($risalah->qr_approved_by))
                    <div style="margin: 10px 0; text-align: center;">
                        <img src="data:image/png;base64,{{ $risalah->qr_approved_by }}" width="100" alt="QR Code">
                    </div>
                @endif
                <p style="margin: 0; text-align: center;">{{ $risalah->nama_bertandatangan }}</p>
            </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
