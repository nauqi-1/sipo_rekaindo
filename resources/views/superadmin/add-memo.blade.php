<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Super Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/add-memo.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Add Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a>/<a href="#">Memo</a>/<a href="#">Add Memo</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><b>Form Add Memo</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tgl. Surat
                        </label>
                        <input type="date" name="tgl_surat" id="tgl_surat" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="seri_surat" id="seri_surat" class="form-control" placeholder="Masukkan Seri Surat" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" placeholder="Masukkan Nomor Surat" required>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_surat" class="form-label">Jenis Surat</label>
                        <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                            <option value="" disabled selected style="text-align: left;">--Pilih--</option>
                            <option value="undangan">Surat Undangan</option>
                            <option value="biasa">Surat Biasa</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="kepada" class="form-label">
                            <img src="/img/memo-superadmin/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
                            <label for="kepada" class="label-kepada">*Pisahkan dengan titik koma(;) jika penerima lebih dari satu</label>
                        </label>
                        <input type="text" name="kepada" id="kepada" class="form-control" placeholder="1. Kepada Satu; 2. Kepada Dua; 3. Kepada Tiga" required>
                    </div>
                    <div class="col-md-6" >
                        <label for="perihal" class="form-label">Perihal</label>
                        <input type="text" name="perihal" id="perihal" class="form-control" placeholder="Masukkan Perihal/Judul Surat" required>
                    </div>
                </div>

                <div class="row mb-4 kolom1-row">
                    <div class="col-md-12">
                        <label for="isi-surat">Isi Surat</label>
                        <div class="rich-text-editor">
                            <!-- Placeholder untuk editor teks -->
                            <textarea id="isi-surat" name="isi-surat"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 kolom1-row">
                    <div class="col-md-12">
                        <label for="jenis_surat" class="form-label">Nama Pimpinan</label>
                        <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                            <option value="" disabled selected>--Pilih--</option>
                            <option value="undangan">Surat Undangan</option>
                            <option value="biasa">Surat Biasa</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-4 kolom1-row">
                    <div class="col-md-12">
                        <label for="upload_file" class="form-label">Tanda Identitas</label>
                        <div class="upload-wrapper">
                            <button type="button" class="btn btn-primary upload-button">Choose File</button>
                            <input type="file" id="upload_file" name="upload_file" class="form-control-file" hidden>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>