<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Undangan Rapat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-memo.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('undangan.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="#">Undangan Rapat</a>/<a href="#" style="color: #565656;">Edit Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- form edit undangan -->
        <form method="POST" action="{{ route('undangan/update', $undangan->id_undangan) }}" onsubmit="console.log('FORM DIKIRIM'); return true;">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Edit Undangan Rapat</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_dibuat" class="form-label">
                            <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tanggal Surat <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control" value="{{ $undangan->tgl_dibuat->format('Y-m-d') }}" required>
                        <input type="hidden" name="tgl_disahkan" >
                        <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">

                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $undangan->seri_surat }}" required readonly>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_undangan" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_undangan" id="nomor_undangan" class="form-control" value="{{ $undangan->nomor_undangan }}" required readonly>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" value="{{ $undangan->judul }}" required>
                    </div>
                </div>
                <!--Checkboxes kepada (tujuan)-->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">
                            <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada <span class="text-danger">*</span>
                        </label>
                        @php
                            $selectedTujuan = old('tujuan') ?? $undangan->tujuan ?? [];
                        @endphp

                        <span class="label-kepada" style="font-size: 13px; color: #888; margin-bottom: 4px; display: block;">Centang lebih dari satu jika diperlukan</span>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach($divisi as $d)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        name="tujuan[]" 
                                        value="{{ $d->id_divisi }}" 
                                        id="divisi_{{ $d->id_divisi }}"
                                        {{ in_array($d->id_divisi, $selectedTujuan) ? 'checked' : '' }}>
                                        {{-- {{ is_array($undangan->tujuan) && in_array($d->id_divisi, $undangan->tujuan) ? 'checked' : '' }}> --}}
                                    <label class="form-check-label" for="divisi_{{ $d->id_divisi }}">
                                        {{ $d->nm_divisi }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                      
                        @error('tujuan')
                            <div class="form-control text-danger">{{ $message }}</div>
                        @enderror
                    

                    
                </div>
                <div class="col-md-6">
                    <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan <span class="text-danger">*</span></label>
                     {{-- Hidden input untuk memastikan data terkirim --}}
                    <input type="hidden" name="nama_bertandatangan" value="{{ $undangan->nama_bertandatangan }}">
                    <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" disabled>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->firstname . ' ' . $manager->lastname }}" 
                                {{ old('nama_bertandatangan', $undangan->nama_bertandatangan) == ($manager->firstname . ' ' . $manager->lastname) ? 'selected' : '' }}>
                                {{ $manager->firstname . ' ' . $manager->lastname }}
                            </option>
                        @endforeach
                    </select>
                    @error('nama_bertandatangan')
                        <div class="form-control text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
                 
                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\undangan\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="summernote">Isi Surat <span class="text-danger">*</span></label>
                    </div>
                    <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_undangan">{{ $undangan->isi_undangan }}</textarea>
                    </div>
                </div>    
                
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('undangan.admin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save">Simpan</button>
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
                            <input type="file" id="fileInput" name="fileInput" accept=".pdf" style="display: none;">
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
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear', 'fontname', 'fontsize', 'color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
                ],
                fontNames: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman'], 
                fontNamesIgnoreCheck: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman']
            });
        });
    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>