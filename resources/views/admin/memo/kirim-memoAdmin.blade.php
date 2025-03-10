@extends('layouts.admin')

@section('title', 'Kirim Memo')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Kirim Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="{{ route('memo.admin') }}">Memo</a>/<a href="#" style="color: #565656;">Kirim Memo</a>
                </div>
            </div>
        </div>
        <form action="{{ route('documents.send') }}" method="POST">
        @csrf
        <input type="hidden" name="id_document" value="{{ $memo->id_memo }}">
        <input type="hidden" name="jenis_document" value="memo">
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
                        <input type="text" id="nomor" value="{{ $memo->nomor_memo }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $memo->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Disahkan</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $memo->tgl_disahkan ? : '-' }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="kepada">Kepada</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{ $memo->tujuan }}" readonly>
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
                        <input type="text" id="pembuat" value="{{ $memo->pembuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">Diproses</button>
                    </div>
                    <div class="card-white">
                        <label for="tgl-buat">Dibuat Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl-buat" value="{{ $memo->tgl_dibuat->translatedFormat('d F Y') }}" readonly>
                    </div>
                    
                   
                    @if($memo->status == 'approve'&& $memo->divisi->id_divisi == Auth::user()->divisi->id_divisi)
                    <div class="card-white">
                    <label for="tanda_identitas" class="form-label">Lampiran</label>
                    <div class="separator"></div>
                        <div class="upload-wrapper">
                            <button type="button" class="btn btn-primary upload-button" id="openUploadModal" style="margin-left: 30px;">Pilih File</button>
                            <input type="file" id="tanda_identitas" name="tanda_identitas" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                            <div id="filePreview" style="display: none; text-align: center">
                                <img id="previewIcon" src="" alt="Preview" style="max-width: 18px; max-height: 18px; object-fit: contain; display: inline-block; margin-right: 10px;">
                                <span id="fileName"></span>
                                <button type="button" id="removeFile" class="bi bi-x remove-btn" style="border: none; color:red; background-color: white;"></button>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <a class="view" href="{{ route('memo.preview', $memo->id_memo)  }}"> <img src="/img/memo-admin/view.png" alt="view" >Lihat</a>
                        <a class="down" href="{{ route('memo.download', $memo->id_memo) }}"><img src="/img/memo-admin/down.png" alt="down">Unduh</a>
                    </div> -->
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">
                        <label for="tindakan">Tindakan Selanjutnya</label>
                        <label for="isi" style="color: #FF000080; font-size: 10px; margin-left: 5px;">
                            *Pilih opsi posisi dan divisi tujuan untuk mengirimkan memo kepada pihak yang dituju.
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="posisi_penerima">Posisi Penerima</label>
                        <div class="separator"></div>
                        <select name="posisi_penerima" id="posisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" required autofocus autocomplete="posisi_penerima">
                            <option disabled selected style="text-align: left;">--Pilih--</option>
                            @foreach($position as $p)
                                <option value="{{ $p->id_position }}">{{ $p->nm_position }}</option>
                            @endforeach
                        </select>                    
                    </div>
                    <div class="card-white">
                        <label for="divisi_penerima" class="form-label">Divisi Penerima</label>
                        <div class="separator"></div>
                         <select name="divisi_penerima" id="divisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" required autofocus autocomplete="divisi_penerima">
                         <option disabled selected style="text-align: left;">--Pilih--</option>
                         @foreach($divisi as $d)
                            <option value="{{ $d->id_divisi }}">{{ $d->nm_divisi }}</option>
                        @endforeach
                        </select>                  
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <button type="button" class="btn back" id="backBtn" onclick="window.location.href='{{ route('memo.admin') }}'">Kembali</button>   
            <button type="submit" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
        </div>
        </form>

        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-admin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Kirim Memo?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="#">Batal</a></button>
                            <button type="button" class="btn ok" id="confirmDelete">Oke</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Mengirimkan Memo</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn backPage" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Upload File -->
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
@endsection