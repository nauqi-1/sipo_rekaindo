<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Undangan Rapat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/arsip.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route('superadmin.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="#">Arsip</a>/<a style="color:#565656" href="#">Arsip Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="title d-flex justify-content-between align-items-center mb-3">
            <h5><b>Arsip Memo</b></h5>
            <div class="d-flex gap-2">
                <div class="search">
                    <img src="/img/memo-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari" style="outline: none; box-shadow: none;">
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
                    <td class="nama-dokumen text-success">Undangan Pengesahan</td>
                    <td>21-10-2024</td>
                    <td>1596</td>
                    <td>837.06/REKA/GEN/VII/2024</td>
                    <td>22-10-2024</td>
                    <td>HR & GA</td>
                    <td>
                        <span class="badge bg-success">Diterima</span>
                    </td>
                    <td>
                        <button class="btn btn-sm1"><img src="/img/arsip/unduh.png" alt="unduh"></button>
                        <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/arsip/delete.png" alt="delete">
                        </button>
                        <button class="btn btn-sm3"><img src="/img/arsip/preview.png" alt="preview"></button>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Overlay Delete Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Question Mark Icon -->
                    <img src="/img/user-manage/question_Vector.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
                    <!-- Delete Confirmation Text -->
                    <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus Undangan Rapat?</h5>
                    <!-- Buttons -->
                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="confirmDelete" data-bs-toggle="modal" data-bs-target="#deleteSuccessModal">Oke</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay Confirmation Delete Success -->
    <div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="my-3" style="width: 80px;">
                    <!-- Success Message -->
                    <h5><b>Berhasil Menghapus Undangan Rapat</b></h5>
                    <!-- Back Button -->
                    <button class="btn btn-primary mt-4 px-4 py-2" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>