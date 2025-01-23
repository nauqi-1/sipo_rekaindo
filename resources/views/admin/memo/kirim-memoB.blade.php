<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Memo Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/admin/kirim-memoB.css') }}">  
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/memo-admin/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Beranda</a>/<a href="#">Memo</a>/<a href="#">Memo Terkirim</a>
                </div>
            </div>
        </div>

        <div class="body-content">
            <!-- Cards in a row  -->
            <div class="row">
                <!-- Card Utama untuk Kolom 1 -->
                <div class="col-md-6">
                    <!-- form add memo -->
                    <div class="card-body">
                        <div class="row-mb-3">
                            <div class="col-md-6">
                                <div class="card-blue">
                                    <img src="/img/memo-admin/info.png" alt="info surat">
                                    <span>Informasi Detail Surat</span>
                                </div>
                                <div class="card-white">
                                    <label for="nomor">No Surat</label>
                                    <div class="separator"></div>
                                    <input type="text" id="nomor">
                                </div>
                                <div class="card-white">
                                    <label for="lampiran">Lampiran</label>
                                    <div class="separator"></div>
                                    <input type="text" id="lampiran">
                                </div>
                                <div class="card-white">
                                    <label for="perihal">Perihal</label>
                                    <div class="separator"></div>
                                    <input type="text" id="perihal">
                                </div>
                                <div class="card-white">
                                    <label for="tanggal">Tanggal</label>
                                    <div class="separator"></div>
                                    <input type="text" id="tanggal">
                                </div>
                                <div class="card-white">
                                    <label for="kepada">Kepada</label>
                                    <div class="separator"></div>
                                    <input type="text" id="kepada">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Utama untuk Kolom 2 -->
                <div class="col-md-6">
                    <div class="card-body">
                        <div class="row-mb-3">
                            <div class="col-md-6">
                                <div class="card-blue">
                                    <img src="/img/memo-admin/detail.png" alt="detail">
                                    <span>Detail</span>
                                </div>
                                <div class="card-white">
                                    <label for="pembuat">Pembuat</label>
                                    <div class="separator"></div>
                                    <input type="text" id="pembuat">
                                </div>
                                <div class="card-white">
                                    <label for="status">Status</label>
                                    <div class="separator"></div>
                                    <button id="statusButton" class="btn-status">Diterima</button>
                                </div>
                                <div class="card-white">
                                    <label for="dibuat">Dibuat Tanggal</label>
                                    <div class="separator"></div>
                                    <input type="text" id="dibuat">
                                </div>
                                <div class="card-white">
                                    <label for="file">Berkas</label>
                                    <div class="separator"></div>
                                    <button><img src="/img/memo-admin/preview.png" alt="preview" style="margin-right: 10px;">Lihat</button>
                                    <button style="margin-left: 5px;"><img src="/img/memo-admin/download.png" alt="unduh" style="margin-right: 10px;">Unduh</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards in a row  -->
            <div class="row">
                <!-- Card Utama untuk Kolom 1 -->
                <div class="col-md-6">
                    <!-- form add memo -->
                    <div class="card-body">
                        <div class="row-mb-3">
                            <div class="col-md-6">
                                <div class="card-blue1">
                                    <!-- <span>Pengesahan</span> -->
                                    <label for="pengesahan" class="label1">Pengesahan</label>
                                    <div class="form-check1">
                                        <label class="form-check-label" for="approve">Diterima</label>
                                        <input type="checkbox" class="form-check-input" id="approve" name="approval" value="approve">
                                    </div>
                                    <div class="form-check2">
                                        <label class="form-check-label" for="reject">Ditolak</label>
                                        <input type="checkbox" class="form-check-input" id="reject" name="approval" value="reject">
                                    </div>
                                </div>
                                <div class="card-blue1">
                                    <!-- <span>Pengesahan</span> -->
                                    <label for="pengesahan" class="label1">Tindakan Selanjutnya</label>
                                </div>
                                <div class="card-white">
                                    <select name="tindakan" id="tindakan" class="form-control" required>
                                        <option value="" disabled selected style="text-align: left;">--Pilih Tindakan--</option>
                                        <option value="koreksi">Koreksi Kembali</option>
                                        <option value="dilajutkan">Akan Dilanjutkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Utama untuk Kolom 2 -->
                <div class="col-md-6">
                    <div class="card-body">
                        <div class="row-mb-3">
                            <div class="col-md-6">
                                <div class="card-blue1">
                                    <span>Catatan</span>
                                </div>
                                <div class="card-input">
                                    <textarea type="text" id="text" placeholder="Berikan Catatan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="#" method="GET">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-button">Batal</button>
                <button type="submit" class="btn btn-primary" id="edit-button">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="approval"]');

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        checkboxes.forEach((other) => {
                            if (other !== this) {
                                other.checked = false;
                            }
                        });
                    }
                });
            });
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
