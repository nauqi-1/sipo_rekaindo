<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perusahaan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/data-perusahaan.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Data Perusahaan</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a> / <a href="#">Setting</a> / <a href="#">Data Lembaga</a>
                </div>
            </div>
        </div>

        <!-- Card untuk tabel -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Bagian input form -->
                    <div class="col-lg-8">
                        <div>
                            <h5 class="heading-company"><b>Data Perusahaan</b></h5>
                            <hr>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Nama Instansi</strong><br>
                                    <label><input type="text" name="nama-instansi" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Alamat Website</strong><br>
                                    <label><input type="text" name="alamat-web" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Telp</strong><br>
                                    <label><input type="text" name="telp" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Email</strong><br>
                                    <label><input type="email" name="email" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Alamat</strong><br>
                                    <label><input type="text" name="alamat" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Logo Perusahaan</strong><br>
                                    <label for="company-photo">
                                        <input type="file" id="company-photo" accept="image/*" class="form-control" disabled>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                        <img src="" alt="Logo Perusahaan" class="company-image" id="company-photo-preview">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="edit-button">Edit</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancel-button">Cancel</button>
                    <button type="submit" class="btn btn-primary d-none" id="save-button">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const editButton = document.getElementById("edit-button");
            const cancelButton = document.getElementById("cancel-button");
            const saveButton = document.getElementById("save-button");
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"]');
            const photoInput = document.getElementById("company-photo");
            const photoPreview = document.getElementById("company-photo-preview");

            // Set gambar default
            const defaultImage = "/img/setting/question.png"; // Path gambar default
            photoPreview.src = defaultImage; // Set gambar default awal

            // Disable all inputs at the start
            inputs.forEach(input => input.disabled = true);
            photoInput.disabled = true;

            // Ketika tombol Edit ditekan
            editButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = false);
                photoInput.disabled = false;
                editButton.classList.add("d-none");
                cancelButton.classList.remove("d-none");
                saveButton.classList.remove("d-none");
            });

            // Ketika tombol Cancel ditekan
            cancelButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = true);
                photoInput.disabled = true;
                editButton.classList.remove("d-none");
                cancelButton.classList.add("d-none");
                saveButton.classList.add("d-none");
            });

            // Ketika tombol Save Changes ditekan
            saveButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = true);
                photoInput.disabled = true;
                editButton.classList.remove("d-none");
                cancelButton.classList.add("d-none");
                saveButton.classList.add("d-none");
                console.log("Data telah disimpan");
            });

            // Menangani perubahan gambar
            photoInput.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        photoPreview.src = e.target.result; // Update dengan gambar baru
                    };
                    reader.readAsDataURL(file); // Baca file gambar
                } else {
                    // Jika tidak ada file, tampilkan gambar default
                    photoPreview.src = defaultImage;
                }
            });
        });
            </script>
</body>
</html>
