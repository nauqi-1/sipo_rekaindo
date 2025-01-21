<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Terkirim</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/supervisor/after-kirim.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo Terkirim</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a>/<a href="#">Memo</a>/<a href="#">Memo Terkirim</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <div class="card-body">
            <div class="row-mb-4">
                <div class="col-md-6">
                    <div class="card-blue">
                        <img src="/img/laporan/tanggal.png" alt="date">
                        <span>Informasi Detail Surat</span>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor">
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi">
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal">
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal">
                    </div>
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="lampiran">
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button><img src="/img/mata.png" alt="preview"> Preview</button>
                        <button style="margin-left: 5px;"><img src="/img/download.png" alt="unduh"> Download</button>
                    </div>
                    <div class="card-white">
                        <label for="status">Status Surat</label>
                        <div class="separator"></div>
                        <span class="badge bg-success">Approve</span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <div class="row-mb-4">
                <div class="col-md-6">
                    <div class="card-blue">
                        <img src="/img/laporan/tanggal.png" alt="date">
                        <span>Informasi Penerima</span>
                    </div>
                    <div class="card-white">
                        <label for="penerima">Penerima</label>
                        <div class="separator"></div>
                        <input type="text" id="penerima">
                    </div>
                    <div class="card-white">
                        <label for="catatan">Catatan</label>
                        <div class="separator"></div>
                        <input type="text" id="catatan">
                    </div>
                </div>
            </div>
        </div>
        <form action="#" method="GET">
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="edit-button">Save</button>
                <button type="button" class="btn btn-secondary" id="cancel-button">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>