<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Memo Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/add-memo.css') }}">
</head>
<body>
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
                    <a href="">Beranda</a>/<a href="#">Memo</a>/<a href="#" style="color: #565656;">Tambah Memo</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <form method="POST" action="{{ route('memo-superadmin.store') }}" enctype="multipart/form-data">
        @csrf 
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Tambah Memo</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat<span class="text-danger">*</span>
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
                        <label for="judul" class="form-label">Perihal<span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan Perihal / Judul Surat" required>
                    </div>

                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/memo-superadmin/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada<span class="text-danger">*</span>
                            <label for="tujuan" class="label-kepada"></label>
                        </label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" placeholder="Kepada yang terhormat" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan<span class="text-danger">*</span></label>
                        <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" required>
                            <option value="" disabled selected style="text-align: left;">--Pilih--</option>
                            @foreach($managers as $manager)
                                <option value="{{  $manager->firstname . ' ' . $manager->lastname  }}">{{ $manager->firstname . ' ' . $manager->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div class="col-md-6 lampiran">
                        <label for="tanda_identitas" class="form-label">Lampiran</label>
                        <div class="upload-wrapper">
                            <button type="button" class="btn btn-primary upload-button" id="openUploadModal">Pilih File</button>
                            <input type="file" id="tanda_identitas" name="tanda_identitas" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                            <div id="filePreview" style="display: none; text-align: center">
                                <img id="previewIcon" src="" alt="Preview" style="max-width: 18px; max-height: 18px; object-fit: contain; display: inline-block; margin-right: 10px;">
                                <span id="fileName"></span>
                                <button type="button" id="removeFile" class="bi bi-x remove-btn" style="border: none; color:red; background-color: white;"></button>
                            </div>
                        </div>
                    </div> -->
                </div>
                

                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\memo-superadmin\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi_memo">Isi Surat<span class="text-danger">*</span></label>
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
                <button type="button" class="btn btn-cancel"><a href="{{route ('memo.superadmin')}}">Batal</a></button>
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
            const tandaIdentitas = document.getElementById('tanda_identitas');
            const fileNameDisplay = document.getElementById('fileName');
            const filePreview = document.getElementById('filePreview');
            const previewIcon = document.getElementById('previewIcon');
            const uploadButton = document.getElementById('openUploadModal');

            // Menampilkan file info di input lampiran setelah file dipilih
            document.getElementById('fileInfoWrapper').style.display = 'flex';
            document.getElementById('fileInfoWrapper').style.alignItems = 'center';
            
            if (file) {
                tandaIdentitas.files = fileInput.files;
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
            document.getElementById('tanda_identitas').value = ''; // Menghapus file yang dipilih
            document.getElementById('filePreview').style.display = 'none'; // Menyembunyikan preview
            document.getElementById('openUploadModal').style.display = 'block'; // Menampilkan tombol upload lagi
        });

        // Menangani pemilihan file di input lampiran
        document.getElementById('tanda_identitas').addEventListener('change', function () {
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
            document.getElementById('tanda_identitas').value = '';
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

        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Show additional fields
            } else {
                fields.style.display = 'none'; // Hide additional fields
            }
        }

        function toggleKategoriBarang() {
            var yaRadio = document.getElementById("ya");
            var jumlahKategoriDiv = document.getElementById("jumlahKategoriDiv");
            var jumlahKategoriInput = document.getElementById("jumlahKategori");
            var barangTable = document.getElementById("barangTable");
            
            if (yaRadio.checked) {
                jumlahKategoriDiv.style.display = "block";
            } else {
                jumlahKategoriDiv.style.display = "none";
                jumlahKategoriInput.value = "";
                barangTable.innerHTML = "";
            }
        }
        
        function generateBarangFields() {
            const jumlahKategori = document.getElementById("jumlahKategori").value;
            const barangTable = document.getElementById("barangTable");
            barangTable.innerHTML = ""; // Hapus isi sebelumnya
            
            if (jumlahKategori > 0) {
                for (let i = 0; i < jumlahKategori; i++) {
                    // Buat row baru untuk setiap kolom
                    const row = document.createElement('div');
                    row.classList.add('row', 'mb-3');
                    row.style.display = 'flex';
                    row.style.gap = '10px';
                    row.style.margin = '10px 47px';

                    // Template untuk input field
                    row.innerHTML = `
                        <div class="col-md-6">
                            <label for="nomor_${i}">Nomor</label>
                            <input type="text" id="nomor_${i}" name="nomor[]" class="form-control" value="${i + 1}" readonly>
                            <input type="hidden" name="memo_divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                        </div>
                        <div class="col-md-6">
                            <label for="barang_${i}">Barang</label>
                            <input type="text" id="barang_${i}" name="barang[]" class="form-control" placeholder="Masukkan barang">
                        </div>
                        <div class="col-md-6">
                            <label for="qty_${i}">Qty</label>
                            <input type="number" id="qty_${i}" name="qty[]" class="form-control" placeholder="Masukkan jumlah">
                        </div>
                        <div class="col-md-6">
                            <label for="satuan_${i}">Satuan</label>
                            <input type="text" id="satuan_${i}" name="satuan[]" class="form-control" placeholder="Masukkan satuan">
                        </div>
                    `;

                    // Tambahkan row ke dalam barangTable
                    barangTable.appendChild(row);
                }
            }
        }

    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>