@extends('layouts.admin')

@section('title', 'Undangan Rapat')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{Route('admin.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
        </div>
        <h1>Undangan Rapat</h1>
    </div>
    <div class="row">
        <div class="breadcrumb-wrapper"
            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 83%;">
                <a href="{{route('admin.dashboard')}}">Beranda / Pemulihan</a>/<a href="#"
                    style="color: #565656;">Undangan
                    Rapat</a>
            </div>
            <form method="GET" action="{{ route('undangan.backup') }}" class="d-flex align-items-center gap-2 mb-3">
                <label class="d-flex align-items-center" style="font-size: 14px; color: #333; margin-bottom: 0;">
                    <span style="margin-right: 6px;">Show</span>
                    <select name="per_page" onchange="this.form.submit()"
                        style="
                padding: 4px 10px;
                border: 1px solid #ccc;
                border-radius: 6px;
                background-color: #fff;
                color: #333;
                font-size: 14px;
                outline: none;
                appearance: none;
                background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2710%27 height=%275%27%3E%3Cpath fill=%27%23000%27 d=%27M0 0l5 5 5-5z%27/%3E%3C/svg%3E');
                background-repeat: no-repeat;
                background-position: right 8px center;
                background-size: 10px 6px;
                padding-right: 30px;
            ">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span style="margin-left: 6px;">entries</span>
                </label>
            </form>

        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
                <form method="GET" action="{{ route('undangan.backup') }}" class="search-filter d-flex gap-2">
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" id="tgl_dibuat_awal" name="tgl_dibuat_awal"
                            class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}"
                            placeholder="Tanggal Awal" onfocus="this.type='date'"
                            onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Awal'; }"
                            onchange="this.form.submit()">
                    </div>
                    <i class="bi bi-arrow-right"></i>
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" id="tgl_dibuat_akhir" name="tgl_dibuat_akhir"
                            class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}"
                            placeholder="Tanggal Akhir" onfocus="this.type='date'"
                            onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Akhir'; }"
                            onchange="this.form.submit()">
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn btn-search d-flex align-items-center"
                            style="gap: 5px; padding: 4px 8px; height: 32px;">
                            <img src="/img/undangan/search.png" alt="search" style="width: 16px; height: 16px;">
                            <input type="text" name="search" class="form-control border-0 bg-transparent p-0"
                                placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()"
                                style="outline: none; box-shadow: none; width: 100px; font-size: 13px;">
                        </div>
                    </div>
                    <div class="dropdown">
                        <select name="kode" id="kode" class="form-select" onchange="this.form.submit()">
                            <option value="pilih" {{ !request()->filled('kode') ? 'selected' : '' }}>Pilih Divisi
                            </option>
                            @foreach($kode as $k)
                            <option value="{{ $k }}" {{ request('kode') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div id="bulkActions" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3" style="margin-top: 10px;">
                        <div class="d-flex gap-2">
                            <button type="button" id="bulkRestoreBtn"
                                class="btn btn-success btn-sm d-flex align-items-center justify-content-center"
                                style="padding: 5px 10px; font-size: 14px; height: 32px;" data-bs-toggle="modal"
                                data-bs-target="#restoreUndanganModal">
                                <i class="fa-solid fa-rotate-left me-2"></i> Pulihkan
                            </button>

                            <button type="button" id="bulkDeleteBtn"
                                class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                style="padding: 5px 10px; font-size: 14px; height: 32px; width: 150px;"
                                data-bs-toggle="modal" data-bs-target="#deleteUndanganModal">
                                <i class="fa-solid fa-trash me-2"></i> Hapus Permanen
                            </button>
                        </div>
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
                <th>Tanggal Undangan
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc', 'sort_by' => 'tgl_dibuat']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>Tanggal Disahkan
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc', 'sort_by' => 'tgl_disahkan']) }}"
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
                <td>
                    <input type="checkbox" name="selected_ids[]" value="{{ $undangan->id_undangan }}"
                        class="selectItem">
                </td>
                <td class="nomor">{{ $index + 1 }}</td>
                <td class="nama-dokumen 
                            {{ $undangan->status == 'reject' ? 'text-danger' : ($undangan->status == 'correction' ? 'text-warning' : ($undangan->status == 'approve' ? 'text-success' : '')) }}"
                    style="{{ $undangan->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                    {{ $undangan->judul }}
                </td>
                <td>{{ \Carbon\Carbon::parse($undangan->tgl_dibuat)->format('d-m-Y') }}</td>
                <td>{{ $undangan->seri_surat }}</td>
                <td>{{ $undangan->nomor_undangan }}</td>
                <td>{{ $undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $undangan->kode }}</td>
                <td>
                    @if ($undangan->status == 'reject')
                    <span class="badge bg-danger">Ditolak</span>
                    @elseif ($undangan->status == 'pending')
                    <span class="badge bg-info">Diproses</span>
                    @elseif ($undangan->status == 'correction')
                    <span class="badge bg-warning">Dikoreksi</span>
                    @elseif($undangan->status == 'approve')
                    <span class="badge bg-success">Diterima</span>
                    @else
                    <span class="badge bg-success">-</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm1 single-restore"
                        data-id="{{ $undangan->id_undangan }}"
                        data-route="{{ route('undangan.restore', $undangan->id_undangan) }}">
                        <i class="fa-solid fa-rotate-left" style="font-size: 14px;"></i>
                    </button>
                    <button type="button" class="btn btn-sm2 single-delete"
                        data-id="{{ $undangan->id_undangan }}"
                        data-route="{{ route('undangan.forceDelete', $undangan->id_undangan) }}">
                        <i class="fa-solid fa-trash" style="color: red; font-size: 14px;"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $undangans->links('pagination::bootstrap-5') }}
</div>

<!-- Restore Success Modal -->
<div class="modal fade" id="successRestoreUndanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Pemulihan Undangan Berhasil.</p>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreUndanganModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                aria-label="Close"></button>
            <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3"
                style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="restoreModalLabel">Pulihkan Dokumen?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                Surat akan dikembalikan ke menu <strong>Undangan</strong>.
            </p>
            <div class="d-flex justify-content-center mt-3">
                <form method="POST" id="restoreUndanganForm">
                    @csrf
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Pulihkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Success Modal -->
<div class="modal fade" id="successDeleteUndanganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <img src="/img/memo-admin/success.png" alt="Success Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title"><b>Sukses</b></h5>
                <p class="mt-2">Undangan Berhasil Dihapus.</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUndanganModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                aria-label="Close"></button>
            <img src="/img/memo-superadmin/warning.png" alt="Warning Icon" class="mb-3"
                style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus Dokumen?</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">
                <span class="text-danger"><strong>PERHATIAN:</strong> </span>Surat yang telah dihapus <strong>TIDAK
                    DAPAT</strong> dipulihkan.
            </p>
            <form method="POST" id="deleteUndanganForm">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-danger me-2" style="padding-inline: 25px;">Hapus</button>
            </form>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helper function to get selected IDs
        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.selectItem:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        // Helper function to show alert
        function showAlert(message, type = 'warning') {
            alert(message); // You can replace this with a better notification system
        }

        // Select all checkbox functionality
        const selectAllCheckbox = document.getElementById("selectAll");
        const itemCheckboxes = document.querySelectorAll(".selectItem");

        selectAllCheckbox.addEventListener("change", function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
        });

        // Individual checkbox change handler
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.selectItem:checked').length;
                selectAllCheckbox.checked = checkedCount === itemCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
            });
        });

        // Bulk restore button handler
        document.getElementById('bulkRestoreBtn').addEventListener('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showAlert('Pilih minimal satu item untuk dipulihkan!');
                return false;
            }

            const form = document.getElementById('restoreUndanganForm');
            form.action = "{{ route('undangan.bulk-restore') }}";

            // Clear existing hidden inputs
            form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());

            // Add selected IDs as hidden inputs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids[]';
                input.value = id;
                form.appendChild(input);
            });
        });

        // Bulk delete button handler
        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showAlert('Pilih minimal satu item untuk dihapus!');
                return false;
            }

            const form = document.getElementById('deleteUndanganForm');
            form.action = "{{ route('undangan.bulk-force-delete') }}";

            // Clear existing hidden inputs
            form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());

            // Add selected IDs as hidden inputs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids[]';
                input.value = id;
                form.appendChild(input);
            });
        });

        // Single restore button handlers
        document.querySelectorAll('.single-restore').forEach(button => {
            button.addEventListener('click', function() {
                const route = this.getAttribute('data-route');
                const form = document.getElementById('restoreUndanganForm');

                // Clear existing hidden inputs
                form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());

                // Set form action for single restore
                form.action = route;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('restoreUndanganModal'));
                modal.show();
            });
        });

        // Single delete button handlers
        document.querySelectorAll('.single-delete').forEach(button => {
            button.addEventListener('click', function() {
                const route = this.getAttribute('data-route');
                const form = document.getElementById('deleteUndanganForm');

                // Clear existing hidden inputs
                form.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());

                // Set form action for single delete
                form.action = route;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('deleteUndanganModal'));
                modal.show();
            });
        });

        // Show success modals based on session messages
        @if(session('success') === 'Undangan berhasil dihapus permanen.')
        const successDeleteModal = new bootstrap.Modal(document.getElementById("successDeleteUndanganModal"));
        successDeleteModal.show();
        setTimeout(function() {
            successDeleteModal.hide();
        }, 1500);
        @endif

        @if(session('success') === 'Pemulihan Undangan Berhasil.')
        const successRestoreModal = new bootstrap.Modal(document.getElementById("successRestoreUndanganModal"));
        successRestoreModal.show();
        setTimeout(function() {
            successRestoreModal.hide();
        }, 1500);
        @endif
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
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