@extends('layouts.superadmin')

@section('title', 'Undangan Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
        </div>
        <h1>Undangan Rapat</h1>
    </div>        
    <div class="row">
    <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>
            </div>
            <form method="GET" action="{{ route('undangan.superadmin') }}" class="search-filter d-flex gap-2">
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
            <form method="GET" action="{{ route('undangan.superadmin') }}" class="search-filter d-flex gap-2">
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
                    <div  class="dropdown">
                        <select name="divisi_id_divisi" id="divisi_id_divisi" class="form-select" onchange="this.form.submit()">
                            <option value="pilih" disabled {{ !request()->filled('divisi_id_divisi') ? 'selected' : '' }}>Pilih Divisi</option>
                            @foreach($divisi as $d)
                                <option value="{{ $d->id_divisi }}" {{ request('divisi_id_divisi') == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nm_divisi }}
                                </option>
                            @endforeach
                        </select>
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
            @foreach ($undangans as $index => $undangan)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                <td class="nama-dokumen 
                    {{ $undangan->status == 'reject' ? 'text-danger' : ($undangan->status == 'pending' ? 'text-warning' : 'text-success') }}">
                    {{ $undangan->judul }}
                </td>
                <td>{{ \Carbon\Carbon::parse($undangan->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $undangan->seri_surat }}</td>
                <td>{{ $undangan->nomor_undangan }}</td>
                <td>{{ $undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $undangan->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                </td>
                <td>
                    @if ($undangan->status == 'reject')
                        <span class="badge bg-danger">Ditolak</span>
                    @elseif ($undangan->status == 'pending')
                        <span class="badge bg-warning">Diproses</span>
                    @else
                        <span class="badge bg-success">Diterima</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteUndanganModal" data-memo-id="{{ $undangan->id_undangan }}"  data-route="{{ route('undangan.destroy', [$undangan->id_undangan, 'jenis_document' => 'Undangan']) }}">
                        <img src="/img/undangan/Delete.png" alt="delete">
                    </button>
                    
                    @if ($undangan->status == 'approve') 
                    <form action="{{ route('arsip.archive', ['document_id' => $undangan->id_undangan, 'jenis_document' => 'Undangan']) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('POST')
                        <button class="btn btn-sm3 submitArsip">
                            <img src="/img/undangan/arsip.png" alt="arsip">
                        </button>
                    </form>

                    @else
                        <a href="{{route ('undangan.edit',$undangan->id_undangan)}}" class="btn btn-sm3">
                            <img src="/img/undangan/edit.png" alt="edit">
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $undangans->links('pagination::bootstrap-5') }}
</div>

<!-- Overlay Add User Success -->
<div class="modal fade" id="successAddUndanganModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Undangan Rapat</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Delete Memo -->
<div class="modal fade" id="deleteUndanganModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus undangan?</h5>
            <form id="deleteUndanganForm" method="POST">
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
<div class="modal fade" id="deleteUndanganSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Close Button -->
                <img src="/img/undangan/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menghapus Undangan Rapat</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip -->
<div class="modal fade" id="arsipUndanganModal" tabindex="-1" aria-labelledby="arsipUndanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Arsip Undangan?</b></h5>
            <!-- Tombol -->
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmArsip">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip Berhasil -->
<div class="modal fade" id="successArsipUndanganModal" tabindex="-1" aria-labelledby="successArsipUndanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/undangan/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Arsip Undangan</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function () {
        let deleteUndanganModal = document.getElementById("deleteUndanganModal");
        let deleteUndanganForm = document.getElementById("deleteUndanganForm");
        let deleteUndanganSuccessModal = new bootstrap.Modal(document.getElementById("deleteUndanganSuccessModal"));

        // Event ketika modal delete user ditampilkan
        deleteUndanganModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            let route = button.getAttribute("data-route");
            deleteUndanganForm.setAttribute("action", route);
        });

        // Event ketika form delete dikirim
        deleteUndanganForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Mencegah pengiriman form default

            let formAction = deleteUndanganForm.getAttribute("action");

            fetch(formAction, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
            }).then(response => {
                if (response.ok) {
                    let modalInstance = bootstrap.Modal.getInstance(deleteUndanganModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteUndanganSuccessModal.show();
                        setTimeout(() => {
                            location.reload(); // Refresh halaman setelah 2 detik
                        }, 1500);
                    }, 500);
                }
            }).catch(error => console.error("Error:", error));
        });
    });

    // Event listener arsip Undangan
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsip");
        const confirmArsipButton = document.getElementById("confirmArsip");
        const cancelArsipButton = document.querySelector("#arsipUndanganModal .btn-outline-secondary");
        const arsipUndanganModal = new bootstrap.Modal(document.getElementById("arsipUndanganModal"));
        const successArsipUndanganModal = new bootstrap.Modal(document.getElementById("successArsipUndanganModal"));

        let currentForm = null;

        // Saat tombol arsip ditekan, simpan form yang akan dikirim
        arsipButtons.forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault(); // Mencegah submit langsung
                currentForm = this.closest("form"); 
                arsipUndanganModal.show(); // Tampilkan modal konfirmasi
            });
        });

        // Saat tombol "Batal" ditekan, tutup modal konfirmasi
        cancelArsipButton.addEventListener("click", function () {
            arsipUndanganModal.hide();
        });

        // Saat tombol "OK" ditekan, submit form dan tampilkan modal sukses
        confirmArsipButton.addEventListener("click", function () {
            if (currentForm) {
                arsipUndanganModal.hide(); // Tutup modal konfirmasi
                setTimeout(() => {
                    successArsipUndanganModal.show(); // Tampilkan modal sukses setelah modal konfirmasi tertutup
                }, 300); 

                setTimeout(() => {
                    successArsipUndanganModal.hide();
                    currentForm.submit(); // Submit form setelah modal sukses ditutup
                }, 1500);
            }
        });
    });

    // Event listener untuk modal sukses tambah Undangan
    document.addEventListener("DOMContentLoaded", function () {
    @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller Undangan store
        var successModal = new bootstrap.Modal(document.getElementById("successAddUndanganModal"));
        successModal.show();
        setTimeout(function () {
            successModal.hide();
        }, 1500);
    @endif
    });
</script>
@endsection