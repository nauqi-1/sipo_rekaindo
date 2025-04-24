<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Arsip Risalah Superadmin</title>
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
                <a href="{{route ('arsip.risalah')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Detail Arsip Risalah</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/
                <a href="#">Arsip</a>/
                <a href="{{route ('arsip.risalah')}}">Arsip Risalah</a>/
                <a style="color:#565656" href="#">Detail Arsip Risalah</a>
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
                        <input type="text" id="seri" value="{{ $risalah->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <input type="text" id="diterima" value="{{ $risalah->tujuan }}">
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status" >Diterima</button>
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{ $risalah->tgl_disahkan->translatedFormat('d F Y') }}" readonly>
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
                        <input type="text" id="nomor" value="{{ $risalah->nomor_risalah }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{ $risalah->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $risalah->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $risalah->tgl_dibuat->translatedFormat('d F Y') }}" readonly>
                    </div>
                    
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        @if ($risalah->status=='approve')
                        <a style="text-decoration: none;" class="view" onclick="window.location.href='{{ route('view-risalahPDF',[$risalah->id_risalah]) }}'"><img src="/img/memo-admin/view.png" alt="view">Lihat</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>