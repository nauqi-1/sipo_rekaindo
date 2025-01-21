<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Rapat Super Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/undangan.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route('superadmin.dashboard')}}"><img src="/img/undangan-superadmin/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a>/<a href="#">Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <select class="form-select" style="width: 150px;">
                    <option>Status</option>
                    <option>Approve</option>
                    <option>Reject</option>
                    <option>Pending</option>
                </select>
                <input type="text" class="form-control date-placeholder" placeholder="Data Dibuat" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 200px;">
                <img src="/img/undangan-superadmin/panah.png" alt="panah" class="icon-panah">
                <input type="text" class="form-control date-placeholder" placeholder="Data Keluar" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 200px;">                
            </div>
            <div class="d-flex gap-2">
                <div class="btn btn-primary d-flex align-items-center" style="gap: 5px;">
                    <img src="/img/undangan-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Search" style="outline: none; box-shadow: none;">
                </div>
            </div>
            <button class="btn btn-success"><a href="{{route('add-undangan.superadmin')}}" style="text-decoration: none; color: #878790;">+ Add Undangan Rapat</a></button>
        </div>

        <!-- Table -->
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Data Dibuat</th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Data Disahkan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 3; $i++)
                <tr>
                    <td class="nomor">{{ $i }}</td>
                    <td class="nama-dokumen {{ $i % 3 == 0 ? 'text-danger' : ($i % 2 == 0 ? 'text-warning' : 'text-success') }}">Undangan Rapat Kajian</td>
                    <td>21-10-2024</td>
                    <td>1596</td>
                    <td>837.06/REKA/GEN/VII/2024</td>
                    <td>22-10-2024</td>
                    <td>
                        @if ($i % 3 == 0)
                            <span class="badge bg-danger">Reject</span>
                        @elseif ($i % 2 == 0)
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-success">Approve</span>
                        @endif
                    </td>
                    <td>
                        <!-- <button class="btn btn-sm1"><img src="/img/undangan-superadmin/share.png" alt="share"></button> -->
                        <button class="btn btn-sm2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <img src="/img/undangan-superadmin/Delete.png" alt="delete">
                        </button>
                        <!-- Status Approve -->
                        @if ($i % 3 != 0 && $i % 2 != 0) 
                            <button class="btn btn-sm4" data-bs-toggle="modal" data-bs-target="#arsipModal">
                                <img src="/img/undangan-superadmin/arsip.png" alt="arsip">
                            </button>
                        @else
                            <!-- <button class="btn btn-sm3">
                                <img src="/img/undangan-superadmin/edit.png" alt="edit">
                            </button> -->
                            <a href="{{ route('edit-undangan.superadmin') }}" class="btn btn-sm3">
                                <img src="/img/undangan-superadmin/edit.png" alt="edit">
                            </a>

                        @endif
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
                        <img src="/img/undangan-superadmin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Hapus Undangan Rapat?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal"><a href="{{route ('undangan.superadmin')}}">Cancel</a></button>
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
                        <img src="/img/undangan-superadmin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Menghapus <br> Undangan Rapat</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn-back" data-bs-dismiss="modal"><a href="{{route ('undangan.superadmin')}}">Back</a></button>
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
                        <img src="/img/undangan-superadmin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Arsip Undangan Rapat?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal"><a href="{{route ('undangan.superadmin')}}">Cancel</a></button>
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
                        <img src="/img/undangan-superadmin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Success</b></h5>
                        <h6 class="mb-4" style="font-size: 14px; color: #5B5757;">Berhasil Arsip Undangan Rapat</h6>
                        <!-- Tombol -->
                        <button type="button" class="btn-back" data-bs-dismiss="modal"><a href="{{route ('undangan.superadmin')}}">Back</a></button>
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