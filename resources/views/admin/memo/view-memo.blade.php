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
                <a href="{{route ('memo.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Detail Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="{{ route('memo.admin') }}">Memo</a>/<a href="#" style="color: #565656;">Lihat Memo</a>
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
                        <input type="text" id="nomor" value="{{$memo->nomor_memo}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{$memo->seri_surat}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{$memo->judul}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{$memo->tgl_dibuat->translatedFormat('d F Y')}}" readonly>
                    </div>
                    <div class="card-white flex items-stretch">
                        <label for="kepada">Kepada</label>
                        <div class="separator"></div>

                        @php
                        use App\Models\Divisi;

                            $divisiIds = explode(';', $memo->tujuan); 
                            $divisiNames = Divisi::whereIn('id_divisi', $divisiIds)->pluck('nm_divisi');
                        @endphp

                        @if (count($divisiNames) === 1)
                            <input type="text" id="kepada" value="{{ trim($divisiNames[0]) }}" readonly>
                        @else
                         <div 
                        style="border-radius: 8px; 
                               padding: 8px 12px; 
                               background-color: #fff; 
                               font-size: 14px;
                               height: auto;">
                            <ol style="padding-left: 20px;">
                                @foreach ($divisiNames as $tujuan)
                                    <li>{{ trim($tujuan) }}</li>
                                @endforeach
                            </ol>
                         </div>
                        @endif
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
                        <input type="text" id="pembuat" value="{{$memo->pembuat}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        @if($memo->divisi->id_divisi != Auth::user()->divisi->id_divisi)
                            @if ($memo->final_status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($memo->final_status  == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @elseif ($memo->final_status  == 'correction')
                                <span class="badge bg-warning">Dikoreksi</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        @else
                            @if ($memo->status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($memo->status  == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @elseif ($memo->status  == 'correction')
                                <span class="badge bg-warning">Dikoreksi</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        @endif
                        
                    </div>
                    
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="view" onclick="window.location.href='{{ route('view-memoPDF', $memo->id_memo) }}'"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                        @if ($memo->status=='approve')
                        <a style="text-decoration: none;" class="down" onclick="window.location.href='{{ route('cetakmemo',['id' => $memo->id_memo]) }}'"><img src="/img/memo-admin/down.png" alt="down">Unduh</a>
                        @endif
                    </div>
                   <!-- @if ($memo->divisi->id_divisi != Auth::user()->divisi->id_divisi)
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <button class="view" onclick="window.location.href='{{ route('memo.preview', $memo->id_memo) }}'"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                    </div>
                    @endif-->
                </div>
            </div>
            @if ($memo->catatan !== null)
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">Catatan</div>
                    <textarea type="text" for="catatan" id="catatan"  readonly>{{$memo->catatan}}</textarea>        
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>