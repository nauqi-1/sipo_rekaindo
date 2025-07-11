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
    <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a style="color:#565656" href="#">Arsip Risalah Rapat</a>
            </div>
            <form method="GET" action="{{ route('arsip.risalah') }}" class="search-filter d-flex gap-2">
            <label style="margin: 0; padding-bottom: 25px; padding-right: 12px; color: #565656;">
                Show
                <select name="per_page" onchange="this.form.submit()" style="color: #565656; padding: 2px 5px;">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                entries
            </label>
            </form>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="arsip">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="title"><b>Arsip Memo</b></h4>
            <div class="d-flex gap-2">
                <div class="search">
                <form method="GET" action="{{ route('arsip.risalah') }}" class="search-filter d-flex gap-2">
                <div class="d-flex gap-2">
                    <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                        <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                        <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()" style="outline: none; box-shadow: none;">
                    </div>
                </div>
                </form>
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
                <th>Tanggal Masuk
                <button class="data-md">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tgl_dibuat', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                    style="color:rgb(135, 135, 148); text-decoration: none;">
                        <span class="bi-arrow-down-up"></span>
                    </a>
                </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tanggal Disahkan
                <button class="data-md">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tgl_disahkan', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                    style="color: rgb(135, 135, 148); text-decoration: none;">
                        <span class="bi-arrow-down-up"></span>
                    </a>
                </button>
                </th>
                <th>Divisi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arsipRisalah as  $arsip)
            <tr>
                <td class="nomor">{{ $loop->iteration }}</td>
               <td class="nama-dokumen 
                        {{ $arsip->document->final_status == 'reject' ? 'text-danger' : ($arsip->document->final_status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $arsip->document->judul }}
                    </td>
                    <td>{{ $arsip->document ? $arsip->document->tgl_dibuat->format('d-m-Y') : '-' }}</td>
                    <td>{{ $arsip->document ? $arsip->document->seri_surat : '-' }}</td>
                    <td>{{ $arsip->document ? $arsip->document->nomor_risalah : '-' }}</td>
                    <td>{{ $arsip->document ? $arsip->document->tgl_disahkan->format('d-m-Y') : '-' }}</td>
                    <td>{{ $arsip->document && $arsip->document->divisi ? $arsip->document->divisi->nm_divisi : '-' }}</td>
                    <td>
                    @if ($arsip->document->final_status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($arsip->document->final_status == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                </td>
                <td>
                    <!-- Button Unduh -->
                    <button class="btn btn-sm1" onclick="window.location.href='{{ route('cetakrisalah',['id' => $arsip->document->id_risalah]) }}'"><img src="/img/arsip/unduh.png" alt="unduh"></button>

                    <!-- Button Arsip -->
                    @if ($arsip->document)
                    <button class="btn btn-sm2 delete-btn" data-bs-toggle="modal" data-bs-target="#deleteArsipRisalahModal" data-route="{{ route('arsip.restore', ['document_id' => $arsip->document->id_risalah, 'jenis_document' => 'Risalah']) }}">
                        <img src="/img/arsip/delete.png" alt="delete">
                    </button>

                    <!-- Button View -->
                    <button class="btn btn-sm3" onclick="window.location.href='{{route('view.risalah-arsip',$arsip->document->id_risalah)}}'"><img src="/img/arsip/preview.png" alt="preview"></button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $arsipRisalah->links('pagination::bootstrap-5') }}
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteArsipRisalahModal" tabindex="-1" aria-labelledby="deleteArsipRisalahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-admin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Hapus Risalah dari arsip?</b></h5>
            <form id="deleteArsipRisalahForm" method="POST">
                @csrf
                @method('DELETE')
                <!-- Tombol -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmDeleteArsipRisalah">Oke</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Berhasil -->
<div class="modal fade" id="deleteSuccessArsipRisalahModal" tabindex="-1" aria-labelledby="deleteSuccessArsipRisalahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Hapus Risalah</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function () {
        let deleteArsipRisalahModal = document.getElementById("deleteArsipRisalahModal");
        let deleteArsipRisalahForm = document.getElementById("deleteArsipRisalahForm");
        let deleteArsipRisalahSuccessModal = new bootstrap.Modal(document.getElementById("deleteSuccessArsipRisalahModal"));
        let confirmDeleteBtn = document.getElementById("confirmDeleteArsipRisalah");

        let deleteRoute = ""; // Menyimpan URL DELxETE

        // Event Listener untuk Menampilkan Modal Delete
        deleteArsipRisalahModal.addEventListener("show.bs.modal", function (event) {
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
                    let modalInstance = bootstrap.Modal.getInstance(deleteArsipRisalahModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteArsipRisalahSuccessModal.show();
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

