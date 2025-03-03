@extends('layouts.manager')

@section('title', 'Memo Terkirim')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/memo-supervisor/Vector_back.png" alt="back"></a>
            </div>
            <h1>Memo Terkirim</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Memo Terkirim</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="header-tools">
            <div class="search-filter">
                <div class="dropdown">
                    <button class="btn btn-dropdown dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Status</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.manage', ['sort' => 'asc']) }}" style="justify-content: center; text-align: center;">
                                Diterima
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.manage', ['sort' => 'desc']) }}" style="justify-content: center; text-align: center;">
                                Proses
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.manage', ['sort' => 'desc']) }}" style="justify-content: center; text-align: center;">
                                Ditolak
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                    <input type="text" class="form-control date-placeholder" placeholder="Data Dibuat" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 100%;">
                    <img src="/img/memo-supervisor/kalender.png" alt="Kalender Icon" class="input-icon">
                </div>
                <i class="bi bi-arrow-right"></i>
                <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                    <input type="text" class="form-control date-placeholder" placeholder="Data Keluar" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 100%;">
                    <img src="/img/memo-supervisor/kalender.png" alt="Kalender Icon" class="input-icon">
                </div>
                <div class="d-flex gap-2">
                    <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                        <img src="/img/memo-supervisor/search.png" alt="search" style="width: 20px; height: 20px;">
                        <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead class="table-light">
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
            @foreach ($memoTerkirim as $index => $kirim)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen {{ $kirim->memo->status == 'Reject' ? 'text-danger' : ($kirim->memo->status == 'Pending' ? 'text-warning' : 'text-success') }}">
                    {{ $kirim->memo->judul }}</td>
                    <td>{{ $kirim->memo->tgl_dibuat }}</td>
                    <td>{{ $kirim->memo->seri_surat }}</td>
                    <td>{{ $kirim->memo->nomor_memo }}</td>
                    <td>{{ $kirim->memo->tgl_disahkan ? \Carbon\Carbon::parse($kirim->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $kirim->memo->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    <td>
                        @if ($kirim->memo->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($kirim->memo->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        
                        <!-- <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/memo-supervisor/Delete.png" alt="delete">
                        </button> -->

                        <!-- <a href="{{ route('kirim-memoSuperadmin.superadmin') }}" class="btn btn-sm1">
                            <img src="/img/memo-supervisor/share.png" alt="share">
                        </a> -->

                        <a class="btn btn-sm3" href="{{ route('view.memo-terkirim', $kirim->id_document) }}">
                            <img src="/img/memo-supervisor/viewBlue.png" alt="view">
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-supervisor/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Hapus Memo Terkirim?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="{{route ('memo.terkirim')}}">Cancel</a></button>
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
                        <img src="/img/memo-supervisor/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050; font-size: 20px;"><b>Berhasil Menghapus <br>Memo Terkirim</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn back" data-bs-dismiss="modal"><a href="{{route ('memo.terkirim')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection