<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan</title>
    <link rel="stylesheet" href="/css/format-surat/format-cetakLaporan.css">
</head>
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

        .memo-title {
            text-align: center;
            justify-content: center;
            font-size: 26px;
            font-weight: bold;
            color: black; 
            margin-top: 3cm; 
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
            table-layout: fixed; 
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
            width: 100%;
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

        .view-mode .header1,
        .view-mode .header2 {
            position: fixed; 
            top: 150px; 
            left: 50%;
            transform: translateX(-50%); 
            width: 40%;
            background-color: white;
            padding: 10px;
            text-align: left;
            z-index: 1000;
        }

        .view-mode .header2 {
            top: 6.5cm; 
            width: 38.5%;
        }

        .view-mode .fill {
            position: relative;
            width: 95%;
            margin-left: auto;
            margin-right: auto;
            text-align: justify;
            /* overflow-y: auto;  */
            /* max-height: calc(100vh - 250px);  */
        }

        .view-mode .collab {
            position: relative;
            margin-top: 5.2cm; 
            width: 95%;
            margin-left: auto;
            margin-right: auto;
            text-align: justify;
            overflow-y: auto; 
            max-height: calc(100vh - 13cm); 

        }

        .pdf-mode header img,
        .pdf-mode footer img,
        .pdf-mode .content {
            width: 100%;
        }
    </style>
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
        <div class="container">
            <div class="memo-title">
                <h5>LAPORAN UNDANGAN RAPAT
                    <br>TAHUN 2025 
                </h5>
            </div>
            <div class="letter">
                <div class="row">
                    <div class="col-md-12">
                        <div class="fill">
                            <h3>Laporan Tgl. {{ \Carbon\Carbon::parse($tgl_awal)->format('d-m-Y') }} / {{ \Carbon\Carbon::parse($tgl_akhir)->format('d-m-Y') }}</h3>
                            <table>
                                <tr>
                                    <th>NO</th>
                                    <th>SERI</th>
                                    <th>DATA MASUK</th>
                                    <th>DATA DISAHKAN</th>
                                    <th>NAMA DOKUMEN</th>
                                    <th>NO DOKUMEN</th>
                                    <th>STATUS</th>
                                </tr>
                                @foreach ($undangans as $index => $laporan)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $laporan->seri_surat }}</td>
            <td>{{ $laporan->tgl_dibuat ? $laporan->tgl_dibuat->format('d-m-Y') : '-' }}</td>

            <td>{{ $laporan->tgl_disahkan ? $laporan->tgl_disahkan->format('d-m-Y') : '-' }}</td>

            <td>{{ $laporan->judul ?? '-' }}</td>

            <td>{{ $laporan->nomor_undangan ?? '-' }}</td>


            <td>
                <span class="badge bg-{{ $laporan->status == 'approve' ? 'success' : 'warning' }}">
                    {{ $laporan->status == 'approve' ? 'Diterima' : 'Pending' }}
                </span>
            </td>

        </tr>
        @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div >
                <table class="signature">
                    <tr>
                        <td>
                            <p><b>Kepala Divisi</b></p><br><br><br>
                            <p><b>Hisyam Syafiq A. A</b></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
