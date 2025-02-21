@extends('layouts.superadmin')

@section('title', 'Tambah Memo')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route ('memo.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Tambah Memo</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="#">Beranda</a>/<a href="#">Memo</a>/<a href="#" style="color: #565656;">Tambah Memo</a>
            </div>
        </div>
    </div>

    <!-- form add memo -->
    <form method="POST" action="{{ route('memo-superadmin.store') }}" enctype="multipart/form-data">
    @csrf 
        <div class="add">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 18px;"><b>Formulir Tambah Memo</b></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="tgl_surat" class="form-label">
                                <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat
                            </label>
                            <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control" required>
                            <input type="hidden" name="tgl_disahkan" >
                            <input type="hidden" name="catatan" >
                        </div>
                        <div class="col-md-6">
                            <label for="seri_surat" class="form-label">Seri Surat</label>
                            <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $nomorSeriTahunan ?? '' }}"  readonly>
                            <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                            <input type="hidden" name="pembuat" value="{{ auth()->user()->position->nm_position . auth()->user()->role->nm_role }}">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nomor_memo" class="form-label">Nomor Surat</label>
                            <input type="text" name="nomor_memo" id="nomor_memo" class="form-control" value="{{ $nomorDokumen }}" readonly>
                        </div>
                        <div class="col-md-6" >
                            <label for="judul" class="form-label">Perihal</label>
                            <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan Perihal / Judul Surat" required>
                        </div>

                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="kepada" class="form-label">
                                <img src="/img/memo-superadmin/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
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
                            <img src="\img\memo-superadmin\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                            <label for="isi_memo">Isi Surat</label>
                        </div>
                        <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                                <textarea id="summernote" name="isi_memo"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mb-4 need-row">
                    <div class="col-md-12">
                        <label for="need" class="need">Keperluan Lain</label>
                        <label for="isi" class="fill">*Isi keperluan barang jika dibutuhkan</label>
                    </div>
                </div>
                <div class="row mb-4 need-row">
                    <div class="col">
                        <label for="need" class="need" style="font-size: 14px; color: #1E4178">Tambah Kategori Barang</label>
                    </div>
                    <div class="col">
                        <div class="cek d-flex" style="font-size: 14px;">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="opsi" id="ya" value="ya" onclick="toggleKategoriBarang()" style="margin-right: 15px;"> Ya
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="opsi" id="tidak" value="tidak" onclick="toggleKategoriBarang()" style="margin-right: 15px;" checked> Tidak
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="jumlahKategoriDiv" class="card-body2" style="display: none;">
                    <div class="row mb-3">
                        <div class="colom">
                            <label for="jumlahKategori" class="form-label">Jumlah Kategori Barang</label>
                            <input type="number" id="jumlahKategori" name="jumlah_kolom" class="form-control" placeholder="Masukkan jumlah kategori barang yang ingin diinput" min="1" oninput="generateBarangFields()">
                        </div>
                    </div>
                </div>
                <div id="barangTable"></div>

                <div class="card-footer">
                    <form action="{{route ('memo.superadmin')}}">
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