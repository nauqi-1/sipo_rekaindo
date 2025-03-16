@extends('layouts.admin')

@section('title', 'Memo Admin')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
        </div>
        <h1>Memo</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Memo</a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
            <form method="GET" action="{{ route('memo.admin') }}" class="search-filter d-flex gap-2">
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
                </form>
                <!-- Add User Button to Open Modal -->
                <a href="{{route ('memo-admin/add')}}" class="btn btn-add">+ <span>Tambah Memo</span></a>
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
                @foreach ($memos as $index => $memo)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    @if (Auth::user()->divisi->id_divisi == $memo->divisi->id_divisi)
                    <td class="nama-dokumen 
                        {{ $memo->status == 'reject' ? 'text-danger' : ($memo->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $memo->judul }}
                    </td>
                    @else
                    <td class="nama-dokumen 
                        {{ $status == 'reject' ? 'text-danger' : ($status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $memo->judul }}
                    </td>
                    @endif
                    <td>{{ \Carbon\Carbon::parse($memo->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $memo->seri_surat }}</td>
                    <td>{{ $memo->nomor_memo }}</td>
                    <td>{{ $memo->tgl_disahkan ? \Carbon\Carbon::parse($memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $memo->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                    <td>
                        
                        @if (Auth::user()->divisi->id_divisi == $memo->divisi->id_divisi)
                            @if ($memo->status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($memo->status  == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        @else
                            @if ($status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($status == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        @endif
                    </td>

                    <td>
                    @if ($status!= 'reject' && ($status != 'approve' || Auth::user()->divisi->id_divisi == $memo->divisi->id_divisi)) 
                        <a href="{{ route('kirim-memoAdmin.admin',['id' => $memo->id_memo]) }}" class="btn btn-sm1">
                            <img src="/img/memo-admin/share.png" alt="share">
                        </a>               
                        @endif             

                        <!-- Status Approve -->
                        @if ($status== 'approve') 
                        <form action="{{ route('arsip.archive', ['document_id' => $memo->id_memo, 'jenis_document' => 'Memo']) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('POST') <!-- Pastikan metode ini sesuai dengan route -->
                            <button type="submit" class="btn btn-sm3">
                                <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                            </button>
                        </form>
                        @else
                            <a href="{{ route('memo.edit', $memo->id_memo) }}" class="btn btn-sm3">
                                <img src="/img/memo-admin/edit.png" alt="edit">
                            </a>
                        @endif

                        <a href="{{ route('view.memo',$memo->id_memo) }}" class="btn btn-sm1">
                            <img src="/img/memo-admin/viewBlue.png" alt="view">
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $memos->links('pagination::bootstrap-5') }}

<!-- Overlay Add Memo Success -->
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
                <button type="button" class="btn btn-primary" id="confirmArsipMemo">Oke</button>
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
    // Event listener untuk modal sukses tambah memo
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller memo store
            var successModal = new bootstrap.Modal(document.getElementById("successAddMemoModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 2000);
        @endif
    });

    // Event Listener Arsip Memo
    document.addEventListener("DOMContentLoaded", function () {
        const arsipButtons = document.querySelectorAll(".submitArsipMemo");
        const confirmArsipButton = document.getElementById("confirmArsipMemo");
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
</script>
@endsection