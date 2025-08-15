@extends('layouts.manager')

@section('title', 'Undangan Rapat')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{ route('manager.dashboard') }}"><img src="/img/undangan/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>
        <div class="row">
            <div class="breadcrumb-wrapper"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="breadcrumb" style="gap: 5px; width: 83%;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>
                </div>
                <form method="GET" action="{{ route('undangan.manager') }}" class="d-flex align-items-center gap-2 mb-3">
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
                    <form method="GET" action="{{ route('undangan.manager', Auth::user()->id) }}"
                        class="d-flex align-items-center gap-2 w-100">
                        <div class="dropdown">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Status</option>
                                <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses
                                </option>
                                <option value="correction" {{ request('status') == 'correction' ? 'selected' : '' }}>
                                    Dikoreksi
                                </option>
                                <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="dropdown" style="gap: 5px; position: relative; width: 180px;">
                            <select name="userid_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Undangan</option>
                                <option value="own" {{ request('userid_filter') == 'own' ? 'selected' : '' }}>Undangan
                                    Keluar
                                </option>
                                <option value="other" {{ request('userid_filter') == 'other' ? 'selected' : '' }}>Undangan
                                    Masuk</option>
                            </select>
                        </div>
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
                        <div class="d-flex">
                            <div class="btn btn-search d-flex align-items-center"
                                style="gap: 5px; position: relative; width: 150px;">
                                <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                                <input type="text" name="search" class="form-control border-0 bg-transparent"
                                    placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()"
                                    style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                    </form>
                    <a href="{{ route('undangan-admin/add') }}" class="btn btn-add">+ <span>Tambah Undangan
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
                    <th>Tanggal Rapat
                        <button class="data-md">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tgl_rapat_diff', 'sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                                style="color:rgb(135, 135, 148); text-decoration: none;">
                                <span class="bi-arrow-down-up"></span>
                            </a>
                        </button>
                    </th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Tanggal Disahkan
                        <button class="data-md">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'undangan.tgl_disahkan', 'sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
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
                @foreach ($undangans as $index => $undangan)
                    @php
                        $undangan = $undangan->undangan;
                        // Cek status untuk user login (final_status)
                        $finalStatus = $undangan->status ?? ($undangan->status ?? '-');
                    @endphp
                    <tr>
                        <td class="nomor">{{ $index + 1 }}</td>
                        <td class="nama-dokumen
                                {{ $undangan->status == 'reject' ? 'text-danger' : ($undangan->status == 'correction' ? 'text-warning' : ($undangan->status == 'approve' ? 'text-success' : '')) }}"
                            style="{{ $undangan->status == 'pending' ? 'color: #0dcaf0;' : '' }}">
                            {{ $undangan->judul }}
                        </td>
                        <td>{{ isset($undangan->tgl_rapat) ? \Carbon\Carbon::parse($undangan->tgl_rapat)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $undangan->seri_surat ?? '-' }}</td>
                        <td>{{ $undangan->nomor_undangan ?? '-' }}</td>
                        <td>{{ isset($undangan->tgl_disahkan) ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $undangan->kode ?? '-' }}</td>
                        <td>
                            @php
                                $isDirektur = Auth::user()->position_id_position == 1;
                            @endphp

                            @if ($isDirektur)
                                @if ($undangan->status == 'approve')
                                    <span class="badge bg-success">Diterima</span>
                                @endif
                            @else
                                @if ($undangan->status == 'reject')
                                    <span class="badge bg-danger">Ditolak</span>
                                @elseif ($undangan->status == 'pending')
                                    <span class="badge bg-info">Diproses</span>
                                @elseif ($undangan->status == 'correction')
                                    <span class="badge bg-warning">Dikoreksi</span>
                                @elseif ($undangan->status == 'approve')
                                    <span class="badge bg-success">Diterima</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($undangan->status == 'approve' || $undangan->status == 'reject')
                                <button type="button" class="btn btn-sm3 arsip-btn"
                                        data-document-id="{{ $undangan->id_undangan }}"
                                        data-jenis-document="Undangan"
                                        data-action="{{ route('arsip.archive', ['document_id' => $undangan->id_undangan, 'jenis_document' => 'Undangan']) }}">
                                    <img src="/img/undangan/arsip.png" alt="arsip">
                                </button>
                            @endif

                            <a class="btn btn-sm3" href="{{ route('view.undangan', ['id' => $undangan->id_undangan]) }}">
                                <img src="/img/undangan/viewBlue.png" alt="view">
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $undangans->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    <!-- Modal Arsip -->
    <div class="modal fade" id="arsipUndanganModal" tabindex="-1" aria-labelledby="arsipUndanganModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
                <h5 class="modal-title mb-4"><b>Arsip Undangan?</b></h5>
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

    <!-- Hidden form for submission -->
    <form id="hiddenArsipForm" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Initializing arsip functionality");

            let currentAction = null;
            let currentDocumentId = null;
            let currentJenisDocument = null;

            // Get modal elements
            const arsipModal = document.getElementById("arsipUndanganModal");
            const successModal = document.getElementById("successArsipUndanganModal");
            const confirmButton = document.getElementById("confirmArsipUndangan");
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
