@extends('layouts.admin')

@section('title', 'Admin')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{ route('admin.dashboard')}}"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="#" style="color: #565656;">Memo</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
         <div class="surat">
        <div class="header-tools">
            <div class="search-filter">
            <form method="GET" action="{{ route('memo.admin') }}" class="search-filter d-flex gap-2">
                <div class="dropdown">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                        <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="date" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}" onchange="this.form.submit()" placeholder="Tanggal Awal" style="width: 100%;">
                <img src="/img/memo-admin/kalender.png" alt="Kalender Icon" class="input-icon">
            </div>
            <i class="bi bi-arrow-right"></i>
            <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                <input type="date" name="tgl_dibuat_akhir" class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" onchange="this.form.submit()" placeholder="Tanggal Akhir" style="width: 100%;">
                <img src="/img/memo-admin/kalender.png" alt="Kalender Icon" class="input-icon">
            </div>
            <div class="d-flex gap-2">
                <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                    <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()">
                </div>
            </div>
        </form>
                

                        </ul>
                    </div>
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" class="form-control date-placeholder" placeholder="Data Dibuat" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 100%;">
                        <img src="/img/memo-admin/kalender.png" alt="Kalender Icon" class="input-icon">
                    </div>
                    <i class="bi bi-arrow-right"></i>
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" class="form-control date-placeholder" placeholder="Data Keluar" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 100%;">
                        <img src="/img/memo-admin/kalender.png" alt="Kalender Icon" class="input-icon">
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                            <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                            <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
                        </div>
                    </div>

                    <!-- Add User Button to Open Modal -->
                    <a href="{{ route('memo-admin/add')}}" class="btn btn-add">+ Tambah Memo</a>
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
                @foreach ($memos as $index => $memo)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ $memo->status == 'Reject' ? 'text-danger' : ($memo->status == 'Pending' ? 'text-warning' : 'text-success') }}">
                        {{ $memo->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($memo->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $memo->seri_surat }}</td>
                    <td>{{ $memo->nomor_memo }}</td>
                    <td>{{ $memo->tgl_disahkan ? \Carbon\Carbon::parse($memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $memo->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                    <td>
                        @if ($memo->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($memo->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        @if ($memo->status != 'reject') 
                        <a href="{{ route('kirim-memoAdmin.admin',['id' => $memo->id_memo]) }}" class="btn btn-sm1">
                            <img src="/img/memo-admin/share.png" alt="share">
                        </a>               
                        @endif             

                        <!-- Status Approve -->
                        @if ($memo->status == 'approve') 
                        <form action="{{ route('arsip.archive', ['document_id' => $memo->id_memo, 'jenis_document' => 'Memo']) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('POST') <!-- Pastikan metode ini sesuai dengan route -->
                            <button type="submit" class="btn btn-sm4">
                                <img src="/img/memo-superadmin/arsip.png" alt="arsip">
                            </button>
                        </form>
                        @else
                            <a href="{{ route('memo.edit', $memo->id_memo) }}" class="btn btn-sm3">
                                <img src="/img/memo-admin/edit.png" alt="edit">
                            </a>
                        @endif

                        <a href="{{ route('view.memo',$memo->id_memo) }}" class="btn btn-sm1">
                            <img src="/img/memo-admin/viewBlue.png" alt="view">
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $memos->links('pagination::bootstrap-5') }}

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-admin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Hapus Memo?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Cancel</a></button>
                            <form method="POST" action="{{ route('memo.destroy', $memo->id_memo) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                            <button type="submit" class="btn-ok" id="confirmDelete">OK</button>
                            </form>
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
                        <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Menghapus Memo</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn-back" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Kembali</a></button>
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
                        <img src="/img/memo-admin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Arsip Memo?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Cancel</a></button>
                            <button type="button" class="btn-ok" id="confirmArsip">OK</button>
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
                        <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Sukses</b></h5>
                        <h6 class="mb-4" style="font-size: 14px; color: #5B5757;">Berhasil Arsip Memo</h6>
                        <!-- Tombol -->
                        <button type="button" class="btn-back" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tambahkan event listener untuk tombol OK pada modal Hapus
        document.getElementById('confirmDelete').addEventListener('click', function () {
            // Tutup modal Hapus
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.hide();

            // Tampilkan modal Berhasil
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
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

    // Handle status filter
    document.querySelectorAll('.dropdown-item[data-status]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            filterMemosByStatus(status);
        });
    });

    function filterMemosByStatus(status) {
        fetch(`/memo-admin/filter?status=${status}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('tbody');
            const newPagination = doc.querySelector('.pagination');
            
            document.querySelector('tbody').innerHTML = newTableBody.innerHTML;
            document.querySelector('.pagination').innerHTML = newPagination?.innerHTML || '';
        })
        .catch(error => console.error('Error:', error));
    }
    </script>


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
@endsection