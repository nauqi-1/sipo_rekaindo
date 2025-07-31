@extends('layouts.admin')

@section('title', 'Risalah Admin')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
        </div>
        <h1>Risalah</h1>
    </div>        
    <div class="row">
    <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Risalah Rapat</a>
            </div>
            <form method="GET" action="{{ route('risalah.backup') }}" class="search-filter d-flex gap-2">
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
            <form method="GET" action="{{ route('risalah.backup') }}" class="search-filter d-flex gap-2">
                <!-- <div class="dropdown">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div> -->
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
                <form id="bulkActionForm" method="POST" action="">
                    @csrf
                <div id="bulkActions" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: 10px;">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                style="padding: 5px 10px; font-size: 14px; height: 32px;" data-bs-toggle="modal" data-bs-target="#restoreRisalahModal">
                                <i class="fa-solid fa-rotate-left me-2"></i> Pulihkan
                            </button>

                            <button type="button" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                style="padding: 5px 10px; font-size: 14px; height: 32px; width: 150px;" data-bs-toggle="modal" data-bs-target="#deleteRisalahModal">
                                <i class="fa-solid fa-trash me-2"></i> Hapus Permanen
                            </button>
                        </div>
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
                    <th>
                        <input type="checkbox" id="selectAll">
                    </th>
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
                    <td>
                        <input type="checkbox" name="selected_ids[]" value="{{ $risalah->id_risalah }}"
                            class="selectItem">
                    </td>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ 'text-danger'}}">
                        {{ $risalah->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $risalah->seri_document }}</td>
                    <td>{{ $risalah->nomor_document }}</td>
                    <td>{{ $risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $risalah->user->department->kode_department ?? $risalah->user->divisi->kode_divisi ?? '-' }}</td>
                    
                        <td>
                           <span class="badge bg-danger">Memulihkan</span>  
                        
                    </td>

                    <td>
                        <button type="button" class="btn btn-sm1 triggerRestoreBtn"
                            data-bs-toggle="modal"
                            data-bs-target="#restoreRisalahModal"
                            data-route="{{ route('risalah.restore', ['id' => $risalah->id_risalah]) }}">
                            <img src="/img/restore.png" alt="restore" style="width: 20px; height: 20px;">
                        </button>

                        <button type="button" class="btn btn-sm2 submitDeleterisalah" data-bs-toggle="modal"
                            data-bs-target="#deleteRisalahModal" data-id="{{ $risalah->id_risalah }}"
                            data-route="{{ route('risalah.forcedestroy', $risalah->id_risalah) }}">
                            <img src="/img/risalah/Delete.png" alt="delete" style="height: 14px;">
                        </button>
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

<!-- Restore Success Modal -->
<div class="modal fade" id="successRestoreRisalahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Pemulihan Risalah Berhasil.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Restore Risalah -->
<div class="modal fade" id="restoreRisalahModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                aria-label="Close"></button>

            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3"
                style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="restoreModalLabel">Pulihkan Dokumen?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                Surat akan dikembalikan ke menu <strong>Risalah</strong>.
            </p>
            <div class="d-flex justify-content-center mt-3">
                <form method="POST" id="restoreRisalahForm">
                    @csrf
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Pulihkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses Hapus -->

<div class="modal fade" id="successDeleteRisalahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Risalah berhasil dihapus permanen.</p>
            </div>
        </div>
    </div>
</div>


<!-- Modal Hapus Risalah -->
<div class="modal fade" id="deleteRisalahModal" tabindex="-1" aria-labelledby="deleteRisalahModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <!-- Close Button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <img src="/img/memo-superadmin/warning.png" alt="Warning Icon" class="mb-3"
                    style="width: 80px; height: 80px;">
                <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus Dokumen?</h5>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    <span class="text-danger"><strong>PERHATIAN:</strong> </span>Surat yang telah dihapus <strong>TIDAK
                        DAPAT</strong> dipulihkan.
                </p>
                <div class="modal-footer border-0">
                    <form id="deleteRisalahForm" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-outline-secondary me-2">Hapus</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
<script>

    document.addEventListener("DOMContentLoaded", function () {
        const selectAllCheckbox = document.getElementById("selectAll");
        const checkboxes = document.querySelectorAll(".selectItem");

        selectAllCheckbox.addEventListener("change", function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    });

    // Ambil semua id dari checkbox yang dicentang
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('input[name="selected_ids[]"]:checked'))
            .map(cb => cb.value);
    }

    // Trigger saat tombol pulihkan diklik
    document.querySelector('[data-bs-target="#restoreRisalahModal"]').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        const form = document.getElementById('restoreRisalahForm');
        form.action = "{{ route('risalah.bulk-restore') }}";

        // Tambahkan input tersembunyi
        form.innerHTML += ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
    });

    // Trigger saat tombol hapus diklik
    document.querySelector('[data-bs-target="#deleteRisalahModal"]').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        const form = document.getElementById('deleteRisalahForm');
        form.action = "{{ route('risalah.bulk-force-delete') }}";

        // Tambahkan input tersembunyi
        form.innerHTML += ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
    });

    document.addEventListener("DOMContentLoaded", function () {
        const restoreModal = document.getElementById('restoreRisalahModal');
        const restoreForm = document.getElementById('restoreRisalahForm');

        // Saat tombol restore diklik, isi action form
        document.querySelectorAll('.triggerRestoreBtn').forEach(button => {
            button.addEventListener('click', function () {
                const route = this.getAttribute('data-route');
                restoreForm.setAttribute('action', route);
            });
        });

        // Tampilkan modal sukses jika session berhasil restore
        @if(session('success_restore'))
            const successModal = new bootstrap.Modal(document.getElementById('successRestoreRisalahModal'));
            successModal.show();
        @endif
    });


    document.addEventListener("DOMContentLoaded", function () {
        const deleteModal = document.getElementById("deleteRisalahModal");
        const deleteForm = document.getElementById("deleteRisalahForm");

        // Atur route saat modal delete dibuka
        deleteModal.addEventListener("show.bs.modal", function (event) {
            const triggerButton = event.relatedTarget;
            const route = triggerButton.getAttribute("data-route");
            deleteForm.setAttribute("action", route);
        });

        // Tampilkan modal sukses jika ada session flash
        @if(session('success_delete'))
            const successModal = new bootstrap.Modal(document.getElementById('successDeleteRisalahModal'));
            successModal.show();
        @endif
    });

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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll('.selectItem');
        const bulkActions = document.getElementById('bulkActions');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                bulkActions.style.display = anyChecked ? 'flex' : 'none';

            });
        });

        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                bulkActions.style.display = anyChecked ? 'flex' : 'none';

            });
        }
    });
</script>
@endsection