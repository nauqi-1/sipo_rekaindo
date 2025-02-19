<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Undangan Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/supervisor/approve-undangan.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('undangan.manager')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('undangan.manager')}}">Undangan Rapat</a>/<a href="#" style="color: #565656;">Tindak Lanjut Undangan</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">No Agenda</label>
                    </div>
                    <div class="card-white">
                        <label for="seri">No Seri</label>
                        <div class="separator"></div>
                        <input type="text" id="seri">
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <input type="text" id="diterima">
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">Diproses</button>
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal">
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/info.png" alt="info surat">Informasi Detail Surat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Dokumen</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor">
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="seri">
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal">
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl">
                    </div>
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada">
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file"><img src="/img/mata.png" alt="view"><a href="#">Lihat</a></button>
                        <button class="down btn-file"><img src="/img/download.png" alt="down"><a href="#">Unduh</a></button>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="label1 card-blue1">
                        <!-- <span>Pengesahan</span> -->
                        <label for="pengesahan" class="label">Pengesahan</label>
                        <div class="form-check1">
                            <label class="form-check-label" for="approve">Diterima</label>
                            <input type="checkbox" class="form-check-input" id="approve" name="approval" value="approve">
                        </div>
                        <div class="form-check2">
                            <label class="form-check-label" for="reject">Ditolak</label>
                            <input type="checkbox" class="form-check-input" id="reject" name="approval" value="reject">
                        </div>
                        <div class="form-check3">
                            <label class="form-check-label" for="reject">Dikoreksi</label>
                            <input type="checkbox" class="form-check-input" id="reject" name="approval" value="reject">
                        </div>
                    </div>
                    <div class="card-blue1">Tindakan Selanjutnya</div>
                    <div class="card-white">
                        <select class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" id="dropdownMenuButton">
                            <option disabled selected style="text-align: left;">--Pilih Tindakan--</option>
                            <option value="koreksi">Koreksi kembali</option>
                            <option value="dilanjutkan">Dilanjutkan</option>
                        </select>                    
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue1">Catatan</div>
                    <textarea type="text" for="catatan" id="catatan" placeholder="Berikan Catatan"></textarea>        
                </div>             
            </div>
        </div>
        <div class="footer">
            <button type="button" class="btn back" id="backBtn"><a href="{{route ('undangan.manager')}}">Kembali</a></button>
            <button type="button" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
        </div>
        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/undangan/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Kirim dan Konfirmasi Undangan?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="#">Batal</a></button>
                            <button type="button" class="btn ok" id="confirmSubmit">Oke</button>
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
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Mengirim</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn backPage" data-bs-dismiss="modal"><a href="{{route ('undangan.manager')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
                $(document).ready(function () {
            $("#confirmSubmit").click(function () {
                // Tutup modal kirim
                $("#submit").modal("hide");

                // Tunggu sebentar sebelum menampilkan modal berhasil
                setTimeout(function () {
                    $("#successModal").modal("show");
                }, 500); // Delay 500ms agar transition lebih smooth
            });
        });
    </script>
</body>
</html>