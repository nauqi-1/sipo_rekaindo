<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Memo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/kirim-admin.css') }}">
</head>
<body>
<div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('risalah.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Lihat risalah</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="{{ route('risalah.admin') }}">Memo</a>/<a href="#" style="color: #565656;">Lihat Memo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-admin/info.png" alt="date">Informasi Detail Memo
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{$risalah->nomor_risalah}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{$risalah->seri_surat}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="judul" value="{{$risalah->judul}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{$risalah->tgl_dibuat}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="kepada">Kepada</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{$risalah->tujuan}}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-admin/detail.png" alt="date" style="margin-right: 5px;">Detail
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="pembuat">Pembuat</label>
                        <div class="separator"></div>
                        <input type="text" id="pembuat" value= "{{ $risalah->pembuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        @if ($risalah->status == 'reject')
                            <a class="status btn bg-danger">Ditolak</a>
                        @elseif ($risalah->status == 'pending')
                            <a class="status btn bg-warning">Diproses</a>
                        @else
                            <a class="status btn bg-success">Diterima</a>
                        @endif
                    </div>
                    
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="view" onclick="window.location.href='{{ route('view-risalahPDF', $risalah->id_risalah) }}'"><img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                        @if ($risalah->status=='approve')
                        <button class="down" onclick="window.location.href='{{ route('cetakrisalah',['id' => $risalah->id_risalah]) }}'"><img src="/img/memo-admin/down.png" alt="down">Unduh</button>
                        @endif
                    </div>
                    @if ($risalah->divisi->id_divisi != Auth::user()->divisi->id_divisi)
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <button class="view" onclick="window.location.href='{{ route('risalah.preview', $risalah->id_risalah) }}'"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                    </div>
                    @endif
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">Catatan</div>
                    <textarea type="text" for="catatan" id="catatan"  readonly>{{$risalah->catatan}}</textarea>        
                </div>
            </div>
        </div>
    </div>
</body>
</html>