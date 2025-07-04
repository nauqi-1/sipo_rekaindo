@extends('layouts.admin')

@section('title', 'Risalah Rapat')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
        </div>
        <h1>Risalah Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Risalah Rapat</a>
            </div>
            <form method="GET" action="{{ route('risalah.admin') }}" class="search-filter d-flex gap-2">
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
            <form method="GET" action="{{ route('risalah.admin') }}" class="search-filter d-flex gap-2">
                <div class="dropdown">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="" disabled selected>Status</option>
                        <option value="">Semua</option>
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
                </form>
                <!-- Add User Button to Open Modal -->
                <a href="{{route ('add-risalah.admin')}}" class="btn btn-add">+ <span>Tambah Risalah Rapat</span></a>
            </div>
        </div>
    </div>

        <!-- Table -->
        <table class="table-light">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Verif</th>
                <th>Tgl. Risalah
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tgl. Disahkan
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tgl_disahkan','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
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
                @if (Auth::user()->divisi->id_divisi == $risalah->divisi->id_divisi)
                    <td class="nama-dokumen 
                        {{ $risalah->status == 'reject' ? 'text-danger' : ($risalah->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $risalah->judul }}
                    </td>
                @elseif(Auth::user()->divisi->id_divisi != $risalah->divisi->id_divisi)
                    <td class="nama-dokumen 
                        {{ $risalah->final_status == 'reject' ? 'text-danger' : ($risalah->final_status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $risalah->judul }}
                    </td>
                @endif
                <td>
                        @php
                            // Cari dokumen kiriman yang sesuai dengan ID risalah
                            $kirimDocument = $kirimDocuments->firstWhere('id_document', $risalah->id_risalah);
                        @endphp

                        @if($kirimDocument)
                            @if($kirimDocument->divisi_penerima == $kirimDocument->divisi_pengirim && $risalah->final_status == 'pending')
                                <img src="/img/checklist-kuning.png" alt="share" style="width: 20px;height: 20px;">
                            @elseif($kirimDocument->divisi_penerima == $kirimDocument->divisi_pengirim && $risalah->final_status == 'approve')
                                <img src="/img/checklist-hijau.png" alt="share" style="width: 20px;height: 20px;">
                            @else
                                <p>-</p>
                            @endif
                        @else
                            <p>-</p>
                        @endif
                    </td>
                <td>{{ \Carbon\Carbon::parse($risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $risalah->seri_surat }}</td>
                <td>{{ $risalah->nomor_risalah }}</td>
                <td>{{ $risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $risalah->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                <td>
                        @if ($risalah->final_status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($risalah->final_status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                </td>
                <td>
                    @if (Auth::user()->divisi->id_divisi == $risalah->divisi->id_divisi)
                            @if($risalah->final_status == 'pending' || $risalah->final_status == 'approve' )
                            <a href="{{ route('kirim-risalahAdmin.admin',['id' => $risalah->id_risalah]) }}" class="btn btn-sm1">
                                <img src="/img/memo-admin/share.png" alt="share">
                            </a>       
                            @endif
                        @elseif (Auth::user()->divisi->id_divisi != $risalah->divisi->id_divisi)
                            @if($risalah->final_status == 'pending' )
                            <a href="{{ route('kirim-risalahAdmin.admin',['id' => $risalah->id_risalah]) }}" class="btn btn-sm1">
                                <img src="/img/memo-admin/share.png" alt="share">
                            </a>       
                            @endif               
                        @endif

                    @if (Auth::user()->divisi->id_divisi == $risalah->divisi->id_divisi)
                            @if ($risalah->status == 'approve' || $risalah->status == 'reject' )
                                <form action="{{ route('arsip.archive', ['document_id' => $risalah->id_risalah, 'jenis_document' => 'Risalah']) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('POST') 
                                    <button type="submit" class="btn btn-sm3 submitArsipRisalah">
                                        <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('risalah.edit', $risalah->id_risalah) }}" class="btn btn-sm3">
                                    <img src="/img/risalah/edit.png" alt="edit">
                                </a>
                            @endif
                            @elseif (Auth::user()->divisi->id_divisi != $risalah->divisi->id_divisi)
                            @if ($risalah->final_status == 'approve' || $risalah->final_status == 'reject')
                                <form action="{{ route('arsip.archive', ['document_id' => $risalah->id_risalah, 'jenis_document' => 'Risalah']) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('POST') 
                                    <button type="submit" class="btn btn-sm3">
                                        <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('risalah.edit', $risalah->id_risalah) }}" class="btn btn-sm3">
                                    <img src="/img/risalah/edit.png" alt="edit">
                                </a>
                            @endif
                        @endif
                    <!-- @if ($risalah->status != 'reject' && ($risalah->status != 'approve' || Auth::user()->divisi->id_divisi == $risalah->divisi->id_divisi)) 
                    <a href="{{ route('kirim-risalahAdmin.admin',['id' => $risalah->id_risalah]) }}" class="btn btn-sm1">
                        <img src="/img/memo-admin/share.png" alt="share">
                    </a>               
                    @endif
                    <!-- Status Approve 
                    @if ($risalah->status == 'approve') 
                    <form action="{{ route('arsip.archive', ['document_id' => $risalah->id_risalah, 'jenis_document' => 'Risalah']) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('POST') 
                        <button type="submit" class="btn btn-sm3">
                            <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                        </button>
                    </form>
                    @else
                        <a href="{{ route('risalah.edit', $risalah->id_risalah) }}" class="btn btn-sm3">
                            <img src="/img/memo-admin/edit.png" alt="edit">
                        </a>
                    @endif -->
                    <a href="{{ route('view.risalahAdmin', ['id' => $risalah->id_risalah]) }}" class="btn btn-sm1">
                        <img src="/img/memo-admin/viewBlue.png" alt="view">
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $risalahs->links('pagination::bootstrap-5') }}
    </div>
</div>

    <!-- Modal Add Risalah Sukses -->
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

    <!-- Overlay Edit Risalah Success -->
    <div class="modal fade" id="successEditRisalahModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                    <!-- Success Message -->
                    <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                    <p class="mt-2">Berhasil Mengubah Risalah Rapat</p>
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
                <h5 class="modal-title mb-4"><b>Arsip Risalah Rapat?</b></h5>
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
                    <p class="mt-2">Berhasil Arsip Risalah Rapat</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Event listener untuk modal sukses tambah risalah
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success') === 'Risalah berhasil ditambahkan') // merujuk ke parameter controller risalah store
                var successModal = new bootstrap.Modal(document.getElementById("successAddRisalahModal"));
                successModal.show();
                setTimeout(function () {
                    successModal.hide();
                }, 1500);
            @endif
        });

        // Event listener untuk modal sukses edit risalah
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success') === 'Risalah berhasil diperbarui.') // merujuk ke parameter controller risalah update
                var successEditRisalahModal = new bootstrap.Modal(document.getElementById("successEditRisalahModal"));
                successEditRisalahModal.show();
                setTimeout(function () {
                    successEditRisalahModal.hide();
                }, 1500);
            @endif
        });

        // Event Listener Arsip risalah
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
    </script>
@endsection