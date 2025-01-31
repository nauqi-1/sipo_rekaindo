<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Memo Terkirim</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/supervisor/view-memoTerkirim.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.terkirim')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo Terkirim</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('memo.terkirim')}}">Memo Terkirim</a>/<a href="#" style="color: #565656;">Lihat Memo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="info-surat" class="form-label">
                            <img src="/img/memo-supervisor/info.png" alt="info surat">Informasi Detail Surat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
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
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">Diproses</button>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="card-blue">
                    <label for="penerima" class="form-label">
                        <img src="/img/memo-supervisor/info.png" alt="info penerima">Informasi Penerima
                    </label>
                </div>
                <div class="col">
                    <div class="card-white">
                        <label for="file">Penerima</label>
                        <div class="separator"></div>
                        <input type="text" id="penerima">                  
                    </div>                        
                    <div class="card-white">
                        <label for="file">Catatan</label>
                        <div class="separator"></div>
                        <input type="text-area" id="catatan">                   
                    </div>
                </div>             
            </div>
        </div>
    </div>
</body>
</html>