<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Risalah Rapat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-risalah.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('risalah.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Risalah Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('risalah.admin')}}">Risalah Rapat</a>/<a href="#" style="color: #565656;">Edit Risalah Rapat</a>
                </div>
            </div>
        </div>

        <!-- form add undangan -->
        <form method="POST" action="{{ route('risalah.admin') }}">
        @csrf 
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Edit Risalah Rapat</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_dibuat" class="form-label">
                            <img src="/img/risalah/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat
                        </label>
                        <input type="text" name="tgl_dibuat" id="tgl_dibuat" class="form-control" placeholder="mm/dd/yyyy" required>
                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="jenis_document" id="seru-surat" class="form-control" placeholder="Masukkan Seri Surat" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_document" class="form-label">Nomor Surat</label>
                        <input type="text" name="jenis_document" id="nomor-surat" class="form-control" placeholder="Masukkan Nomor Surat" required>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Perihal</label>
                        <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan Perihal / Judul Surat" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/risalah/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
                            <label for="tujuan" class="label-kepada">*Pisahkan dengan titik koma(;) jika penerima lebih dari satu</label>
                        </label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" placeholder="1. Kepada Satu; 2. Kepada Dua; 3. Kepada Tiga" required>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Agenda</label>
                        <input type="text" name="agenda" id="agenda" class="form-control" placeholder="Masukkan Agenda Rapat" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="judul" class="form-label">Tempat</label>
                        <input type="text" name="tempat" id="tempat" class="form-control" placeholder="Masukkan Tempat Rapat" required>
                    </div>
                    <div class="col-md-6" style="border: none;"></div>                        
                </div>
                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\risalah\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi_document">Isi Risalah Rapat</label>
                    </div>
                </div>
                <div id="risalahContainer">
                    <div class="isi-surat-row">
                        <div class="col-md-1">
                            <label for="no">No</label>
                            <input type="text" class="form-control" name="no[]">
                        </div>
                        <div class="col-md-3">
                            <label for="topik">Topik</label>
                            <textarea class="form-control" name="topik[]" placeholder="Topik Pembahasan" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="pembahasan">Pembahasan</label>
                            <textarea class="form-control" name="pembahasan[]" placeholder="Pembahasan" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="tindak-lanjut">Tindak Lanjut</label>
                            <textarea class="form-control" name="tindak_lanjut[]" placeholder="Tindak Lanjut" rows="2"></textarea>
                        </div>
                        <div class="col-md-2">
                            <label for="target">Target</label>
                            <textarea class="form-control" name="target[]" placeholder="Target" rows="2"></textarea>
                        </div>
                        <div class="col-md-2">
                            <label for="pic">PIC</label>
                            <textarea class="form-control" name="pic[]" placeholder="PIC" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-tambah" style="margin-bottom: 10px;">
                    <button class="btn btn-tambah" id="tambahIsiRisalahBtn">Tambah Isi Risalah</button>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nama_pimpinan" class="form-label">Nama yang Bertanda Tangan</label>
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
                <button type="button" class="btn btn-cancel"><a href="{{route ('risalah.admin')}}">Batal</a></button>
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
                        <img src="/img/risalah/cloud-add.png" alt="Icon" style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/risalah/cloud-add.png" alt="Cloud Icon" style="width: 40px; margin-bottom: 10px;">
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

    <script>
        $(document).ready(function() {
            $('#dropdownMenuButton').on('change', function() {
                $(this).css('text-align', 'left');
                if($(this).val() === null || $(this).val() === "") {
                    $(this).css('text-align', 'center');
                }
            });
        });

        document.getElementById('selectFileBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function () {
            const uploadBtn = document.getElementById('uploadBtn');
            if (this.files.length > 0) {
                uploadBtn.disabled = false;
            } else {
                uploadBtn.disabled = true;
            }
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

        document.getElementById('tgl_dibuat').addEventListener('focus', function() {
            this.type = 'date'; 
        });

        document.getElementById('tgl_dibuat').addEventListener('blur', function() {
            if (this.value) { 
                const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                let inputTanggal = new Date(this.value);
                
                let namaHari = hari[inputTanggal.getDay()];
                let tanggal = inputTanggal.getDate().toString().padStart(2, '0');
                let bulan = (inputTanggal.getMonth() + 1).toString().padStart(2, '0');
                let tahun = inputTanggal.getFullYear();
                
                this.type = 'text'; 
                this.value = `${namaHari}, ${tanggal}-${bulan}-${tahun}`; 
            } else {
                this.type = 'text';
                this.placeholder = "mm/dd/yyyy"; 
            }
        });  

        document.getElementById('tambahIsiRisalahBtn').addEventListener('click', function() {
        var newRow = document.createElement('div');
        newRow.classList.add('isi-surat-row', 'row');  
        newRow.style.gap = '0';  

        newRow.innerHTML = `
            <div class="col-md-1">
                <input type="text" class="form-control" name="no[]">
            </div>
            <div class="col-md-3">
                <textarea class="form-control" name="topik[]" placeholder="Topik Pembahasan" rows="2"></textarea>
            </div>
            <div class="col-md-3">
                <textarea class="form-control" name="pembahasan[]" placeholder="Pembahasan" rows="2"></textarea>
            </div>
            <div class="col-md-3">
                <textarea class="form-control" name="tindak_lanjut[]" placeholder="Tindak Lanjut" rows="2"></textarea>
            </div>
            <div class="col-md-2">
                <textarea class="form-control" name="target[]" placeholder="Target" rows="2"></textarea>
            </div>
            <div class="col-md-2">
                <textarea class="form-control" name="pic[]" placeholder="PIC" rows="2"></textarea>
            </div>
        `;
        document.getElementById('risalahContainer').appendChild(newRow);
    });
    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>