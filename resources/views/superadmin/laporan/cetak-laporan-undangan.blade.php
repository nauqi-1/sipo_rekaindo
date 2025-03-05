@extends('layouts.superadmin')

@section('title', 'Laporan Undangan Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('laporan-undangan.superadmin') }}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Laporan Undangan Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="#">Beranda</a>/<a href="#">Laporan</a>/<a style="color:#565656" href="#">Laporan Undangan Rapat</a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="cetak-laporan">
        <div class="title d-flex justify-content-between align-items-center mb-3">
            <h2><b>Laporan Undangan Rapat</b></h2>
            @if(session('filter_dates'))
                <div class="filter-info">
                    Menampilkan undangan rapat dari tanggal {{ session('filter_dates')['tgl_awal'] }} hingga {{ session('filter_dates')['tgl_akhir'] }}
                </div>
            @endif
            <div class="d-flex gap-2">
                <div class="search">
                    <img src="/img/memo-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
                </div>
                <!-- Add User Button to Open Mod    al -->
                <a href="{{route ('format-cetakLaporan-undangan')}}" class="btn btn-primary-print">
                    <img src="/img/laporan/print.png" alt="print"> Cetak Data
</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table-light">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Data Masuk
                    <button class="data-md">
                        <a href="" style="color:rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Data Disahkan
                    <button class="data-md">
                        <a href="" style="color: rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                    </button>
                </th>
                <!-- <th>Divisi</th> -->
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @if ($undangans->isNotEmpty())
            @foreach ($undangans as $index => $laporan)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                <td>{{ $laporan->judul }}</td>
                <td>{{ $laporan->tgl_dibuat->format('d-m-Y') }}</td>
                <td>{{ $laporan->seri_surat }}</td>
                <td>{{ $laporan->nomor_undangan }}</td>
                <td>{{ $laporan->tgl_disahkan ? $laporan->tgl_disahkan->format('d-m-Y') : '-' }}</td>
                <!-- <td>{{ $laporan->divisi->nm_divisi }}</td> -->
                <td>
                    <span class="badge bg-{{ $laporan->status == 'approve' ? 'success' : 'warning' }}">
                        {{ $laporan->status == 'approve' ? 'Diterima' : 'Pending' }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm1"><img src="/img/arsip/unduh.png" alt="unduh"></button>
                    <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <img src="/img/arsip/delete.png" alt="delete">
                    </button>
                    <button class="btn btn-sm3"><img src="/img/arsip/preview.png" alt="preview"></button>
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8">Tidak ada undangan rapat pada tanggal yang dipilih.</td>
            </tr>
        @endif
        </tbody>
    </table>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Tombol Close -->
                <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body text-center">
                    <!-- Ikon atau Gambar -->
                    <img src="/img/risalah/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                    <!-- Tulisan -->
                    <h5 class="mb-4" style="color: #545050;"><b>Hapus Undangan?</b></h5>
                    <!-- Tombol -->
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('cetak-laporan-undangan.superadmin')}}">Batal</a></button>
                        <button type="button" class="btn ok" id="confirmDelete">Oke</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Berhasil -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Tombol Close -->
                <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body text-center">
                    <!-- Ikon atau Gambar -->
                    <img src="/img/risalah/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                    <!-- Tulisan -->
                    <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Undangan</b></h5>
                    <!-- Tombol -->
                    <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('cetak-laporan-undangan.superadmin')}}">Kembali</a></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
