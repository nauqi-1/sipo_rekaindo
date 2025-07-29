@extends('layouts.admin')
@section('title', 'Memo Backup')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
        </div>
        <h1>Pemulihan Memo</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Pemulihan Memo</a>
            </div>
            <form method="GET" action="{{ route('memo.backup') }}" class="search-filter d-flex gap-2">
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
                <form method="GET" action="{{ route('memo.backup') }}" class="search-filter d-flex gap-2">
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
                    <select name="kode" id="kode" class="form-select" onchange="this.form.submit()">
                        <option value="pilih" {{ !request()->filled('kode') ? 'selected' : '' }}>Pilih Divisi</option>
                        @foreach($kode as $k)
                            <option value="{{ $k }}" {{ request('kode') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </form>
                <form id="bulkActionForm" method="POST" action="">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: 10px;">
                            <div class="d-flex gap-2">
                                <button type="submit" formaction="{{ route('memo.bulk-restore') }}"
                                    class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                    style="padding: 5px 10px; font-size: 14px; height: 32px;">
                                    <i class="fa-solid fa-rotate-left me-2"></i> Pulihkan
                                </button>

                                <button type="submit" formaction="{{ route('memo.bulk-force-delete') }}"
                                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                    style="padding: 5px 10px; font-size: 14px; height: 32px; width: 150px;">
                                    <i class="fa-solid fa-trash me-2"></i> Hapus Permanen
                                </button>
                            </div>
                        </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table-light">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="selectAll">
                </th>
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
                <td>
                    <input type="checkbox" name="selected_ids[]" value="{{ $memo->id_memo }}" class="selectItem">
                </td>
                <td class="nomor">{{ $index + 1 }}</td>
                <td class="nama-dokumen 
                        {{ $memo->status == 'reject' ? 'text-danger' : ($memo->status == 'correction' ? 'text-warning' : ($memo->status == 'approve' ? 'text-success' : '')) }}"
                             style="{{ $memo->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                        {{ $memo->judul }}
                    </td>
                <td>{{ \Carbon\Carbon::parse($memo->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $memo->seri_surat }}</td>
                <td>{{ $memo->nomor_memo }}</td>
                <td>{{ $memo->tgl_disahkan ? \Carbon\Carbon::parse($memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $memo->kode ?? 'No Divisi Assigned' }}</td>
                <td>
                        @if ($memo->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($memo->status == 'pending')
                            <span class="badge bg-info">Diproses</span>
                        @elseif ($memo->status == 'correction')
                            <span class="badge bg-warning">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                <td>
                    <button type="button" class="btn btn-sm1 submitRestoreMemo" data-bs-toggle="modal"
                        data-bs-target="#restoreMemoModal" data-id="{{ $memo->id_memo }}"
                        data-route="{{ route('memo.restore-file', $memo->id_memo) }}">
                        <i class="fa-solid fa-rotate-left" style="font-size: 14px;"></i> 
                    </button>
                    <button type="button" class="btn btn-sm2 submitDeleteMemo" data-bs-toggle="modal"
                        data-bs-target="#deleteMemoModal" data-id="{{ $memo->id_memo }}"
                        data-route="{{ route('memo.destroy', $memo->id_memo) }}">
                        <i class="fa-solid fa-trash" style="color: red; font-size: 14px;"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $memos->appends(request()->query())->links('pagination::bootstrap-5') }}
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

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreMemoModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="restoreModalLabel">Pulihkan memo?</h5>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-center mt-3">
                <form method="POST" id="restoreMemoForm">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-outline-secondary me-2">Oke</button>
                </form>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Memo Modal -->
<div class="modal fade" id="deleteMemoModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="deleteMemoForm">
      @csrf
      @method('DELETE')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Hapus Permanen Memo?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin menghapus memo ini secara permanen?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const restoreModal = document.getElementById('restoreMemoModal');
        const restoreForm = document.getElementById('restoreMemoForm');

        restoreModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const route = button.getAttribute('data-route');
                    console.log("Restore route set to:", route);

            if (restoreForm && route) {
                restoreForm.setAttribute('action', route);
            }
        });
    

        const deleteButtons = document.querySelectorAll(".submitDeleteMemo");
        const deleteBtnOk = document.getElementById("openConfirmDeleteBtn");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const route = button.getAttribute("data-route");
                deleteBtnOk.setAttribute("data-route", route);
            });
        });

        // Optional: if you're submitting the delete via JS
       
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') === 'Pemulihan Memo Berhasil.')
            var successModal = new bootstrap.Modal(document.getElementById("successRestoreMemoModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 1500);
        @endif
    });
</script>

<script>
  

    // Event listener untuk modal sukses edit memo
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success') == 'Dokumen berhasil diubah.') // merujuk ke parameter controller memo update
            var successModal = new bootstrap.Modal(document.getElementById("successEditMemoSuperModal"));
            successModal.show();
            setTimeout(function () {
                successModal.hide();
            }, 1500);
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
    document.getElementById("selectAll").addEventListener("change", function () {
        const checkboxes = document.querySelectorAll(".selectItem");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection