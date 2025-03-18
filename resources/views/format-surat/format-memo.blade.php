<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo</title>
    
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
            /* margin-top: 5.2cm;  */
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
            <h3 class="memo-title">Memo</h3>
            <div class="letter">
                <table class="header1">
                    @if ($memo->tgl_disahkan!= null)
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ $memo->tgl_disahkan->translatedFormat('d F Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td>{{ $memo->nomor_memo }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td><b>{{ $memo->judul }}</b></td>
                    </tr>
                </table>
                <div class="header2">
                    <table>
                        <tr>
                            <th>Dari : Unit {{ $memo->divisi->nm_divisi }}</th>
                            <th>Kepada Yth : {{ $memo->tujuan }}</th>
                        </tr>
                    </table>                 
                </div>
                <div class="collab">
                    <div class="fill">
                        <p>{!! $memo->isi_memo !!}</p>
                        @if($memo->kategoriBarang) 
                        <table>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                            </tr>
                            @foreach ($memo->kategoriBarang as $index => $barang)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $barang->barang }}</td>
                                    <td>{{ $barang->qty }}</td>
                                    <td>{{ $barang->satuan }}</td>
                                </tr>
                            @endforeach
                        </table>
                        @endif
                        <p style="text-align: justify;">Demikian kami sampaikan. Atas segala perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
                    </div>
                    <table class="signature">
                        <tr>
                            <td>
                                <p><b>Hormat kami,</b></p>
                                <b>Manajer {{ $memo->divisi->nm_divisi }}</b> <br><br><br>
                                <p><b><u>{{ $memo->nama_bertandatangan }}</u></b></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>