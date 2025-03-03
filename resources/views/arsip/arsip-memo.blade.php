@extends('layouts.superadmin')

@section('title', 'Arsip Memo')
      
@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}">Beranda</a>/<a href="#">Arsip</a>/<a style="color:#565656" href="#">Arsip Memo</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="arsip">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="title"><b>Arsip Memo</b></h2>
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
                    <th>Divisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($arsipMemo as  $arsip)
                <tr>
                    <td class="nomor">{{ $loop->iteration }}</td>
                    <td class="nama-dokumen text-success">
                            {{ $arsip->document ? $arsip->document->judul : 'Memo Tidak Ditemukan' }}
                        </td>
                        <td>{{ $arsip->document ? $arsip->document->tgl_dibuat : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->seri_surat : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->nomor_memo : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->tgl_disahkan : '-' }}</td>
                        <td>{{ $arsip->document && $arsip->document->divisi ? $arsip->document->divisi->nm_divisi : '-' }}</td>
                        <td>
                        <span class="badge bg-success">Diterima</span>
                    </td>
                    <td>
                        <button class="btn btn-sm1"><img src="/img/arsip/unduh.png" alt="unduh"></button>
                        <form action="{{ route('arsip.restore', ['document_id' => $arsip->document->id_memo, 'jenis_document' => 'Memo']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/arsip/delete.png" alt="delete">
                        </button>
                        </form>
                        <button class="btn btn-sm3" onclick="window.location.href='{{route('view.memo-arsip',$arsip->document->id_memo)}}'"><img src="/img/arsip/preview.png" alt="preview"></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    

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
                    <h5 class="mb-4" style="color: #545050;"><b>Hapus Memo?</b></h5>
                    <!-- Tombol -->
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('arsip-memo.superadmin')}}">Batal</a></button>
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
                    <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Memo</b></h5>
                    <!-- Tombol -->
                    <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('arsip-memo.superadmin')}}">Kembali</a></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('confirmDelete').addEventListener('click', function () {
            // Ambil referensi modal
            const deleteModalEl = document.getElementById('deleteModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            
            // Tutup modal Hapus terlebih dahulu
            deleteModal.hide();
            
            // Pastikan modal benar-benar tertutup sebelum membuka modal berikutnya
            deleteModalEl.addEventListener('hidden.bs.modal', function () {
                const successModal = new bootstrap.Modal(document.getElementById('deleteSuccessModal'));
                successModal.show();
            }, { once: true }); // Tambahkan event listener hanya sekali
        });
</script>  
@endsection