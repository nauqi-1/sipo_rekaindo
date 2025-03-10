@extends('layouts.admin')

@section('title', 'Undangan Rapat')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{Route('admin.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">

                    <a href="{{route('admin.dashboard')}}">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>

                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
            <form method="GET" action="{{ route('undangan.admin') }}" class="search-filter d-flex gap-2">
                <div class="dropdown">
                <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Status</option>
                        <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="date" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}" onchange="this.form.submit()" placeholder="Tanggal Awal" style="width: 100%;">
                </div>
                <i class="bi bi-arrow-right"></i>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="date" name="tgl_dibuat_akhir" class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" onchange="this.form.submit()" placeholder="Tanggal Akhir" style="width: 100%;">
                </div>
                <div class="d-flex gap-2">
                    <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                        <img src="/img/undangan/search.png" alt="search" style="width: 20px; height: 20px;">
                        <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
                    </div>
                </div>
                </form>

                <!-- Add User Button to Open Modal -->
                <a href="{{route ('undangan-admin/add')}}" class="btn btn-add">+ <span>Tambah Undangan Rapat</span></a>
            </div>
        </div>
        </div>

        <!-- Table -->
        <table class="table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Data Masuk
                        <button class="data-md">
                            <a href="" style="color:rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                        </button>
                    </th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Data Disahkan
                        <button class="data-md">
                            <a href="" style="color: rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
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
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ $undangan->status == 'reject' ? 'text-danger' : ($undangan->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $undangan->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($undangan->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td >{{ $undangan->seri_surat }}</td>
                    <td>{{ $undangan->nomor_undangan }}</td>
                    <td>{{ $undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $undangan->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                    <td>
                        @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route ('kirim-undanganAdmin.admin',['id' => $undangan->id_undangan])}}" class="btn btn-sm1">
                            <img src="/img/undangan/share.png" alt="share">
                        </a>
                        <!-- <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/undangan/Delete.png" alt="delete">
                        </button> -->
                        <!-- Status Approve -->
                        @if ($undangan->status == 'approve')
                        <form action="{{ route('arsip.archive', ['document_id' => $undangan->id_undangan, 'jenis_document' => 'Undangan']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST') 
                            <button class="btn btn-sm3" data-bs-toggle="modal" data-bs-target="#arsipModal">
                                <img src="/img/undangan/arsip.png" alt="arsip">
                            </button>
                        </form>
                        @else
                            <a href="{{route ('undangan.edit',$undangan->id_undangan)}}" class="btn btn-sm3">
                                <img src="/img/undangan/edit.png" alt="edit">
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $undangans->links('pagination::bootstrap-5') }}

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/undangan/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Hapus Undangan Rapat?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('undangan.admin')}}">Batal</a></button>
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
                        <img src="/img/undangan/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Undangan Rapat</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('undangan.admin')}}">Kembali</a></button>
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
                        <img src="/img/undangan/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Arsip Undangan Rapat?</b></h5>
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
                        <img src="/img/undangan/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Sukses</b></h5>
                        <h6 class="mb-4" style="font-size: 14px; color: #5B5757;">Berhasil Arsip Undangan Rapat</h6>
                        <!-- Tombol -->
                        <button type="button" class="btn back" data-bs-dismiss="modal"><a href="#">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection