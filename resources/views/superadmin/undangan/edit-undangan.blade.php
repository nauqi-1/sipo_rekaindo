@extends('layouts.superadmin')

@section('title', 'Edit Undangan Rapat')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('undangan.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Home</a>/<a href="#">Undangan Rapat</a>/<a href="#" style="color: #565656;">Edit Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- form edit undangan -->
        <form method="POST" action="{{ route('undangan/update', $undangan->id_undangan) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Edit Undangan Rapat</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat
                        </label>
                        <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control" value="{{ $undangan->tgl_dibuat }}" required>
                        <input type="hidden" name="tgl_disahkan" >
                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="jenis_document" id="seru-surat" class="form-control" value="{{ $undangan->seri_surat }}" required readonly>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_document" class="form-label">Nomor Surat</label>
                        <input type="text" name="jenis_document" id="nomor-surat" class="form-control" value="{{ $undangan->nomor_undangan }}" required>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Perihal</label>
                        <input type="text" name="judul" id="judul" class="form-control" value="{{ $undangan->judul }}" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
                            <label for="tujuan" class="label-kepada">*Pisahkan dengan titik koma(;) jika penerima lebih dari satu</label>
                        </label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" value="{{ $undangan->tujuan }}" required>
                    </div>
                    <div class="col-md-6" style="border: none;"></div>
                </div>
                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\undangan\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi_document">Isi Surat</label>
                    </div>
                    <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_document">{{ $undangan->isi_undangan }}</textarea>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nama_pimpinan" class="form-label">Nama yang Bertanda Tangan</label>
                        <!-- <select name="nama_pimpinan" id="nama_pimpinan" class="form-control" required>
                            <option value="" disabled selected style="text-align: left;">--Pilih--</option>
                        </select> -->
                        <select class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" id="dropdownMenuButton">
                            <option disabled selected style="text-align: left;">--Pilih--</option>
                            <option value="pimpinan1">Jokowi</option>
                            <option value="pimpinan2">Prabowo</option>
                        </select>
                    </div>
                    <div class="col-md-6 lampiran">
                        <label for="tanda_identitas" class="form-label">Lampiran</label>
                        <div class="upload-wrapper">
                            <button type="button" class="btn btn-primary upload-button" data-bs-toggle="modal" data-bs-target="#uploadModal">Pilih File</button>
                            <input type="file" id="tanda_identitas" name="tanda_identitas" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('undangan.superadmin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save"><a href="{{route ('undangan.superadmin')}}">Simpan</a></button>
            </div>
        </div>
        </form>
    </div>
    
    <!-- Modal Upload File -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">
                        <img src="/img/undangan/cloud-add.png" alt="Icon" style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/undangan/cloud-add.png" alt="Cloud Icon" style="width: 40px; margin-bottom: 10px;">
                            <p class="upload-text">Pilih file atau seret & letakkan di sini</p>
                            <p class="upload-note">Ukuran file PDF tidak lebih dari 20MB</p>
                            <button class="btn btn-outline-primary" id="selectFileBtn">Pilih File</button>
                            <input type="file" id="fileInput" accept=".pdf" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="uploadBtn">Unggah</button>
                </div>
            </div>
        </div>
    </div>
@endsection