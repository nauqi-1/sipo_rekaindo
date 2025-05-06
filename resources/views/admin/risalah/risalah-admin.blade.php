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
                <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Memo</a>
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
                <a href="{{route ('add-risalah.admin')}}" class="btn btn-add">+ <span>Tambah Risalah</span></a>
            </div>
        </div>
    </div>

        <!-- Table -->
        <table class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>
                    <button class="data-md">
                        <a href="{{ request()->fullUrlWithQuery(['sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                    </button>
                </th>
                <th>Seri</th>
                <th>Dokumen</th>
                <th>
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
                <td>{{ $index + 1 }}</td>
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
                                    <button type="submit" class="btn btn-sm3">
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

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/risalah/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Hapus Risalah Rapat?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('risalah.admin')}}">Batal</a></button>
                            <button type="button" class="btn ok" id="confirmDelete">Oke</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/risalah/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Risalah Rapat</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('risalah.admin')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Arsip -->
        <div class="modal fade" id="arsipModal" tabindex="-1" aria-labelledby="arsipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/risalah/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Arsip Risalah Rapat?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="#">Batal</a></button>
                            <button type="button" class="btn ok" id="confirmArsip">Oke</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Arsip Berhasil -->
        <div class="modal fade" id="successArsipModal" tabindex="-1" aria-labelledby="successArsipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/risalah/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Sukses</b></h5>
                        <h6 class="mb-4" style="font-size: 14px; color: #5B5757;">Berhasil Arsip Risalah Rapat</h6>
                        <!-- Tombol -->
                        <button type="button" class="btn back" data-bs-dismiss="modal"><a href="#">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirmDelete').addEventListener('click', function () {
            // Ambil referensi modal
            const deleteModalEl = document.getElementById('deleteModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            
            // Tutup modal Hapus terlebih dahulu
            deleteModal.hide();
            
            // Pastikan modal benar-benar tertutup sebelum membuka modal berikutnya
            deleteModalEl.addEventListener('hidden.bs.modal', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, { once: true }); // Tambahkan event listener hanya sekali
        });

        document.getElementById('confirmArsip').addEventListener('click', function () {
            // Ambil referensi modal
            const deleteModalEl = document.getElementById('arsipModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            
            // Tutup modal Hapus terlebih dahulu
            deleteModal.hide();
            
            // Pastikan modal benar-benar tertutup sebelum membuka modal berikutnya
            deleteModalEl.addEventListener('hidden.bs.modal', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successArsipModal'));
                successModal.show();
            }, { once: true }); // Tambahkan event listener hanya sekali
        });
    </script>


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
@endsection