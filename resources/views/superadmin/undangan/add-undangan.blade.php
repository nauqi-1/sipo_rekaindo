@extends('layouts.superadmin')

@section('title', 'Tambah Undangan Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route ('undangan.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Tambah Undangan Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="#">Beranda</a>/<a href="#">Undangan Rapat</a>/<a href="#" style="color: #565656;">Tambah Undangan Rapat</a>
            </div>
        </div>
    </div>

    <!-- form add undangan -->
    <form method="POST" action="{{ route('undangan-superadmin.store') }}">
    @csrf 
        <div class="add">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 18px;"><b>Formulir Tambah Undangan</b></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="tgl_surat" class="form-label">
                                <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat
                            </label>
                            <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control" required>
                            <input type="hidden" name="tgl_disahkan" >
                        </div>
                        <div class="col-md-6">
                            <label for="seri_surat" class="form-label">Seri Surat</label>
                            <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $nomorSeriTahunan }}"  readonly>
                            <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                            <input type="hidden" name="pembuat" value="{{ auth()->user()->firstname . auth()->user()->lastname }}">
                            <input type="hidden" name="catatan" >
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nomor_undangan" class="form-label">Nomor Surat</label>
                            <input type="text" name="nomor_undangan" id="nomor_undangan" class="form-control" value="{{ $nomorDokumen }}" readonly>
                        </div>
                        <div class="col-md-6" >
                            <label for="judul" class="form-label">Perihal</label>
                            <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan Perihal / Judul Surat" required>
                        </div>

                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="kepada" class="form-label">
                                <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
                                <label for="tujuan" class="label-kepada">*Pisahkan dengan titik koma(;) jika penerima lebih dari satu</label>
                            </label>
                            <input type="text" name="tujuan" id="tujuan" class="form-control" placeholder="1. Kepada Satu; 2. Kepada Dua; 3. Kepada Tiga" required>
                        </div>
                        <div class="col-md-6 lampiran">
                            <label for="tanda_identitas" class="form-label">Lampiran</label>
                            <div class="upload-wrapper">
                                <button type="button" class="btn upload-button" id="openUploadModal">Pilih File</button>
                                <input type="file" id="tanda_identitas" name="tanda_identitas" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div id="filePreview" style="display: none; text-align: center">
                                    <img id="previewIcon" src="" alt="Preview" style="max-width: 18px; max-height: 18px; object-fit: contain; display: inline-block; margin-right: 10px; margin-left: 10px;">
                                    <span id="fileName"></span>
                                    <button type="button" id="removeFile" class="bi bi-x remove-btn" style="border: none; color:red; background-color: white;"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan</label>
                            <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" required>
                                <option value="" disabled selected style="text-align: left;">--Pilih--</option>
                                @foreach($managers as $manager)
                                    <option value="{{  $manager->firstname . ' ' . $manager->lastname  }}">{{ $manager->firstname . ' ' . $manager->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" style="border: none;"></div>
                    </div>

                    <div class="row mb-4 isi-surat-row">
                        <div class="col-md-12">
                            <img src="\img\undangan\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                            <label for="isi_undangan">Isi Surat</label>
                        </div>
                        <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                                <textarea id="summernote" name="isi_undangan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{route ('undangan.superadmin')}}">
                        <button type="button" class="btn btn-cancel">Batal</button>
                    </form>
                    <form action="">
                        <button type="submit" class="btn btn-save">Simpan</button>
                    </form>                    
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Upload File -->
<div class="uploadFile">
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">
                        <img src="/img/memo-superadmin/cloud-add.png" alt="Icon" style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/memo-superadmin/cloud-add.png" alt="Cloud Icon" style="width: 40px; margin-bottom: 10px;">
                            <p class="upload-text">Pilih file atau seret & letakkan di sini</p>
                            <p class="upload-note">Ukuran file PDF tidak lebih dari 2MB</p>
                            <button class="btn btn-outline-primary" id="selectFileBtn">Pilih File</button>
                            <input type="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                            <div id="fileInfo" style="display: none; text-align: center ">
                                <div id="fileInfoWrapper" style="display: flex; align-items: center; justify-content: center">
                                    <img id="modalPreviewIcon" src="" alt="Preview" style="max-width: 18px; max-height: 18px; object-fit: contain; margin-right: 5px; display: none;">
                                    <span id="modalFileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="uploadBtn" >Unggah</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection