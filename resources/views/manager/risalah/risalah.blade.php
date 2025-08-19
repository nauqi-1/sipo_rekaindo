@extends('layouts.manager')

@section('title', 'Risalah Manager')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{ route('manager.dashboard') }}"><img src="/img/undangan/Vector_back.png" alt=""></a>
            </div>
            <h1>Risalah Rapat</h1>
        </div>
        <div class="row">
            <div class="breadcrumb-wrapper"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="breadcrumb" style="gap: 5px; width: 83%;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Risalah Rapat</a>
                </div>
                <form method="GET" action="{{ route('risalah.manager') }}" class="d-flex gap-2">
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
                    <form method="GET" action="{{ route('risalah.manager') }}"
                        class="d-flex align-items-center gap-3 flex-wrap w-100">


                        <!-- <div class="dropdown">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Status</option>
                                <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                                <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div> -->
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
                            <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                                <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                                <input type="text" name="search" class="form-control border-0 bg-transparent"
                                    placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()"
                                    style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                    </form>
                    <a href="{{ route('add-risalah.manager') }}" class="btn btn-add">+ <span>Tambah Risalah
                            Rapat</span></a>
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
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'risalah.tgl_dibuat', 'sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                    </th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Tanggal Disahkan
                        <button class="data-md">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'risalah.tgl_disahkan', 'sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                                style="color: rgb(135, 135, 148); text-decoration: none;">
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
                @foreach ($risalahs as $index => $risalah)
                    <tr>
                        <td class="nomor">{{ $index + 1 }}</td>
                        <td class="nama-dokumen
                        {{ $risalah->status == 'reject' ? 'text-danger' : ($risalah->status == 'correction' ? 'text-warning' : ($risalah->status == 'approve' ? 'text-success' : '')) }}"
                            style="{{ $risalah->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                            {{ Str::limit($risalah->risalah->judul ? $risalah->risalah->judul : '-', 35, '...') }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($risalah->risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                        <td>{{ $risalah->risalah->seri_surat }}</td>
                        <td>{{ $risalah->risalah->nomor_risalah }}</td>
                        <td>{{ $risalah->risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $risalah->risalah->user->department->kode_department ?? ($risalah->user->divisi->kode_divisi ?? '-') }}
                        </td>
                        </td>
                        <td>
                            @if ($risalah->status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($risalah->status == 'pending')
                                <span class="badge bg-info">Diproses</span>
                            @elseif ($risalah->status == 'correction')
                                <span class="badge bg-warning">Dikoreksi</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        </td>
                        <td>
                            @if ($risalah->status == 'approve' || $risalah->status == 'reject')
                                <button type="button" class="btn btn-sm3 arsip-btn"
                                    data-document-id="{{ $risalah->risalah->id_risalah }}" data-jenis-document="Risalah"
                                    data-action="{{ route('arsip.archive', ['document_id' => $risalah->risalah->id_risalah, 'jenis_document' => 'Risalah']) }}">
                                    <img src="/img/undangan/arsip.png" alt="arsip">
                                </button>
                            @endif
                            <a href="{{ route('persetujuan.risalah', ['id' => $risalah->risalah->id_risalah]) }}"
                                class="btn btn-sm1">
                                <!-- <img src="/img/undangan/share.png" alt="share"> -->
                                <img src="/img/undangan/viewBlue.png" alt="view">
                            </a>
                            <!-- <a class="btn btn-sm3" href="{{ route('view.risalah', ['id' => $risalah->risalah->id_risalah]) }}">

                                <img src="/img/undangan/viewBlue.png" alt="view">
                            </a> -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $risalahs->links('pagination::bootstrap-5') }}
    </div>

    <!-- Modal Add Risalah Sukses -->
    <div class="modal fade" id="successAddRisalahModal" tabindex="-1" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3"
                        style="width: 80px; height: 80px;">
                    <!-- Success Message -->
                    <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                    <p class="mt-2">Berhasil Menambahkan Risalah Rapat</p>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Arsip -->
    <div class="modal fade" id="arsipRisalahModal" tabindex="-1" aria-labelledby="arsipRisalahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title mb-4"><b>Arsip Risalah?</b></h5>
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

    <!-- Hidden form for submission -->
    <form id="hiddenArsipForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="document_id" id="hiddenDocumentId">
    <input type="hidden" name="jenis_document" id="hiddenJenisDocument">
    </form>

    <script>
        // Event listener untuk modal sukses tambah risalah
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success') === 'Risalah berhasil ditambahkan') // merujuk ke parameter controller risalah store
                var successModal = new bootstrap.Modal(document.getElementById("successAddRisalahModal"));
                successModal.show();
                setTimeout(function() {
                    successModal.hide();
                }, 1500);
            @endif
        });

        // Event listener untuk modal sukses edit risalah
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success') === 'Risalah berhasil diperbarui.') // merujuk ke parameter controller risalah update
                var successEditRisalahModal = new bootstrap.Modal(document.getElementById(
                    "successEditRisalahModal"));
                successEditRisalahModal.show();
                setTimeout(function() {
                    successEditRisalahModal.hide();
                }, 1500);
            @endif
        });

        document.addEventListener("DOMContentLoaded", function() {
            console.log("Initializing arsip functionality");

            let currentAction = null;
            let currentDocumentId = null;
            let currentJenisDocument = null;

            // Get modal elements
            const arsipModal = document.getElementById("arsipRisalahModal");
            const successModal = document.getElementById("successArsipRisalahModal");
            const confirmButton = document.getElementById("confirmArsipRisalah");
            const hiddenForm = document.getElementById("hiddenArsipForm");

            if (!arsipModal || !successModal || !confirmButton || !hiddenForm) {
                console.error("Required modal elements not found");
                return;
            }

            // Initialize Bootstrap modals
            const arsipModalInstance = new bootstrap.Modal(arsipModal);
            const successModalInstance = new bootstrap.Modal(successModal);

            // Handle arsip button clicks
            document.addEventListener('click', function(e) {
                if (e.target.closest('.arsip-btn')) {
                    e.preventDefault();

                    const button = e.target.closest('.arsip-btn');
                    currentAction = button.getAttribute('data-action');
                    currentDocumentId = button.getAttribute('data-document-id');
                    currentJenisDocument = button.getAttribute('data-jenis-document');

                    console.log('Arsip button clicked:', {
                        action: currentAction,
                        documentId: currentDocumentId,
                        jenisDocument: currentJenisDocument
                    });

                    arsipModalInstance.show();
                }
            });

            // Handle confirm button
            confirmButton.addEventListener('click', function() {
                console.log('Confirm button clicked');

                if (currentAction && hiddenForm) {
                    // Set form action
                    hiddenForm.action = currentAction;

                    console.log('Submitting to:', currentAction);

                    // Hide confirmation modal
                    arsipModalInstance.hide();

                    // Show success modal
                    setTimeout(() => {
                        successModalInstance.show();

                        // Auto-hide and submit
                        setTimeout(() => {
                            successModalInstance.hide();
                            setTimeout(() => {
                                hiddenForm.submit();
                            }, 300);
                        }, 1500);
                    }, 300);
                }
            });

            // Session messages
            @if (session('success'))
                console.log("Success:", "{{ session('success') }}");
            @endif

            @if (session('error'))
                console.log("Error:", "{{ session('error') }}");
            @endif

            @if (session('warning'))
                console.log("Warning:", "{{ session('warning') }}");
            @endif
        });
    </script>
@endsection
