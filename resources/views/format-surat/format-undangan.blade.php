<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan rapat</title>
    
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
            table-layout: auto; /* Ini biarkan auto jika tidak ingin fixed untuk tabel header2 */
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
        .header2 th + th {
            border-left: 3px solid black;
        }
        .header2 td {
            padding: 0;
            margin: 0;
            text-align: left;
            white-space: nowrap; /* Cegah teks turun ke bawah */
        }

        .header2 td:first-child {
            width: 1%;
            text-align: left;
            padding-right: 10px;
        }

        .fill {
            margin-top: 5px;
            width: 95%; /* Atau bisa 100% jika ingin full lebar `letter` */
            margin: 0 auto;
        }
        .fill p {
            text-align: left;
        }
        /* ---- Mulai Perubahan Penting untuk Tabel Detail Undangan ---- */
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

        .view-mode .header1{
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
            width: 100%; /* Sesuaikan dengan kebutuhan layout utama Diva */
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
            overflow-y: auto; 
            max-height: calc(100vh - 12cm); 
            padding: 0;
            margin-top: 0;

        }

        .date {
            margin-right: 2cm;
            margin-top: 15%;
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
                    <table style="margin-top: 3px;">
                        @foreach (explode(';', $undangan->tujuan) as $index => $tujuan)
                        <tr>
                            <td>{{ $index + 1 }}.</td>
                            <td>{{ trim($tujuan) }}</td> 
                        </tr>
                        @endforeach
                    </table>
                    <p style="margin-top: 3px;">Di Tempat</p>
                </div>
                
                <div class="fill">
                    <p>Dengan hormat,</p>
                    <p class="contents">Bersama ini kami mengharapkan kehadiran Bapak / Ibu pada:</p>
                    <table style="
                        width: 100%;
                        margin-top: 10px;
                        line-height: 1.5;
                        border-collapse: collapse; 
                        background-color: white; 
                        margin-left: 20px; 
                        table-layout: fixed; 
                    ">
                        <tr>
                            <td style="width: 15%;">Hari, Tanggal</td> <td style="width: 3%; text-align: center;">:</td> <td style="width: 82%;">
                                {{ \Carbon\Carbon::parse($undangan->tgl_rapat)->translatedFormat('l , d F Y') }}
                            </td> </tr>
                        <tr>
                            <td>Pukul</td>
                            <td style="text-align: center;">:</td>
                            <td>
                                {{ $undangan->waktu_mulai }} s.d {{ $undangan->waktu_selesai ?? 'selesai'  }} 
                            </td>
                        </tr>
                        <tr>
                            <td>Tempat</td>
                            <td style="text-align: center;">:</td>
                            <td>
                                {!! nl2br(e($undangan->tempat)) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>Agenda</td>
                            <td style="text-align: center;">:</td>
                            <td>{!! $undangan->isi_undangan !!}</td>
                        </tr>
                    </table>
                    <p style="margin-top: 10px; text-align: justify;">
                        Demikian undangan ini kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih.
                    </p>
                </div>

                <table class="signature">
                    <tr>
                        <td>
                            <p><b>Hormat kami,</b></p>
                            <b>Manajer {{ $undangan->divisi->nm_divisi }}</b> 
                            @if(!empty($undangan->qr_approved_by))
                                <div style="text-align: center; margin-top: 10px;">
                                    <img src="data:image/png;base64,{{ $undangan->qr_approved_by }}" width="100" alt="QR Code">
                                </div>
                            @endif
                            <p><b><u>{{ $undangan->nama_bertandatangan }}</u></b></p>
                        </td>
                    </tr>
                </table>
                </div> {{-- Close collab --}}
            </div> {{-- Close letter --}}
        </div> {{-- Close content --}}
    </main>
</body>
</html>