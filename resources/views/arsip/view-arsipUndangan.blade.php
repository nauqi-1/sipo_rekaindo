<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Arsip Undangan Superadmin</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('/css/superadmin/viewArsip.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('arsip.undangan')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Detail Arsip Undangan</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}">Beranda</a>/
                <a href="#">Arsip</a>/
                <a href="{{route ('arsip.undangan')}}">Arsip Undangan</a>/
                <a style="color:#565656" href="#">Detail Arsip Undangan</a>
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
                        <input type="text" id="seri" value="{{ $undangan->seri_surat }}" >
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
                        <button class="status">Diterima</button>
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{ $undangan->tgl_disahkan }}" readonly>
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
                        <input type="text" id="nomor" value="{{ $undangan->nomor_undangan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{ $undangan->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $undangan->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $undangan->tgl_dibuat }}" readonly>
                    </div>
                    <!-- <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" >
                    </div> -->
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <!-- <button class="btn-file"><img src="/img/mata.png" alt="view"><a href="#">Lihat</a></button>
                        <button class="down btn-file"><img src="/img/download.png" alt="down"><a href="#">Unduh</a></button> -->
                        <a href="{{ route('view-undanganPDF', $undangan->id_undangan)  }}" class="btn btn-file"><img src="/img/mata.png" alt="view"> Lihat</a>
                        <a href="#" class="btn btn-file down"><img src="/img/download.png" alt="down"> Unduh</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>