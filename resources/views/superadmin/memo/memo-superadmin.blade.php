@extends('layouts.superadmin')

@section('title', 'Memo')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/memo-superadmin/Vector_back.png" alt=""></a>
        </div>
        <h1>Memo</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="#" style="color: #565656;">Memo</a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
            <form method="GET" action="{{ route('memo.superadmin') }}" class="search-filter d-flex gap-2">
                <div class="dropdown">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                    <input type="text" id="tgl_dibuat_awal" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}" placeholder="Tanggal Awal" onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Awal'; }" onchange="this.form.submit()">
                </div>
                <i class="bi bi-arrow-right"></i>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                    <input type="text" id="tgl_dibuat_akhir" name="tgl_dibuat_akhir"
                        class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" placeholder="Tanggal Akhir"
                        onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Akhir'; }" onchange="this.form.submit()">
                </div>
                <div class="d-flex gap-2">
                    <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                        <img src="/img/memo-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                        <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()" style="outline: none; box-shadow: none;">
                    </div>
                </div>
                </form>
                <!-- Add User Button to Open Modal -->
                <a href="{{route ('memo-superadmin/add')}}" class="btn btn-add">+ <span>Tambah Memo</span></a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table-light">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Tanggal Memo
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
            @foreach ($memos as $index => $memo)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                <td class="nama-dokumen 
                    {{ $memo->status == 'reject' ? 'text-danger' : ($memo->status == 'pending' ? 'text-warning' : 'text-success') }}">
                    {{ $memo->judul }}
                </td>
                <td>{{ \Carbon\Carbon::parse($memo->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $memo->seri_surat }}</td>
                <td>{{ $memo->nomor_memo }}</td>
                <td>{{ $memo->tgl_disahkan ? \Carbon\Carbon::parse($memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $memo->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                </td>
                <td>
                    @if ($memo->status == 'reject')
                        <span class="badge bg-danger">Ditolak</span>
                    @elseif ($memo->status == 'pending')
                        <span class="badge bg-warning">Diproses</span>
                    @else
                        <span class="badge bg-success">Diterima</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteMemoModal"
                    data-memo-id="{{ $memo->id_memo }}"  data-route="{{ route('memo.destroy', $memo->id_memo) }}">
                        <img src="/img/memo-superadmin/Delete.png" alt="delete">
                    </button>
                    
                    <!-- status approve -->
                    @if ($memo->status == 'approve')
                    <form action="{{ route('arsip.archive', ['document_id' => $memo->id_memo, 'jenis_document' => 'Memo']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-sm3 submitArsip">
                            <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                        </button>
                    </form>

                    @else
                        <a href="{{ route('memo.edit', $memo->id_memo) }}" class="btn btn-sm3">
                            <img src="/img/memo-superadmin/edit.png" alt="edit">
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $memos->links('pagination::bootstrap-5') }}
</div>

<!-- Overlay Add User Success -->
<div class="modal fade" id="successAddMemoModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Memo</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Delete Memo -->
<div class="modal fade" id="deleteMemoModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus memo?</h5>
            <form id="deleteMemoForm" method="POST">
                @csrf
                @method('DELETE')
                <!-- Tombol -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Oke</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay Confirmation Delete Success -->
<div class="modal fade" id="deleteMemoSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Close Button -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="my-3" style="width: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menghapus Memo</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip -->
<div class="modal fade" id="arsipMemoModal" tabindex="-1" aria-labelledby="arsipMemoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-admin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Arsip Memo?</b></h5>
            <!-- Tombol -->
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmArsip">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip Berhasil -->
<div class="modal fade" id="successArsipMemoModal" tabindex="-1" aria-labelledby="successArsipMemoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Arsip Memo</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function () {
        let deleteMemoModal = document.getElementById("deleteMemoModal");
        let deleteMemoForm = document.getElementById("deleteMemoForm");
        let deleteMemoSuccessModal = new bootstrap.Modal(document.getElementById("deleteMemoSuccessModal"));

        // Event ketika modal delete user ditampilkan
        deleteMemoModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            let route = button.getAttribute("data-route");
            deleteMemoForm.setAttribute("action", route);
        });

        // Event ketika form delete dikirim
        deleteMemoForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Mencegah pengiriman form default

            let formAction = deleteMemoForm.getAttribute("action");

            fetch(formAction, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
            }).then(response => {
                if (response.ok) {
                    let modalInstance = bootstrap.Modal.getInstance(deleteMemoModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteMemoSuccessModal.show();
                        setTimeout(() => {
                            location.reload(); // Refresh halaman setelah 2 detik
                        }, 1500);
                    }, 500);
                }
            }).catch(error => console.error("Error:", error));
        });
    });

    // Event listener arsip memo
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsip");
        const confirmArsipButton = document.getElementById("confirmArsip");
        const cancelArsipButton = document.querySelector("#arsipMemoModal .btn-outline-secondary");
        const arsipMemoModal = new bootstrap.Modal(document.getElementById("arsipMemoModal"));
        const successArsipMemoModal = new bootstrap.Modal(document.getElementById("successArsipMemoModal"));

        let currentForm = null;

        // Saat tombol arsip ditekan, simpan form yang akan dikirim
        arsipButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault(); // Mencegah submit langsung
                currentForm = this.closest("form"); 
                arsipMemoModal.show(); // Tampilkan modal konfirmasi
            });
        });

        // Saat tombol "Batal" ditekan, tutup modal konfirmasi
        cancelArsipButton.addEventListener("click", function () {
            arsipMemoModal.hide();
        });

        // Saat tombol "OK" ditekan, submit form dan tampilkan modal sukses
        confirmArsipButton.addEventListener("click", function () {
            if (currentForm) {
                arsipMemoModal.hide(); // Tutup modal konfirmasi
                setTimeout(() => {
                    successArsipMemoModal.show(); // Tampilkan modal sukses setelah modal konfirmasi tertutup
                }, 300); 

                setTimeout(() => {
                    successArsipMemoModal.hide();
                    currentForm.submit(); // Submit form setelah modal sukses ditutup
                }, 1500);
            }
        });
    });

    // Event listener untuk modal sukses tambah memo
    document.addEventListener("DOMContentLoaded", function () {
    @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller memo store
        var successModal = new bootstrap.Modal(document.getElementById("successAddMemoModal"));
        successModal.show();
        setTimeout(function () {
            successModal.hide();
        }, 1500);
    @endif
    });
</script>
@endsection