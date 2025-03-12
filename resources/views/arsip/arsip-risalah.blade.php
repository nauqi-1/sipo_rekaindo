@extends('layouts.superadmin')

@section('title', 'Arsip Risalah')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Risalah Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="#">Arsip</a>/<a style="color:#565656" href="#">Arsip Risalah Rapat</a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="arsip">
        <div class="title d-flex justify-content-between align-items-center mb-3">
            <h2><b>Arsip Risalah Rapat</b></h2>
            <div class="d-flex gap-2">
                <div class="search">
                    <img src="/img/memo-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table-light">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Tanggal Risalah
                    <button class="data-md">
                        <a href="" style="color:rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tanggal Disahkan
                    <button class="data-md">
                        <a href="" style="color: rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                    </button>
                </th>
                <th>Divisi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 3; $i++)
            <tr>
                <td class="nomor">{{ $i }}</td>
                <td class="nama-dokumen text-success">Risalah Rapat Pengesahan</td>
                <td>21-10-2024</td>
                <td>1596</td>
                <td>837.06/REKA/GEN/VII/2024</td>
                <td>22-10-2024</td>
                <td>HR & GA</td>
                <td>
                    <span class="badge bg-success">Diterima</span>
                </td>
                <td>
                    <button class="btn btn-sm1"><img src="/img/arsip/unduh.png" alt="unduh"></button>
                    <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <img src="/img/arsip/delete.png" alt="delete">
                    </button>
                    <a class="btn btn-sm3" href="{{route('view.risalah-arsip')}}"><img src="/img/arsip/preview.png" alt="preview"></a>
                </td>
            </tr>
            @endfor
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
                    <h5 class="mb-4" style="color: #545050;"><b>Hapus Risalah Rapat?</b></h5>
                    <!-- Tombol -->
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('arsip.risalah')}}">Batal</a></button>
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
                    <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Risalah Rapat</b></h5>
                    <!-- Tombol -->
                    <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('arsip.risalah')}}">Kembali</a></button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection