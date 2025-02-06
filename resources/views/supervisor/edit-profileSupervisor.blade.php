<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supervisor/edit-profile.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Profil</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a> / <a href="#" style="color: #565656;">Edit Profil</a>
                </div>
            </div>
        </div>

        <!-- Card untuk tabel -->
        <div class="card">
            <div class="card-header">
                <label for="label" class="heading-profile">Foto Profil Supervisor</label>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="modal-content">
                        <div class="profil-container" style="margin: 30px;">
                            <img id="profileImage" class="rounded-circle" src="/img/setting/default-logo.png" alt="profile-logo">
                            <div class="profil-info">
                                <h5>Supervisor</h5>
                                <p>supervisor@gmail.com</p>
                                <button id="editPhotoButton" data-bs-toggle="modal" data-bs-target="#uploadModal">Edit Foto</button>
                            </div>
                        </div>
                        <hr>
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">ID Pengguna :</label>
                                    <input type="text" name="user_id" id="user_id" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email_id" class="form-label">Email :</label>
                                    <input type="text" name="email_id" id="email_id" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Nama Depan :</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" required autocomplete="firstname">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nama Akhir :</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" required autocomplete="lastname">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Nama Pengguna :</label>
                                    <input type="text" name="username" id="username" class="form-control" required autocomplete="username">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">No. Telepon :</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" required autocomplete="phone_number">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Kata Sandi :</label>
                                    <input type="text" name="password" id="password" class="form-control" required autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi :</label>
                                    <input type="text" name="confirm_password" id="confirm_password" class="form-control" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Divisi</label>
                                    <input type="text" name="divisi" id="divisi" class="form-control" required autocomplete="divisi">
                                </div>
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Posisi</label>
                                    <input type="text" name="position" id="position" class="form-control" required autocomplete="position">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="#">Batal</a></button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Upload file edit foto profil -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-container">
                        <img src="img/setting/cloud-add.png" alt="Cloud Icon" style="width: 50px; margin-bottom: 10px;">
                        <p>Choose a file or drag & drop it here</p>
                        <p class="text-muted">Image file size no more than 20MB</p>
                        <input type="file" id="fileInput" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="uploadBtn" disabled>Upload</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
     document.getElementById("editPhotoButton").addEventListener("click", function () {
        const fileInput = document.getElementById("fileInput");
        fileInput.value = ""; // Reset input file setiap kali modal dibuka
        document.getElementById("uploadBtn").disabled = true; // Nonaktifkan tombol upload
    });

    // Menangani perubahan file untuk validasi dan preview
    const fileInput = document.getElementById("fileInput");
    fileInput.addEventListener("change", handleFileChange);

    function handleFileChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.startsWith("image/")) {
            alert("Only image files are allowed.");
            fileInput.value = ""; // Reset input file jika file bukan gambar
            return;
        }

        if (file.size > 20 * 1024 * 1024) {
            alert("File size exceeds 20MB.");
            fileInput.value = ""; // Reset input file jika ukuran file terlalu besar
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const imagePreview = document.createElement("img");
            imagePreview.src = e.target.result;
            imagePreview.alt = "Preview Image";
            imagePreview.style.width = "100px"; // Sesuaikan ukuran preview
            imagePreview.style.borderRadius = "50%"; // Pastikan berbentuk lingkaran
            imagePreview.style.marginTop = "10px";

            const uploadContainer = document.querySelector(".upload-container");
            uploadContainer.innerHTML = ` 
                <img src="img/setting/cloud-add.png" alt="Cloud Icon" style="width: 50px; margin-bottom: 10px;">
                <p>Choose a file or drag & drop it here</p>
                <p class="text-muted">Image file size no more than 20MB</p>
            `;
            uploadContainer.appendChild(imagePreview);
            document.getElementById("uploadBtn").disabled = false; // Aktifkan tombol upload
        };
        reader.readAsDataURL(file);
    }

    // Fungsi upload file yang menghindari pembekuan
    document.getElementById("uploadBtn").addEventListener("click", function () {
        const file = fileInput.files[0];
        if (file) {
            // Menjalankan proses upload secara asinkron untuk menghindari pembekuan halaman
            uploadImageAsync(file);
        } else {
            alert("No file selected.");
        }
    });

    async function uploadImageAsync(file) {
        try {
            // Disable tombol upload agar tidak ada klik ganda
            document.getElementById("uploadBtn").disabled = true;
            
            const reader = new FileReader();
            
            reader.onload = function (e) {
                const profileImage = document.getElementById("profileImage");
                profileImage.src = e.target.result; // Ganti src dengan gambar baru

                // Setelah gambar berhasil diubah, lakukan proses berikut di luar thread utama
                setTimeout(() => {
                    alert(`File "${file.name}" uploaded successfully.`);
                    fileInput.value = ""; // Reset input file
                    // Tutup modal setelah upload selesai
                    const uploadModal = bootstrap.Modal.getInstance(document.getElementById("uploadModal"));
                    uploadModal.hide();
                }, 0); // Pastikan proses dilakukan di luar thread utama (UI thread)
            };
            
            reader.readAsDataURL(file);
        } catch (error) {
            console.error("Error uploading image:", error);
            alert("An error occurred while uploading the file.");
        }
    }

    // Mengatur pengaturan untuk drag & drop
    const uploadContainer = document.querySelector(".upload-container");
    uploadContainer.addEventListener("dragover", function (event) {
        event.preventDefault();
        this.style.backgroundColor = "#f1f1f1";
    });

    uploadContainer.addEventListener("dragleave", function () {
        this.style.backgroundColor = "#f9f9f9";
    });

    uploadContainer.addEventListener("drop", function (event) {
        event.preventDefault();
        this.style.backgroundColor = "#f9f9f9";

        const files = event.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith("image/")) {
            fileInput.files = files;
            handleFileChange({ target: { files } });
        } else {
            alert("Only image files are allowed.");
        }
    });
    </script>
</body>
</html>