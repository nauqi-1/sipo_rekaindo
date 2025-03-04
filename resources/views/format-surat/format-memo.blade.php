<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo</title>
    
    <style>
        .container {
            font-family: arial;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            border: 1px solid #ddd;
            position: relative;
            font-size: 12px;
        }

        /* Agar gambar background berada di belakang */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .title {
            margin-top: 15%;
            font-size: 20px;
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
        }

        .header2 th {
            border-top: 3px solid black;
            border-bottom: 3px solid black;
            text-align: center;
            font-weight: normal;
            padding: 10px;
        }

        /* Menambahkan garis tengah pemisah antar <th> */
        .header2 th + th {
            border-left: 3px solid black;
        }

        .fill {
            margin-top: 20px;
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

        .contents {
            text-align: justify;
            line-height: 0.7cm;
        }

        .signature {
            margin-top: 5%;
            text-align: left !important;
            width: fit-content;
            margin-left: auto;
        }

        .signature p {
            text-align: center;
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Gunakan Base64 jika tersedia, jika tidak pakai asset() -->
        <img class="background" src="{{ $base64Image ?? asset('img/border-surat.png') }}" alt="Background">

        <div class="title">
            <h2>Memo</h2>
        </div>
        <div class="letter">
            <header>
                <table class="header1">
                    @if ($memo->tgl_disahkan!= null)
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ $memo->tgl_disahkan->translatedFormat('d F Y') }}</td>
                        <!-- <td>{{ $memo->tgl_disahkan? \Carbon\Carbon::parse($memo->tgl_disahkan)->format('d F Y') : '-' }}</td> -->
                    </tr>
                    @endif
                    <tr>
                        <td>No</td>
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
            </header>
            <div class="fill">
                {!! $memo->isi_memo !!}
                
                @if($memo->kategoriBarang) <!-- Perbaikan Nama Variabel -->
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
            </div>
            <footer>
                <p style="text-align: justify;">Demikian kami sampaikan. Atas segala perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
            </footer>
            <div class="signature">
                <p>Hormat kami,<br>Manajer {{ $memo->divisi->nm_divisi }}</p>
                <br><br><br>
                <p>{{ $memo->nama_bertandatangan }}</p>
            </div>
        </div>
    </div>

</body>
</html>
