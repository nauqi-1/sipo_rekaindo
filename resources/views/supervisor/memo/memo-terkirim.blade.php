<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Terkirim Supervisor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supervisor/memo-terkirim.css') }}">
</head>
<body>
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
                @for ($i = 1; $i <= 3; $i++)
                <tr>
                    <td class="nomor">{{ $i }}</td>
                    <td class="nama-dokumen {{ $i % 3 == 0 ? 'text-danger' : ($i % 2 == 0 ? 'text-warning' : 'text-success') }}">Memo Monitoring Risiko</td>
                    <td>21-10-2024</td>
                    <td>1596</td>
                    <td>837.06/REKA/GEN/VII/2024</td>
                    <td>22-10-2024</td>
                    <td>HR & GA</td>
                    <td>
                        @if ($i % 3 == 0)
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($i % 2 == 0)
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        <!-- <a href="{{ route('kirim-memoSuperadmin.superadmin') }}" class="btn btn-sm1"> -->
                        <a href="#" class="btn btn-sm1">
                            <img src="/img/memo-supervisor/share.png" alt="share">
                        </a>
                        <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/memo-supervisor/Delete.png" alt="delete">
                        </button>
                        <button class="btn btn-sm3">
                            <img src="/img/memo-supervisor/viewBlue.png" alt="view">
                        </button>
                    </td>
                </tr>
                @endfor
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
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal"><a href="{{route ('memo.terkirim')}}">Cancel</a></button>
                            <button type="button" class="btn-ok" id="confirmDelete">OK</button>
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
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Menghapus <br>Memo Terkirim</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn-back" data-bs-dismiss="modal"><a href="{{route ('memo.terkirim')}}">Kembali</a></button>
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

    // Tambahkan event listener untuk tombol OK pada modal Arsip
    document.getElementById('confirmArsip').addEventListener('click', function () {
        // Tutup modal Arsip
        const arsipModal = new bootstrap.Modal(document.getElementById('arsipModal'));
        arsipModal.hide();

        // Tampilkan modal Berhasil
        const successArsipModal = new bootstrap.Modal(document.getElementById('successArsipModal'));
        successArsipModal.show();
    });

    </script>


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>