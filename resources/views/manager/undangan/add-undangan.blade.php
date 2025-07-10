<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Undangan Rapat Admin</title>
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
                <a href="{{route ('undangan.'. Auth::user()->role->nm_role)}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Tambah Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route(Auth::user()->role->nm_role .'.dashboard') }}">Beranda</a>/<a href="{{ route('undangan.admin') }}">Undangan Rapat</a>/<a href="#" style="color: #565656;">Tambah Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- form add undangan -->
        <form method="POST" action="{{ route('undangan-superadmin.store') }}" enctype="multipart/form-data">
        @csrf 
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Tambah Undangan</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tanggal Surat <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control"  value="{{ old('tgl_dibuat') }}">
                        @error('tgl_dibuat')
                            <div class="form-control text-danger">{{ $message }}</div>
                        @enderror
                        <input type="hidden" name="tgl_disahkan" >
                        <input type="hidden" name="catatan" >
                        <input type="hidden" name="pembuat" value="{{ auth()->user()->firstname . auth()->user()->lastname }}">
                    </div>
                    <div class="col-md-6">
                    <label for="seri_surat" class="form-label">Seri Surat</label>
                    <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $nomorSeriTahunan ?? '' }}" readonly>
                    <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                    <input type="hidden" name="pembuat" value="{{ auth()->user()->firstname .' '. auth()->user()->lastname }}">
                </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_undangan" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_undangan" id="nomor_undangan" class="form-control" value="{{ $nomorDokumen }}" readonly>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan Perihal / Judul Surat" value="{{ old('judul') }}" >
                        @error('judul')
                            <div class="form-control text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="row mb-4">
                    <!--Checkboxes kepada (tujuan)-->
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada <span class="text-danger">*</span>
                            <label for="tujuan" class="label-kepada">Centang lebih dari satu jika diperlukan</label>
                        </label>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach($divisiList as $d)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        name="tujuan[]" 
                                        value="{{ $d->id_divisi }}" 
                                        id="divisi_{{ $d->id_divisi }}">
                                    <label class="form-check-label" for="divisi_{{ $d->id_divisi }}">
                                        {{ $d->nm_divisi }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        @error('tujuan[]')
                            <div class="form-control text-danger">{{ $message }}</div>       
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_rapat" class="form-label">
                            <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tanggal Rapat<span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tgl_rapat" id="tgl_rapat" class="form-control"  value="{{ old('tgl_rapat') }}" placeholder="Tanggal Rapat">
                        @error('tgl_rapat')
                            <div class="form-control text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="tempat">Tempat Rapat</label> <span class="text-danger">*</span>
                            <input type="text" name="tempat" id="tempat" class="form-control" placeholder="Ruang Rapat" required>
                        </div>
                        <div class="col-md-6">
                            <label for="waktu" class="form-label">Waktu Rapat</label> <span class="text-danger">*</span>
                            <div class="d-flex align-items-center">
                                <input type="text" name="waktu_mulai" id="waktu_mulai" class="form-control me-2" placeholder="09.00" required>
                                <span class="fw-bold">s/d</span>
                                <input type="text" name="waktu_selesai" id="waktu_selesai" class="form-control ms-2" placeholder="Selesai" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <!--TTD yang bertanda tangan-->
                    <div class="col-md-6">
                        <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan </label>
                    <select name="manager_user_id" required id="managerDropdown" class="form-control" disabled>
                            <option value="">-- Pilih Penandatangan --</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ $manager->id == Auth::id() ? 'selected' : '' }}>
                                    {{ $manager->firstname }} {{ $manager->lastname }}
                                </option>
                            @endforeach
                        </select>

                        <input type="hidden" name="manager_user_id" id="managerUserId" class="form-control" value="{{ Auth::user()->id }}">

                        <input type="hidden" name="nama_bertandatangan" id="namaBertandatangan" class="form-control" value="{{ Auth::user()->firstname }} {{ Auth::user()->lastname}}">

                        @error('nama_bertandatangan')
                            <div class="form-control text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 lampiran">
                        <label for="lampiran" class="form-label">Lampiran</label>
                        <div class="separator"></div>
                            <div class="upload-wrapper">
                                <button type="button" class="btn btn-primary upload-button" id="openUploadModal" style="margin-left: 30px;">Pilih File</button>
                                <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                                <div id="filePreview" style="display: none; text-align: center">
                                    <img id="previewIcon" src="" alt="Preview" style="max-width: 18px; max-height: 18px; object-fit: contain; display: inline-block; margin-right: 10px;">
                                    <span id="fileName"></span>
                                    <button type="button" id="removeFile" class="bi bi-x remove-btn" style="border: none; color:red; background-color: white;"></button>
                                </div>
                            </div>
                        </div> 
                    </div>
                    
                </div>
                <div class="row mb-4">
                    
                    
                    <div class="col-md-6 lampiran"></div>   
                </div>
                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\undangan\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi_undangan">Agenda <span class="text-danger">*</span></label>
                    </div>
                    <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_undangan" value="{{ old('isi_undangan') }}"></textarea>
                            @error('isi_undangan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror   
                    </div>
                </div>
            </div>
            <!--Permohonan Approval disini yah-->
            {{-- <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">
                        <label for="tindakan">Undangan Akan Diajukan untuk Proses Approval</label>
                        <label for="isi" style="color: #FF000080; font-size: 10px; margin-left: 5px;">
                            *Undangan akan diapprove oleh :
                        </label>
                    </div>
                        <div class="separator"></div>
                        @foreach($managers as $manager)
                                <option value="{{  $manager->firstname . ' ' . $manager->lastname  }}">{{ $manager->firstname . ' ' . $manager->lastname }}</option>
                            @endforeach
                        
                 
                    <!---->
                </div>
            </div> --}}
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('undangan.superadmin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save">Kirim</button>
            </div>
        </div>
        </form>
    </div>
        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <img src="/img/memo-admin/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                        <!-- Success Message -->
                        <h5 class="modal-title"><b>Sukses</b></h5>
                        <p class="mt-2">Undangan Telah Terkirim</p>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a href="{{route ('undangan.admin')}}" style="color: white; text-decoration: none">Kembali ke Halaman Undangan</a></button>
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
        <script>
        // Modal Upload File - Menampilkan Modal
        document.getElementById('openUploadModal').addEventListener('click', function () {
            var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
            uploadModal.show();
        });

        // Membuka file input ketika tombol "Pilih File" di klik
        document.getElementById('selectFileBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        // Menangani dragover event untuk upload box
        document.getElementById('uploadBox').addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.border = '2px dashed #007bff';
        });

        // Menangani dragleave event untuk upload box
        document.getElementById('uploadBox').addEventListener('dragleave', function () {
            this.style.border = '2px dashed #ccc';
        });

        // Menangani drop event untuk upload box
        document.getElementById('uploadBox').addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.border = '2px dashed #ccc';
            document.getElementById('fileInput').files = e.dataTransfer.files;
            updateFilePreview();
        });

        // Menangani pemilihan file melalui file input
        document.getElementById('fileInput').addEventListener('change', function () {
            const file = this.files[0];
            const uploadBtn = document.getElementById('uploadBtn');
            const fileInfo = document.getElementById('fileInfo');
            const modalFileName = document.getElementById('modalFileName');
            const modalPreviewIcon = document.getElementById('modalPreviewIcon');
            const uploadText = document.querySelector('.upload-text');
            const uploadNote = document.querySelector('.upload-note');
            const selectFileBtn = document.getElementById('selectFileBtn');

            if (file) {
                modalFileName.textContent = file.name;
                fileInfo.style.display = 'block';
                uploadBtn.disabled = false;
                uploadText.style.display = 'none';
                uploadNote.style.display = 'none';
                selectFileBtn.style.display = 'none';
                
                if (file.type.startsWith('image/')) {
                    modalPreviewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    modalPreviewIcon.src = '/img/pdf.png'; // Ikon PDF
                }
                modalPreviewIcon.style.display = 'block';
            }
        });

        // Meng-upload file setelah tombol "Unggah" di klik di modal
        document.getElementById('uploadBtn').addEventListener('click', function () {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            const lampiran = document.getElementById('lampiran');
            const fileNameDisplay = document.getElementById('fileName');
            const filePreview = document.getElementById('filePreview');
            const previewIcon = document.getElementById('previewIcon');
            const uploadButton = document.getElementById('openUploadModal');

            // Menampilkan file info di input lampiran setelah file dipilih
            document.getElementById('fileInfoWrapper').style.display = 'flex';
            document.getElementById('fileInfoWrapper').style.alignItems = 'center';
            
            if (file) {
                lampiran.files = fileInput.files;
                fileNameDisplay.textContent = file.name;
                filePreview.style.display = 'block';
                uploadButton.style.display = 'none';
                
                if (file.type.startsWith('image/')) {
                    previewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    previewIcon.src = '/img/pdf.png'; // Ikon PDF
                }
                previewIcon.style.display = 'inline-block'; // Menampilkan ikon preview
            }

            // Menyembunyikan modal setelah file diupload
            var uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            uploadModal.hide();
        });

        // Menghapus file yang dipilih dan menyembunyikan preview
        document.getElementById('removeFile').addEventListener('click', function () {
            document.getElementById('lampiran').value = ''; // Menghapus file yang dipilih
            document.getElementById('filePreview').style.display = 'none'; // Menyembunyikan preview
            document.getElementById('openUploadModal').style.display = 'block'; // Menampilkan tombol upload lagi
        });

        // Menangani pemilihan file di input lampiran
        document.getElementById('lampiran').addEventListener('change', function () {
            const file = this.files[0];
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const previewIcon = document.getElementById('previewIcon');
            const removeFileBtn = document.getElementById('removeFile');

            if (file) {
                fileName.textContent = file.name;
                filePreview.style.display = 'block'; // Menampilkan preview

                // Menampilkan ikon preview
                if (file.type.startsWith('image/')) {
                    previewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    previewIcon.src = '/img/pdf.png'; // Ikon PDF
                }

                previewIcon.style.display = 'inline-block'; // Menampilkan ikon
            }
        });

        document.getElementById('removeFile').addEventListener('click', function () {
            // Reset input field dan preview pada kolom input
            document.getElementById('lampiran').value = '';
            document.getElementById('filePreview').style.display = 'none';
            document.getElementById('openUploadModal').style.display = 'block';

            // Reset pada modal overlay
            const uploadBtn = document.getElementById('uploadBtn');
            const fileInfo = document.getElementById('fileInfo');
            const modalFileName = document.getElementById('modalFileName');
            const modalPreviewIcon = document.getElementById('modalPreviewIcon');
            const uploadText = document.querySelector('.upload-text');
            const uploadNote = document.querySelector('.upload-note');
            const selectFileBtn = document.getElementById('selectFileBtn');

            // Reset file yang tampil di overlay
            fileInfo.style.display = 'none';
            modalFileName.textContent = '';
            modalPreviewIcon.style.display = 'none';
            uploadBtn.disabled = true;
            uploadText.style.display = 'block';
            uploadNote.style.display = 'block';
            selectFileBtn.style.display = 'block';

            document.getElementById('selectFileBtn').style.display = 'flex';
            document.getElementById('selectFileBtn').style.justifyContent = 'center';
            document.getElementById('selectFileBtn').style.alignItems = 'center';
        });
    
        // Raroh iki opo
        $(document).ready(function() {
            $('#dropdownMenuButton').on('change', function() {
                // Saat opsi dipilih, teks akan ke kiri
                $(this).css('text-align', 'left');

                // Jika kembali ke opsi default (Pilih), teks akan kembali ke center
                if($(this).val() === null || $(this).val() === "") {
                    $(this).css('text-align', 'center');
                }
            });
        });

        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Show additional fields
            } else {
                fields.style.display = 'none'; // Hide additional fields
            }
        }
        </script>
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