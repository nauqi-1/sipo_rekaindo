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
            /* table-layout: fixed;  */
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
        .header2 th + th {
            border-left: 3px solid black;
        }
        .header2 td {
            padding: 0; /* Hapus padding */
            margin: 0; /* Hapus margin */
            text-align: left;
            white-space: nowrap; /* Cegah teks turun ke bawah */
        }

        .header2 td:first-child {
            width: 1%;
            text-align: left; /* Nomor tetap rata kanan */
            padding-right: 10px; /* Jarak antara nomor dan teks hanya 2px */
        }

        .fill {
            margin-top: 5px;
            width: 95%;
            margin: 0 auto;
        }
        .fill p {
            text-align: left;
        }
        .fill table {
            border-collapse: collapse;
            width: 100%;
        }
        .fill table th, .fill table td {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
        }
        .fill table th:first-child,
        .fill table td:first-child {
            width: 5%;
            min-width: 40px;
        }
        .fill table th:nth-child(3),
        .fill table td:nth-child(3),
        .fill table th:nth-child(4),
        .fill table td:nth-child(4) {
            width: 10%; 
            min-width: 100px; 
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
                <p>Madiun, {{ $undangan->tgl_disahkan?->translatedFormat('d F Y') }}</p>
            </div>
            <br>
            <div class="letter">
                <table class="header1">
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <!-- <td>{{ $undangan->nomor_undangan }}</td> -->
                        <td>{{ trim($undangan->nomor_undangan) }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <!-- <td><b>{{ $undangan->judul }}</b></td> -->
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
                        <p>Dengan Hormat,</p>
                        <p class="contents">Bersama ini kami mengharapkan kehadiran Bapak / Ibu pada: </p>
                        <p>{!! $undangan->isi_undangan !!}</p>
                        <p style="text-align: justify;">Demikian undangan ini kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih.</p>
                    </div>
                    <table class="signature">
                        <tr>
                            <td>
                                <!-- Hormat Kami,<br>  -->
                                <p><b>Hormat kami,</b></p>
                                <b>Manajer {{ $undangan->divisi->nm_divisi }}</b> <br><br><br>
                                <p><b><u>{{ $undangan->nama_bertandatangan }}</u></b></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>