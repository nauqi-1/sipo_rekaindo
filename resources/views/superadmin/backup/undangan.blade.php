@extends('layouts.admin')

@section('title', 'Undangan Rapat')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{Route('admin.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
        </div>
        <h1>Undangan Rapat</h1>
    </div>        
    <div class="row">
    <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="{{route('admin.dashboard')}}">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>
            </div>
        <form method="GET" action="{{ route('undangan.backup') }}" class="search-filter d-flex gap-2">
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
        <form method="GET" action="{{ route('undangan.backup') }}" class="search-filter d-flex gap-2">
            <!-- <div class="dropdown">
            <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Status</option>
                    <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                    <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div> -->
            <!-- <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="date" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}"  onchange="this.form.submit()" placeholder="Tanggal Awal" style="width: 100%;">
            </div> -->
            <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="text" id="tgl_dibuat_awal" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}" placeholder="Tanggal Awal" onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Awal'; }" onchange="this.form.submit()">
            </div>
            <i class="bi bi-arrow-right"></i>
            <!-- <div class="input-icon-wrapper" style="position: relative; width: 150px;">
            <input type="date" name="tgl_dibuat_akhir" class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" onchange="this.form.submit()" placeholder="Tanggal Akhir" style="width: 100%;">
            </div> -->
            <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="text" id="tgl_dibuat_akhir" name="tgl_dibuat_akhir"
                    class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" placeholder="Tanggal Akhir"
                    onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Akhir'; }" onchange="this.form.submit()">
            </div>
            <div class="d-flex gap-2">
                <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                    <img src="/img/undangan/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()" style="outline: none; box-shadow: none;">
                </div>
            </div>
                <div  class="dropdown">
                    <select name="divisi_id_divisi" id="divisi_id_divisi" class="form-select" onchange="this.form.submit()">
                    <option value="">Pilih Divisi</option>
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
                        <a href="{{ request()->fullUrlWithQuery(['sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
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
            @foreach ($undangans as $index => $undangan)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ 'text-danger'}}">
                        {{ $undangan->judul }}
                    </td>
               
                <td>{{ \Carbon\Carbon::parse($undangan->tgl_dibuat)->format('d-m-Y') }}</td>
                <td >{{ $undangan->seri_document }}</td>
                <td>{{ $undangan->nomor_document }}</td>
                <td>{{ $undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $undangan->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                </td>
                <td>
                        <span class="badge bg-danger">Memulihkan</span>  
                    
                </td>
                <td>
                    
                        <a href="{{ route('undangan.restore',['id' => $undangan->id_document]) }}" class="btn btn-sm1">
                            <img src="/img/restore.png" alt="restore" style="width: 20px; height: 20px;">
                        </a>
                        
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $undangans->links('pagination::bootstrap-5') }}
    </div>

<!-- Overlay Add Undangan Success -->
<div class="modal fade" id="successAddUndanganModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Undangan</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Edit Undangan Success -->
<div class="modal fade" id="successEditUndanganModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengubah Undangan</p>
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
            <h5 class="modal-title mb-4"><b>Arsip Undangann?</b></h5>
            <!-- Tombol -->
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmArsipUndangan">Oke</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip Berhasil -->
<div class="modal fade" id="successArsipUndanganModal" tabindex="-1" aria-labelledby="successArsipUndanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Arsip Undangan</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event listener untuk modal sukses tambah undangan
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller undangan store
            var successModal = new bootstrap.Modal(document.getElementById("successAddUndanganModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 1500);
        @endif
    });

    // Event listener untuk modal sukses tambah undangan
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'Undangan updated successfully') // merujuk ke parameter controller undangan update
            var successEditUndanganModal = new bootstrap.Modal(document.getElementById("successEditUndanganModal"));
            successEditUndanganModal.show();
            setTimeout(function () {
                successEditUndanganModal.hide();
            }, 1500);
        @endif
    });

    // Event Listener Arsip undangan
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsipUndangan");
        const confirmArsipButton = document.getElementById("confirmArsipUndangan");
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
</script>
@endsection