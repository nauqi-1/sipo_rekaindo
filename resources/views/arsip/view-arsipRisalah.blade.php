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
                        <label for="pembuat">Pembuat</label>
                        <div class="separator"></div>
                        <input type="text" id="pembuat" value="{{ $risalah->user ? $risalah->user->firstname . ' ' . $risalah->user->lastname : 'N/A' }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                         @if($risalah->pembuat != Auth::user()->id)
                            @if ($risalah->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($risalah->status == 'pending')
                            <span class="badge bg-info">Diproses</span>
                        @elseif ($risalah->status == 'correction')
                            <span class="badge bg-warning">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @else
                            @if ($risalah->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($risalah->status == 'pending')
                            <span class="badge bg-info">Diproses</span>
                        @elseif ($risalah->status == 'correction')
                            <span class="badge bg-warning">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @endif
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
                        <input type="text" id="divisi" value="{{ $risalah->user->department->kode_department ?? $risalah->user->divisi->kode_divisi ?? '-' }}" readonly>
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
                       <a href="{{ route('view-risalahPDF', $risalah->id_risalah)  }}" class="btn btn-file"><img src="/img/mata.png" alt="view"> Lihat</a>
                        <a class="btn btn-file down" onclick="window.location.href='{{ route('cetakrisalah',['id' => $risalah->id_risalah]) }}'"><img src="/img/download.png" alt="down">Unduh</a>
                    </div>
                    <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="diterima" class="form-label">
                            <img src="/img/memo-admin/detail.png" alt="date" style="margin-right: 5px;">Daftar Tujuan
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <pre style="font-family: Arial, sans-serif; font-size: 15px;padding: 10px 15px;">{{ $risalah->tujuan }}</pre>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
