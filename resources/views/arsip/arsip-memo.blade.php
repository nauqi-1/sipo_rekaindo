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
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}">Beranda</a>/<a style="color:#565656" href="#">Arsip Memo</a>
            </div>
            <form method="GET" action="{{ route('arsip.memo') }}" class="search-filter d-flex gap-2">
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
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="page" value="{{ request('page') }}">
            </form>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="arsip">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="title"><b>Arsip Memo</b></h4>
            <div class="d-flex gap-2">
                <div class="search">
                    <form method="GET" action="{{ route('arsip.memo') }}" class="search-filter d-flex gap-2">
                        <div class="d-flex gap-2">
                            <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                                <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                                <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()" style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        <input type="hidden" name="page" value="{{ request('page') }}">
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
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}"
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
            @foreach($arsipMemo as $memo)
            <tr>
                <td class="nomor">{{ $loop->iteration }}</td>
                <td class="nama-dokumen
                        {{ $memo->status == 'reject' ? 'text-danger' : ($memo->status == 'correction' ? 'text-warning' : ($memo->status == 'approve' ? 'text-success' : '')) }}"
                    style="{{ $memo->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                    {{ Str::limit($memo->judul, 35, '...') }}
                </td>
                <td>{{ $memo->tgl_dibuat ? $memo->tgl_dibuat->format('d-m-Y') : '-' }}</td>
                <td>{{ $memo->seri_surat }}</td>
                <td>{{ $memo->nomor_memo }}</td>
                <td>{{ $memo->tgl_disahkan ? $memo->tgl_disahkan->format('d-m-Y') : '-' }}</td>
                <td>{{ $memo->kode }}</td>
                <td>
                    @if ($memo->final_status == 'reject')
                    <span class="badge bg-danger">Ditolak</span>
                    @elseif ($memo->final_status == 'pending')
                    <span class="badge bg-info">Diproses</span>
                    @elseif ($memo->final_status == 'correction')
                    <span class="badge bg-warning">Dikoreksi</span>
                    @else
                    <span class="badge bg-success">Diterima</span>
                    @endif
                </td>
                <td>
                    <!-- Button Unduh -->
                    <button class="btn btn-sm1" onclick="window.location.href='{{ route('cetakmemo',['id' => $memo->id_memo]) }}'"><img src="/img/arsip/unduh.png" alt="unduh"></button>

                    <!-- Button Arsip -->
                    @if ($memo)
                    <button class="btn btn-sm3" data-bs-toggle="modal" data-bs-target="#deleteArsipMemoModal" data-route="{{ route('arsip.restore', ['document_id' => $memo->id_memo, 'jenis_document' => 'Memo']) }}">
                        <img src="/img/arsip/unarchive2.png" alt="unarchive" style="height: 16px;">
                    </button>

                    <!-- Button View -->
                    <button class="btn btn-sm3" onclick="window.location.href='{{route('view.memo-arsip',$memo->id_memo)}}'"><img src="/img/arsip/preview.png" alt="preview"></button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $arsipMemo->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteArsipMemoModal" tabindex="-1" aria-labelledby="deleteArsipMemoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-admin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Keluarkan memo dari arsip?</b></h5>
            <form id="deleteArsipMemoForm" method="POST">
                @csrf
                @method('DELETE')
                <!-- Tombol -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmDeleteArsipMemo">Oke</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Berhasil -->
<div class="modal fade" id="deleteSuccessArsipMemoModal" tabindex="-1" aria-labelledby="deleteSuccessArsipMemoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengeluarkan Memo</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function() {
        let deleteArsipMemoModal = document.getElementById("deleteArsipMemoModal");
        let deleteArsipMemoForm = document.getElementById("deleteArsipMemoForm");
        let deleteArsipMemoSuccessModal = new bootstrap.Modal(document.getElementById("deleteSuccessArsipMemoModal"));
        let confirmDeleteBtn = document.getElementById("confirmDeleteArsipMemo");

        let deleteRoute = ""; // Menyimpan URL DELETE

        // Event Listener untuk Menampilkan Modal Delete
        deleteArsipMemoModal.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget;
            deleteRoute = button.getAttribute("data-route");
        });

        // Event Listener untuk Tombol "OK" di Modal
        confirmDeleteBtn.addEventListener("click", function(event) {
            event.preventDefault(); // Mencegah submit default

            fetch(deleteRoute, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    _method: "DELETE"
                })
            }).then(response => {
                if (response.ok) {
                    let modalInstance = bootstrap.Modal.getInstance(deleteArsipMemoModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteArsipMemoSuccessModal.show();
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