<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan rapat</title>

    <style>
        @page {
            margin-top: 1px;
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
            margin-bottom: 5px;
            text-align: center;
        }

        .content {
            width: 100%;
            margin: 20px;
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
        }

        .header2 td:first-child {
            width: 1%;
            text-align: left;
            padding-right: 10px;
        }

        .fill {
            margin-top: 1px;
            width: 95%;
            margin: 0 auto;
        }

        .fill p {
            text-align: left;
        }

        .fill table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            background-color: white;
            margin-left: 20px;
        }

        .fill table td {
            border: none;
            text-align: left;
            padding: 0;
            vertical-align: top;
        }

        .fill table tr td:first-child {
            width: 15%;
        }

        .fill table tr td:nth-child(2) {
            width: 3%;
            text-align: center;
        }

        .fill table tr td:nth-child(3) {
            width: 82%;
        }

        .contents {
            text-align: justify;
            line-height: 0.7cm;
        }

        /* PERBAIKAN UTAMA: SIGNATURE PROTECTION */
        .signature-container {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 30px;
            min-height: 120px;
            position: relative;
            clear: both;
        }

        /* PERBAIKAN: Pastikan konten sebelum signature bisa break */
        .content-section {
            page-break-inside: auto;
            overflow: visible;
        }

        /* PERBAIKAN: Agenda cell yang bisa wrap dan break */
        .agenda-cell {
            text-align: justify !important; 
            padding-right: 20px !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            page-break-inside: auto !important;
        }

        /* Force new page jika diperlukan */
        .signature-new-page {
            page-break-before: always !important;
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
            overflow-y: visible !important;
            max-height: none !important;
        }

        .pdf-mode header img,
        .pdf-mode footer img,
        .pdf-mode .content {
            width: 100%;
        }

        .pdf-mode .date {
            text-align: right;
            width: 89%;
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
            overflow-y: visible !important;
            max-height: none !important;
            padding: 0;
            margin-top: 0;
        }

        .date {
            margin-right: 20px;
            margin-top: 10%;
            justify-items: end;
        }

        .header1 tr td {
            line-height: 1.2;
        }

        .header2 h4,
        .header2 p,
        .header2 table td {
            line-height: 1.5;
        }

        .allow-page-break {
            page-break-inside: auto;
        }

        .no-page-break {
            page-break-inside: avoid;
            page-break-before: auto;
        }

        .ttd {
            margin-top: 40px;
            text-align: right;
        }

        /* MEDIA QUERY UNTUK PRINT */
        @media print {
            .signature-container {
                min-height: 100px;
                page-break-inside: avoid;
                margin-top: 20px;
            }
            
            .signature-new-page {
                page-break-before: always;
            }

            .content-section {
                page-break-inside: auto;
            }

            .agenda-cell {
                page-break-inside: auto !important;
            }
        }

        /* CLEAR FLOAT UTILITY */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* PERBAIKAN: QR Code container */
        .qr-container {
            margin: 8px 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .qr-container img {
            max-width: 80px;
            height: auto;
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
        <div class="content; no-page-break ttd">
            <div class="date">
                <p>Madiun, {{ $undangan->tgl_dibuat?->translatedFormat('d F Y') }}</p>
            </div>
            <br>
            <div class="letter">
                <table class="header1">
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td>{{ trim($undangan->nomor_undangan) }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td><b>{{ trim($undangan->judul) }}</b></td>
                    </tr>
                </table>
                <div class="collab">
                    <div class="header2">
                        <h4 style="margin-bottom: 0;"><b>Kepada Yth. :</b></h4>
                        <table style="margin-top: 3px; page-break-inside: auto;">
                            @foreach($tujuanUsers as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $user->position->nm_position ?? '-' }}
                                        {{ $user->bagian_text ?? '-' }}
                                        ({{  $user->firstname }} {{ $user->lastname }})
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <p style="margin-top: 3px;">Di Tempat</p>
                    </div>

                    <div class="fill content-section">
                        <p style="margin-top: 1px;">Dengan hormat,</p>
                        <p class="contents">Bersama ini kami mengharapkan kehadiran Bapak / Ibu pada:</p>
                        <table style="
                        width: 100%;
                        margin-top: 1px;
                        line-height: 1.5;
                        border-collapse: collapse; 
                        background-color: white; 
                        margin-left: 20px; 
                        table-layout: fixed;
                        page-break-inside: auto;
                        ">
                            <tr>
                                <td style="width: 15%;">Hari/Tanggal</td>
                                <td style="width: 3%; text-align: center;">:</td>
                                <td style="width: 82%;">
                                    {{ \Carbon\Carbon::parse($undangan->tgl_rapat)->translatedFormat('l / d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Pukul</td>
                                <td style="text-align: center;">:</td>
                                <td>
                                    {{ $undangan->waktu_mulai }}
                                    @if(preg_match('/^\d{1,2}(\.\d{1,2})?$/', $undangan->waktu_mulai))
                                        WIB
                                    @endif
                                    s.d
                                    {{ $undangan->waktu_selesai ?? 'selesai' }}
                                    @if($undangan->waktu_selesai && preg_match('/^\d{1,2}(\.\d{1,2})?$/', $undangan->waktu_selesai))
                                        WIB
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tempat</td>
                                <td style="text-align: center;">:</td>
                                <td style="text-align: justify; padding-right: 20px;">
                                    {!! nl2br(e($undangan->tempat)) !!}
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Agenda</td>
                                <td style="text-align: center; vertical-align: top;">:</td>
                                <td class="agenda-cell">{{ $cleanTag }}</td>
                            </tr>
                        </table>
                        <p style="margin-top: 10px; text-align: justify;">
                            Demikian undangan ini kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu kami ucapkan
                            terima kasih.
                        </p>
                    </div>

                    {{-- PENGECEKAN APAKAH DIA DIREKTUR --}}
                    @php
                        $bagian = optional($manager->unit)->name_unit
                            ?? optional($manager->section)->name_section
                            ?? optional($manager->department)->name_department
                            ?? optional($manager->divisi)->nm_divisi
                            ?? optional($manager->director)->name_director;

                        $isDirektur = is_null($manager->divisi_id_divisi)
                            && is_null($manager->department_id_department)
                            && is_null($manager->section_id_section)
                            && is_null($manager->unit_id_unit);

                        // DETEKSI OTOMATIS APAKAH PERLU PAGE BREAK
                        $agendaLength = strlen($cleanTag ?? '');
                        $tujuanCount = count($tujuanUsers ?? []);
                        $totalContent = $agendaLength + ($tujuanCount * 60);
                        $needsPageBreak = $totalContent > 1500 || $tujuanCount > 12 || $agendaLength > 800;
                    @endphp

                    {{-- SIGNATURE CONTAINER WITH PAGE BREAK PROTECTION --}}
                    <div class="signature-container {{ $needsPageBreak ? 'signature-new-page' : '' }} clearfix">
                        <div style="width: 40%; float: right; text-align: left; margin-right: 3%; line-height: 1.3; margin-top: 1px;">
                            <p style="text-align: center; margin-bottom: 5px;"><b>Hormat kami,</b></p>
                            {{-- MENAMPILKAN POSISI DARI TABEL POSITION --}}
                            @if($isDirektur)
                                <p style="text-align: center; margin: 0; font-weight: bold;">
                                    {{ optional($manager->director)->name_director }}
                                </p>
                            @else
                                {{-- MENAMPILKAN POSISI DARI TABEL POSITION SERTA ASAL UNIT/SECTION/DEPARTMENT/DIVISI--}}
                                <p style="text-align: center; margin: 0; font-weight: bold;">
                                    {{ preg_replace('/^\([A-Z]+\)\s*/', '', $manager->position->nm_position) }} {{ $bagian }}
                                </p>
                            @endif

                            {{-- QR CODE --}}
                            @if(!empty($undangan->qr_approved_by))
                                <div class="qr-container">
                                    <img src="data:image/png;base64,{{ $undangan->qr_approved_by }}" alt="QR Code">
                                </div>
                            @endif
                            {{-- NAMA BERTANDA TANGAN --}}
                            <p style="margin: 0; text-align: center;"><b><u>{{ $manager->firstname }}
                                        {{ $manager->lastname }}</u></b></p>
                        </div>
                    </div>

                </div> {{-- Close collab --}}
            </div> {{-- Close letter --}}
        </div> {{-- Close content --}}
    </main>
</body>

</html>