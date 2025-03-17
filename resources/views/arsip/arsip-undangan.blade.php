@extends('layouts.superadmin')

@section('title', 'Arsip Undangan Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Undangan Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}">Beranda</a>/<a href="#">Arsip</a>/<a style="color:#565656" href="#">Arsip Undangan Rapat</a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="arsip">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="title"><b>Arsip Undangan Rapat</b></h4>
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
                    <th>Tanggal Undangan
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
            @foreach($arsipUndangan as  $arsip)
                <tr>
                    <td class="nomor">{{ $loop->iteration }}</td>
                    <td class="nama-dokumen text-success">
                            {{ $arsip->document ? $arsip->document->judul : 'Memo Tidak Ditemukan' }}
                        </td>
                        <td>{{ $arsip->document ? $arsip->document->tgl_dibuat->format('d-m-Y') : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->seri_surat : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->nomor_undangan : '-' }}</td>
                        <td>{{ $arsip->document ? $arsip->document->tgl_disahkan->format('d-m-Y') : '-' }}</td>
                        <td>{{ $arsip->document && $arsip->document->divisi ? $arsip->document->divisi->nm_divisi : '-' }}</td>
                        <td>
                        <span class="badge bg-success">Diterima</span>
                    </td>
                    <td>
                    <!-- <button class="btn btn-sm1"><img src="/img/arsip/unduh.png" alt="unduh"></button> -->
                    <a href="{{ route('cetakundangan', ['id' => $arsip->document->id_undangan]) }}" class="btn btn-sm1" target="_blank">
                        <img src="/img/arsip/unduh.png" alt="unduh">
                    </a>
                    <!-- Tombol Delete (Hanya Memicu Modal) -->
                    <button class="btn btn-sm2 delete-btn" data-bs-toggle="modal" data-bs-target="#deleteArsipUndanganModal" data-route="{{ route('arsip.restore', ['document_id' => $arsip->document->id_undangan, 'jenis_document' => 'Undangan']) }}">
                        <img src="/img/arsip/delete.png" alt="delete">
                    </button>

                    <button class="btn btn-sm3" onclick="window.location.href='{{route('view.undangan-arsip',$arsip->document->id_undangan)}}'"><img src="/img/arsip/preview.png" alt="preview"></button>    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteArsipUndanganModal" tabindex="-1" aria-labelledby="deleteArsipUndanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/risalah/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Hapus Undangan Rapat dari Arsip?</b></h5>
            <form id="deleteArsipUndanganForm" method="POST">
                @csrf
                @method('DELETE')
                <!-- Tombol -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmDeleteArsipUndangan">Oke</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Berhasil -->
<div class="modal fade" id="deleteSuccessArsipUndanganModal" tabindex="-1" aria-labelledby="deleteSuccessArsipUndanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/risalah/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Hapus Undangan Rapat</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function () {
        let deleteArsipUndanganModal = document.getElementById("deleteArsipUndanganModal");
        let deleteArsipUndanganForm = document.getElementById("deleteArsipUndanganForm");
        let deleteArsipUndanganSuccessModal = new bootstrap.Modal(document.getElementById("deleteSuccessArsipUndanganModal"));
        let confirmDeleteBtn = document.getElementById("confirmDeleteArsipUndangan");

        let deleteRoute = ""; // Menyimpan URL DELETE

        // Event Listener untuk Menampilkan Modal Delete
        deleteArsipUndanganModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            deleteRoute = button.getAttribute("data-route");
        });

        // Event Listener untuk Tombol "OK" di Modal
        confirmDeleteBtn.addEventListener("click", function (event) {
            event.preventDefault(); // Mencegah submit default

            fetch(deleteRoute, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
            }).then(response => {
                if (response.ok) {
                    let modalInstance = bootstrap.Modal.getInstance(deleteArsipUndanganModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteArsipUndanganSuccessModal.show();
                        setTimeout(() => {
                            location.reload(); // Refresh halaman setelah 2 detik
                        }, 1500);
                    }, 500);
                }
            }).catch(error => console.error("Error:", error));
        });
    });
</script>
@endsection