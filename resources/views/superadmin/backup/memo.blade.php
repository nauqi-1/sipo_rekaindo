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
            <form method="GET"  class="search-filter d-flex gap-2">
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
                <form method="GET" class="search-filter d-flex gap-2">
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
                        <option value="pilih" {{ !request()->filled('kode') ? 'selected' : '' }}>Semua Divisi</option>
                        @foreach($kode as $k)
                            <option value="{{ $k }}" {{ request('kode') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </form>
                
                <form id="bulkActionForm" method="POST">
                        @csrf
                        <div id="bulkActions" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: 5px;">
                            <div class="d-flex gap-2">
                                <button type="button" formaction="{{ route('memo.bulk-restore') }}"
                                    class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                    style="padding: 5px 10px; font-size: 14px; height: 32px;"
                                    data-bs-toggle="modal" data-bs-target="#restoreMemoModal">
                                    <i class="fa-solid fa-rotate-left me-2"></i> Pulihkan
                                </button>
                                <button type="button" formaction="{{ route('memo.bulk-force-delete') }}"
                                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                    style="padding: 5px 10px; font-size: 14px; height: 32px; width: 150px;"
                                    data-bs-toggle="modal" data-bs-target="#deleteMemoModal">
                                    <i class="fa-solid fa-trash me-2"></i> Hapus Permanen
                                </button>
                            </div>
                        </div>
                        </div>
                </form>
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
                    <button title="Pulihkan" type="button" class="btn btn-sm1 submitRestoreMemo" data-bs-toggle="modal"
                        data-bs-target="#restoreMemoModal" data-id="{{ $memo->id_memo }}"
                        data-route="{{ route('memo.restore-file', $memo->id_memo) }}">
                        <i class="fa-solid fa-rotate-left" style="font-size: 14px;"></i> 
                    </button>
                    
                        <button title="Hapus Permanen" type="button" class="btn btn-sm2 submitDeleteMemo" data-bs-toggle="modal"
                            data-bs-target="#deleteMemoModal" data-id="{{ $memo->id_memo }}"
                            data-route="{{ route('memo.forceDelete', $memo->id_memo) }}">
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

<!-- Restore Success Modal -->
<div class="modal fade" id="successRestoreMemoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Pemulihan Memo Berhasil.</p>
            </div>
        </div>
    </div>
</div>
<!-- Destroy Success Modal -->
<div class="modal fade" id="successDeleteMemoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Memo Berhasil Dihapus.</p>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMemoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <img src="/img/memo-superadmin/warning.png" class="mb-3" style="width: 80px;">
            <h5 class="modal-title mb-4">Hapus Dokumen?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                <span class="text-danger"><strong>PERHATIAN:</strong></span> Surat yang telah dihapus <strong>TIDAK DAPAT</strong> dipulihkan.
            </p>
            
                <form method="POST" id="deleteMemoForm">
                    @csrf
                    @method('DELETE')
                <div class="d-flex justify-content-center mt-3">
                <button type="submit" class="btn btn-danger me-2" id="confirmDeleteBtn" style="padding-inline: 25px;">Hapus</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreMemoModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="restoreModalLabel">Pulihkan Dokumen?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                Surat akan dikembalikan ke menu <strong>Memo</strong>.
            </p>
            <div class="d-flex justify-content-center mt-3">
                <form method="POST" id="restoreMemoForm">
                    @csrf
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Pulihkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Select all checkbox
        document.getElementById("selectAll").addEventListener("change", function () {
            const checkboxes = document.querySelectorAll(".selectItem");
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    document.addEventListener('DOMContentLoaded', function () {
        

        // DELETE MODAL LOGIC
        const deleteMemoModal = document.getElementById('deleteMemoModal');
        const deleteMemoForm = document.getElementById('deleteMemoForm');

        deleteMemoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const route = button.getAttribute('data-route');
            console.log('DELETE route:', route);
            console.log('deleteMemoForm element: ', deleteMemoForm);
            if (deleteMemoForm && route) {
                
                deleteMemoForm.setAttribute('action', route);
            }
        });

        // RESTORE MODAL LOGIC
        const restoreMemoModal = document.getElementById('restoreMemoModal');
        const restoreMemoForm = document.getElementById('restoreMemoForm');

        restoreMemoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const route = button.getAttribute('data-route');
            console.log('RESTORE route:', route);
            console.log('restoreMemoForm element: ', restoreMemoForm);
            if (restoreMemoForm && route) {
                
                restoreMemoForm.setAttribute('action', route);
            }
        });

        // SHOW SUCCESS MODALS
        @if(session('success') === 'Memo terpilih berhasil dihapus permanen.')
            const successDeleteModal = new bootstrap.Modal(document.getElementById("successDeleteMemoModal"));
            successDeleteModal.show();
            setTimeout(() => successDeleteModal.hide(), 1500);
        @endif

        @if(session('success') === 'Memo terpilih berhasil dipulihkan.')
            const successRestoreModal = new bootstrap.Modal(document.getElementById("successRestoreMemoModal"));
            successRestoreModal.show();
            setTimeout(() => successRestoreModal.hide(), 1500);
        @endif
    });
</script>
<script>
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('input[name="selected_ids[]"]:checked'))
            .map(cb => cb.value);
    }

    // Trigger saat tombol pulihkan diklik
    document.querySelector('[data-bs-target="#restoreMemoModal"]').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        const form = document.getElementById('restoreMemoForm');
        form.action = "{{ route('memo.bulk-restore') }}";

        // Tambahkan input tersembunyi
        form.innerHTML += ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
    });

    // Trigger saat tombol hapus diklik
    document.querySelector('[data-bs-target="#deleteMemoModal"]').addEventListener('click', function () {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        const form = document.getElementById('deleteMemoForm');
        form.action = "{{ route('memo.bulk-force-delete') }}";

        // Tambahkan input tersembunyi
        form.innerHTML += ids.map(id => `<input type="hidden" name="selected_ids[]" value="${id}">`).join('');
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
