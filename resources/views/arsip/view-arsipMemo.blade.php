<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Arsip Memo Superadmin</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('/css/admin/viewArsip.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('arsip.memo')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Detail Arsip Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a>/
                <a href="{{route ('arsip.memo')}}">Arsip Memo</a>/
                <a style="color:#565656" href="#">Detail Arsip Memo</a>
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
                        <input type="text" id="seri" value="{{ $memo->seri_surat }}" readonly>
                    </div>
                     @php
                        use App\Models\Divisi;
                    
                        $divisiIds = explode(';', $memo->tujuan); 
                        $divisiNames = Divisi::whereIn('id_divisi', $divisiIds)->pluck('nm_divisi')->toArray();
                    @endphp
                    <div class="card-white">
                        <label for="file">Penerima</label>
                        <div class="separator"></div>
                        <input type="text" id="penerima" value="{{ implode(', ', $divisiNames) }}" readonly>                  
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
                        <input type="text" id="tanggal" value="{{ $memo->tgl_disahkan->translatedFormat('d F Y') }}" readonly>
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
                        <input type="text" id="nomor" value="{{ $memo->nomor_memo }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{ $memo->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $memo->tgl_dibuat->translatedFormat('d F Y') }}" readonly>
                    </div>
                    
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <!-- <button class="view" onclick="window.location.href='{{ route('view-memoPDF', $memo->id_memo) }}'"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button> -->
                        @if ($memo->status=='approve')
                        <a style="text-decoration: none;" class="view" onclick="window.location.href='{{ route('view-memoPDF',[$memo->id_memo]) }}'"><img src="/img/memo-admin/view.png" alt="view">Lihat</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>