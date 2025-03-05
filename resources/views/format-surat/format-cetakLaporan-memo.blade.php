<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan</title>
    <link rel="stylesheet" href="/css/format-surat/format-cetakLaporan.css">
</head>
<body>
    <div class="container">
        <div class="title">
            <h5>LAPORAN MEMO
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

    @foreach ($memos as $index => $laporan)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $laporan->seri_surat }}</td>
        <td>{{ $laporan->tgl_dibuat ? $laporan->tgl_dibuat->format('d-m-Y') : '-' }}</td>

        <td>{{ $laporan->tgl_disahkan ? $laporan->tgl_disahkan->format('d-m-Y') : '-' }}</td>

        <td>{{ $laporan->judul ?? '-' }}</td>

        <td>{{ $laporan->nomor_memo ?? '-' }}</td>


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
        <div class="signature">
            <table>
                <tr>
                    <td>Kepala Divisi</td>
                </tr>
                <tr>
                    <td>Hisyam Syafiq A. A</td>
                </tr>
        </div>
    </div>
</body>
