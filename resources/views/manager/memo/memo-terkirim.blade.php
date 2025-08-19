@extends('layouts.manager')

@section('title', 'Memo Keluar')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="#"><img src="/img/memo-supervisor/Vector_back.png" alt="back"></a>
        </div>
        <h1>Memo Keluar</h1>
    </div>
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Memo Keluar</a>
            </div>
            <form method="GET" action="{{ route('memo.terkirim', Auth::user()->id) }}" class="d-flex gap-2">
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
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="tgl_dibuat_awal" value="{{ request('tgl_dibuat_awal') }}">
                <input type="hidden" name="tgl_dibuat_akhir" value="{{ request('tgl_dibuat_akhir') }}">
                <input type="hidden" name="page" value="{{ request('page') }}">
            </form>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
                <form method="GET" action="{{ route('memo.terkirim', Auth::user()->id) }}" class="d-flex align-items-center gap-3 w-100">
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    <div class="dropdown">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                            <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                            <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                            <option value="correction" {{ request('status') == 'correction' ? 'selected' : '' }}>Dikoreksi</option>
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
                <a href="{{route ('memo-manager/add')}}" class="btn btn-add">+ <span>Tambah Memo</span></a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table-light">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Tanggal memo
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'memo.tgl_dibuat','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tanggal Disahkan
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'memo.tgl_disahkan','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                    </button>
                </th>
                <th>Pengirim</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($memoTerkirim as $index => $kirim)
            <tr>
                <td class="nomor">{{ $index + 1 }}</td>
                @if (Auth::user()->divisi_id_divisi == $kirim->memo->divisi_id_divisi)
               <td class="nama-dokumen 
                        {{ $kirim->memo->status == 'reject' ? 'text-danger' : ($kirim->memo->status == 'correction' ? 'text-warning' : ($kirim->memo->status == 'approve' ? 'text-success' : '')) }}"
                    style="{{ $kirim->memo->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                    {{ Str::limit($kirim->memo->judul, 35, '...') }}
                </td>
                @else
                <td class="nama-dokumen {{ ($kirim->status == 'reject' || $kirim->status == 'correction') ? 'text-danger' : ($kirim->status == 'pending' ? '' : 'text-success') }}"
                    style="{{ $kirim->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                    {{ Str::limit($kirim->memo->judul, 35, '...') }}
                </td>
                @endif

                <!-- <td>{{ $kirim->memo->tgl_dibuat }}</td> -->
                <td>{{ $kirim->memo->tgl_dibuat ? \Carbon\Carbon::parse($kirim->memo->tgl_dibuat)->format('d-m-Y') : '-' }}</td>
                <td>{{ $kirim->memo->seri_surat }}</td>
                <td>{{ $kirim->memo->nomor_memo }}</td>
                <td>{{ $kirim->memo->tgl_disahkan ? \Carbon\Carbon::parse($kirim->memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $kirim->memo->kode ?? '-' }}</td>
                <td>
                    @if(Auth::user()->divisi_id_divisi == $kirim->memo->divisi_id_divisi)
                    @if ($kirim->memo->status == 'reject')
                    <span class="badge bg-danger">Ditolak</span>
                    @elseif ($kirim->memo->status == 'pending')
                    <span class="badge bg-info">Diproses</span>
                    @elseif ($kirim->memo->status == 'correction')
                    <span class="badge bg-warning">Dikoreksi</span>
                    @else
                    <span class="badge bg-success">Diterima</span>
                    @endif
                    @else
                    @if ($kirim->status == 'reject')
                    <span class="badge bg-danger">Ditolak</span>
                    @elseif ($kirim->status == 'pending')
                    <span class="badge bg-info">Diproses</span>
                    @elseif ($kirim->status == 'correction')
                    <span class="badge bg-warning">Dikoreksi</span>
                    @else
                    <span class="badge bg-success">Diterima</span>
                    @endif
                    @endif
                </td>
                <td>
                    @if($kirim->memo->status == 'pending')
                    <a class="btn btn-sm3" href="{{ route('view.memo-diterima', $kirim->id_document) }}">
                        <img src="/img/memo-supervisor/viewBlue.png" alt="view">
                    </a>
                    @else
                    <form action="{{ route('arsip.archive', ['document_id' => $kirim->memo->id_memo, 'jenis_document' => 'Memo']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST') <!-- Pastikan metode ini sesuai dengan route -->
                        <button type="submit" class="btn btn-sm3 submitArsipMemo">
                            <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                        </button>
                    </form>
                    <a class="btn btn-sm3" href="{{ route('view.memo-terkirim', $kirim->id_document) }}">
                        <img src="/img/memo-supervisor/viewBlue.png" alt="view">
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $memoTerkirim->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>

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

<!-- Overlay Edit Memo Success -->
<div class="modal fade" id="successEditMemoModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengubah Memo</p>
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
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success') === 'Dokumen berhasil dibuat.') // merujuk ke parameter controller memo store
        var successModal = new bootstrap.Modal(document.getElementById("successAddMemoModal"));
        successModal.show();
        setTimeout(function() {
            successModal.hide();
        }, 1500);
        @endif
    });

    // Event listener untuk modal sukses edit memo
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success') === 'User updated successfully') // merujuk ke parameter controller memo update
        var successModal = new bootstrap.Modal(document.getElementById("successEditMemoModal"));
        successModal.show();
        setTimeout(function() {
            successModal.hide();
        }, 1500);
        @endif
    });

    // Event Listener Arsip Memo
    document.addEventListener("DOMContentLoaded", function() {
        const arsipButtons = document.querySelectorAll(".submitArsipMemo");
        const confirmArsipButton = document.getElementById("confirmArsipMemo");
        const cancelArsipButton = document.querySelector("#arsipMemoModal .btn-outline-secondary");
        const arsipMemoModal = new bootstrap.Modal(document.getElementById("arsipMemoModal"));
        const successArsipMemoModal = new bootstrap.Modal(document.getElementById("successArsipMemoModal"));

        let currentForm = null;

        // Saat tombol arsip ditekan, simpan form yang akan dikirim
        arsipButtons.forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault(); // Mencegah submit langsung
                currentForm = this.closest("form");
                arsipMemoModal.show(); // Tampilkan modal konfirmasi
            });
        });

        // Saat tombol "Batal" ditekan, tutup modal konfirmasi
        cancelArsipButton.addEventListener("click", function() {
            arsipMemoModal.hide();
        });

        // Saat tombol "OK" ditekan, submit form dan tampilkan modal sukses
        confirmArsipButton.addEventListener("click", function() {
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