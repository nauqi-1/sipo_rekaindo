@extends('layouts.superadmin')

@section('title', 'Risalah Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
        </div>
        <h1>Risalah Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="#" style="color: #565656;">Risalah Rapat</a>
            </div>
            <form method="GET" action="{{ route('risalah.superadmin') }}" class="search-filter d-flex gap-2">
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
    <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
            <form method="GET" action="{{ route('risalah.superadmin') }}" class="search-filter d-flex gap-2">
                <div class="dropdown">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                        <option value="pending" {{ request('status') == 'correction' ? 'selected' : '' }}>Dikoreksi</option>
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
                        <a href="{{ request()->fullUrlWithQuery(['sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                                style="color:rgb(135, 135, 148); text-decoration: none;">
                                <span class="bi-arrow-down-up"></span>
                            </a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tanggal Disahkan
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tgl_disahkan','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
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
            @foreach ($risalahs as $index => $risalah)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                <td class="nama-dokumen 
                        {{ $risalah->status == 'reject' ? 'text-danger' : ($risalah->status == 'correction' ? 'text-warning' : ($risalah->status == 'approve' ? 'text-success' : '')) }}"
                    style="{{ $risalah->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                    {{ $risalah->judul }}
                </td>
                <td>{{ \Carbon\Carbon::parse($risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $risalah->seri_surat }}</td>
                <td>{{ $risalah->nomor_risalah }}</td>
                <td>{{ $risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $risalah->user->department->kode_department ?? $risalah->user->divisi->kode_divisi ?? '-' }}</td>
                </td>
                <td>
                    @if ($risalah->status == 'reject')
                        <span class="badge bg-danger">Ditolak</span>
                    @elseif ($risalah->status == 'pending')
                        <span class="badge bg-info">Diproses</span>
                    @elseif ($risalah->status == 'correction')
                        <span class="badge bg-warning">Dikoreksi</span>
                    @else
                        <span class="badge bg-success">Diterima</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteRisalahModal" data-memo-id="{{ $risalah->id_risalah }}"  data-route="{{ route('superadmin.risalah.destroy', [$risalah->id_risalah, 'jenis_document' => 'risalah']) }}">
                        <img src="/img/undangan/Delete.png" alt="delete">
                    </button>
                    
                    @if ($risalah->status == 'approve') 
                    <form action="{{ route('arsip.archive', ['document_id' => $risalah->id_risalah, 'jenis_document' => 'Risalah']) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('POST')
                        <button class="btn btn-sm3 submitArsip">
                            <img src="/img/undangan/arsip.png" alt="arsip">
                        </button>
                    </form>

                    @else
                        <a href="{{route ('risalah.edit',$risalah->id_risalah)}}" class="btn btn-sm3">
                            <img src="/img/undangan/edit.png" alt="edit">
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $risalahs->links('pagination::bootstrap-5') }}
</div>

<!-- Overlay Add User Success -->
<div class="modal fade" id="successAddRisalahModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Risalah Rapat</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteRisalahModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-2" id="deleteModalLabel">Yakin ingin menghapus risalah ini?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                Risalah yang dihapus akan masuk ke menu <strong>Pemulihan</strong> dan dapat dikembalikan sewaktu-waktu.
            </p>

                <!-- Tombol -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" id="openConfirmDeleteBtn" data-route="">Oke</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
                    
                </div>
        </div>
    </div>
</div>
<!-- Pop up konfirmasi penghapusan memo kedua-->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/memo-superadmin/warning.png" alt="Warning Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="confirmDeleteLabel">Yakin ingin menghapus risalah ini?</h5>
            <form id="deleteRisalahForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-danger me-2" id="confirmDeleteBtn">Ya, Hapus</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay Confirmation Delete Success -->
<div class="modal fade" id="deleteRisalahSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Close Button -->
                <img src="/img/undangan/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menghapus Risalah Rapat</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip -->
