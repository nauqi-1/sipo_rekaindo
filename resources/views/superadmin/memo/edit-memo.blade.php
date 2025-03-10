@extends('layouts.superadmin')

@section('title', 'Edit Memo')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="#">Memo</a>/<a href="#" style="color: #565656;">Edit Memo</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <form method="POST" action="{{ route('memo/update', $memo->id_memo) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Form Edit Memo</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tanggal Surat <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tgl_surat" id="tgl_surat" class="form-control" value="{{ $memo->tgl_dibuat->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $memo->seri_surat }}" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" value="{{ $memo->nomor_memo }}" required>
                    </div>
                    <div class="col-md-6" >
                        <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="perihal" id="perihal" class="form-control" value="{{ $memo->judul }}" required>
                    </div>

                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/memo-superadmin/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kepada" id="kepada" class="form-control" value="{{ $memo->tujuan }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bertandatangan" id="kepada" class="form-control" value="{{ $memo->nama_bertandatangan }}" required>
                    </div>
                </div>

                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\memo-superadmin\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi-surat">Isi Surat</label>
                    </div>
                    <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_surat" >{{ $memo->isi_memo }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-4 need-row">
                <div class="col-md-12">
                    <label for="need" class="need">Keperluan Barang</label>
                </div>
            </div>
            @foreach ($memo->kategoriBarang as $index => $barang)
            <div class="row mb-4 isi">
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_nomor">Nomor</label>
                    <input type="text" id="kategori_barang_{{ $index }}_nomor" name="kategori_barang[{{ $index }}][nomor]" class="form-control" value="{{ $barang->nomor }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_nama_barang">Barang</label>
                    <input type="text" id="kategori_barang_{{ $index }}_nama_barang" name="kategori_barang[{{ $index }}][nama_barang]" class="form-control" value="{{ $barang->nama_barang }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_qty">Qty</label>
                    <input type="number" id="kategori_barang_{{ $index }}_qty" name="kategori_barang[{{ $index }}][qty]" class="form-control" value="{{ $barang->qty }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_satuan">Satuan</label>
                    <input type="text" id="kategori_barang_{{ $index }}_satuan" name="kategori_barang[{{ $index }}][satuan]" class="form-control" value="{{ $barang->satuan }}">
                </div>
            </div>
            @endforeach
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('memo.superadmin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save"><a href="{{route ('memo.superadmin')}}">Simpan</a></button>
            </div>
        </div>
        </form>
    </div>
@endsection