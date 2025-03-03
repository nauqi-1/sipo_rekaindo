<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan rapat</title>
    <link rel="stylesheet" href="/css/format-surat/format-undangan.css">
</head>
<body>
    <div class="container">
        <div class="date">
            <p>Madiun, 13 Juni 2023</p>
        </div>
        <br>
        <div class="letter">
            <header>
                <table>
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td>{{ $undangan->nomor_undangan }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td><b>{{ $undangan->judul }}</b></td>
                    </tr>
                </table>
            </header>
            <div class="goal">
                <h4><b>Kepada Yth. :</b></h4>
                <table>
                @foreach ($tujuanList as $index => $tujuan)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td>{{ trim($tujuan) }}</td> <!-- Trim untuk menghapus spasi berlebih -->
                </tr>
                @endforeach
                </table>
                <p class="">Di Tempat</p>
            </div>
            <div class="fill">
                <p>Dengan Hormat,</p>
                <p class="contents">Bersama ini kami mengharapkan kehadiran Bapak / Ibu pada: </p>
                <table>
                    <tr>
                        <td>Hari, tanggal</td>
                        <td>:</td>
                        <td>Rabu, 14 Juni 2023</td>
                    </tr>
                    <tr>
                        <td>Pukul</td>
                        <td>:</td>
                        <td>08.00 WIB s.d selesai</td>
                    </tr>
                    <tr>
                        <td>Tempat</td>
                        <td>:</td>
                        <td>R. Rapat Lt. 2, PT Rekaindo Global Jasa
                            Jalan Candi Sewu Nomor 30 Madiun.</td>
                    </tr>
                    <tr>
                        <td>Agenda</td>
                        <td>:</td>
                        <td>Kick Off Meeting Developing Payroll System</td>
                    </tr>
                </table>
            </div>
            <footer>
                <p style="text-align: justify;">Demikian undangan ini kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu kami ucapkan terima kasih.</p>
            </footer>
            <div class="signature">
                <p><b>PJ M Maintenance QA HSE & IT</b></p>
                <br><br><br>
                <p><b><u>Hisyam Syafiq A. A</u></b></p>
            </div>
        </div>
    </div>
</body>
</html>