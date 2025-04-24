@extends('layouts.admin')

@section('title', 'Risalah Admin')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
        </div>
        <h1>Risalah</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Risalah Rapat</a>
            </div>
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
                        <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
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
                @foreach ($risalahs as $index => $risalah)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ $risalah->status == 'reject' ? 'text-danger' : ($risalah->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $risalah->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $risalah->seri_surat }}</td>
                    <td>{{ $risalah->nomor_risalah }}</td>
                    <td>{{ $risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $risalah->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                        <td>
                            @if ($risalah->status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($risalah->status == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        
                    </td>

                    <td>
                       
                            
                            <a href="{{ route('risalah.restore',['id' => $risalah->id]) }}" class="btn btn-sm1">
                                <img src="/img/risalah/share.png" alt="share">
                            </a>
                           
                       

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $risalahs->links('pagination::bootstrap-5') }}
</div>

<!-- Overlay Add Memo Success -->
<div class="modal fade" id="successAddRisalahModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Risalah</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Edit Memo Success -->
<div class="modal fade" id="successEditRisalahModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengubah Risalah</p>
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
            <img src="/img/memo-admin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4"><b>Arsip Risalah?</b></h5>
            <!-- Tombol -->
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmArsipRisalah">Oke</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Arsip Berhasil -->
<div class="modal fade" id="successArsipRisalahModal" tabindex="-1" aria-labelledby="successArsipRisalahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Arsip Risalah</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Event listener untuk modal sukses tambah memo
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller memo store
            var successModal = new bootstrap.Modal(document.getElementById("successAddRisalahModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 1500);
        @endif
    });

    // Event listener untuk modal sukses edit memo
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'User updated successfully') // merujuk ke parameter controller memo update
            var successModal = new bootstrap.Modal(document.getElementById("successEditRisalahModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 1500);
        @endif
    });

    // Event Listener Arsip Memo
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsipRisalah");
        const confirmArsipButton = document.getElementById("confirmArsipRisalah");
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
            arsipMemoModal.hide();
        });

        // Saat tombol "OK" ditekan, submit form dan tampilkan modal sukses
        confirmArsipButton.addEventListener("click", function () {
            if (currentForm) {
                arsipMemoModal.hide(); // Tutup modal konfirmasi
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
</script>
@endsection