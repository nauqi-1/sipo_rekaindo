@extends('layouts.manager')

@section('title', 'Detail Memo Terkirim')

@section('content')
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
                    <a href="#">Beranda</a>/<a href="{{route ('memo.terkirim')}}">Memo Terkirim</a>/<a href="#" style="color: #565656;">Detail Memo</a>
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
                        <input type="text" id="nomor" value="{{ $memo->memo->nomor_memo }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $memo->memo->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->memo->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $memo->memo->tgl_dibuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{ $memo->memo->tujuan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file" onclick="window.location.href='{{ route('view-memoPDF', $memo->memo->id_memo) }}'"><img src="/img/mata.png" alt="view">Lihat</button>
                        
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
                        <input type="text-area" id="catatan" value="{{ $memo->memo->catatan }}" readonly>                   
                    </div>
                </div>             
            </div>
        </div>
    </div>
@endsection