<div class="modal fade" id="arsipRisalahModal" tabindex="-1" aria-labelledby="arsipRisalahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Arsip Risalah?</b></h5>
            <!-- Tombol -->
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmArsip">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip Berhasil -->
<div class="modal fade" id="successArsipRisalahModal" tabindex="-1" aria-labelledby="successArsipRisalahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/undangan/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Arsip Risalah</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event listener untuk modal hapus undangan
    document.addEventListener("DOMContentLoaded", function () {
        const deleteRisalahModal = document.getElementById("deleteRisalahModal");
        const confirmDeleteModalElement = document.getElementById("confirmDeleteModal");
        const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement);
        const deleteRisalahSuccessModal = new bootstrap.Modal(document.getElementById("deleteRisalahSuccessModal"));
        const deleteRisalahForm = document.getElementById("deleteRisalahForm");
        const openConfirmDeleteBtn = document.getElementById("openConfirmDeleteBtn");

        let routeToDelete = "";

        // Ketika modal pertama ditampilkan
        deleteRisalahModal.addEventListener("show.bs.modal", function (event) {
            const triggerButton = event.relatedTarget;
            routeToDelete = triggerButton.getAttribute("data-route");

            // Simpan route ke tombol konfirmasi
            openConfirmDeleteBtn.setAttribute("data-route", routeToDelete);

            // Hilangkan backdrop modal sebelumnya (jika ada)
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });

        // Saat tombol konfirmasi di klik
        openConfirmDeleteBtn.addEventListener("click", function () {
            // Tutup modal awal
            const deleteModalInstance = bootstrap.Modal.getInstance(deleteRisalahModal);
            if (deleteModalInstance) deleteModalInstance.hide();

            // Tampilkan modal konfirmasi setelah delay
            setTimeout(() => {
                deleteRisalahForm.setAttribute("action", routeToDelete);
                confirmDeleteModal.show();
            }, 300);
        });

        // Submit form untuk hapus data
        deleteRisalahForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const formAction = deleteRisalahForm.getAttribute("action");

            fetch(formAction, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
            })
            .then(response => {
                if (response.ok) {
                    confirmDeleteModal.hide();
                    setTimeout(() => {
                        deleteRisalahSuccessModal.show();
                        setTimeout(() => location.reload(), 1500);
                    }, 500);
                } else {
                    alert("Gagal menghapus data.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat menghapus.");
            });
        });
    });


    // Event listener arsip Undangan
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsip");
        const confirmArsipButton = document.getElementById("confirmArsip");
        const cancelArsipButton = document.querySelector("#arsipRisalahModal .btn-outline-secondary");
        const arsipRisalahModal = new bootstrap.Modal(document.getElementById("arsipRisalahModal"));
        const successArsipRisalahModal = new bootstrap.Modal(document.getElementById("successArsipRisalahModal"));

        let currentForm = null;

        // Saat tombol arsip ditekan, simpan form yang akan dikirim
        arsipButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault(); // Mencegah submit langsung
                currentForm = this.closest("form"); 
                arsipRisalahModal.show(); // Tampilkan modal konfirmasi
            });
        });

        // Saat tombol "Batal" ditekan, tutup modal konfirmasi
        cancelArsipButton.addEventListener("click", function () {
            arsipRisalahModal.hide();
        });

        // Saat tombol "OK" ditekan, submit form dan tampilkan modal sukses
        confirmArsipButton.addEventListener("click", function () {
            if (currentForm) {
                arsipRisalahModal.hide(); // Tutup modal konfirmasi
                setTimeout(() => {
                    successArsipRisalahModal.show(); // Tampilkan modal sukses setelah modal konfirmasi tertutup
                }, 300); 

                setTimeout(() => {
                    successArsipRisalahModal.hide();
                    currentForm.submit(); // Submit form setelah modal sukses ditutup
                }, 1500);
            }
        });
    });

    // Event listener untuk modal sukses tambah Undangan
    document.addEventListener("DOMContentLoaded", function () {
    @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller Undangan store
        var successModal = new bootstrap.Modal(document.getElementById("successAddRisalahModal"));
        successModal.show();
        setTimeout(function () {
            successModal.hide();
        }, 1500);
    @endif
    });
</script>
@endsection