@extends('layouts.superadmin')

@section('title', 'Data Perusahaan')

@section('content')
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
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a> / <a href="#">Pengaturan</a> / <a href="#" style="color: #565656;">Data Perusahaan</a>
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
                        </div>
                        <form action="{{ route('data-perusahaan') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Menggunakan PUT untuk update -->
                            <ul class="list-group">
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Nama Instansi</strong><br>
                                        <input type="text" name="nama_instansi" class="form-control" value="{{ $perusahaan->nama_instansi ?? '' }}" disabled>
                                    </div>
                                </li>
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Alamat Situs Web</strong><br>
                                        <input type="text" name="alamat_web" class="form-control" value="{{ $perusahaan->alamat_web ?? '' }}" disabled>
                                    </div>
                                </li>
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Telepon</strong><br>
                                        <input type="text" name="telepon" class="form-control" value="{{ $perusahaan->telepon ?? '' }}" disabled>
                                    </div>
                                </li>
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Email</strong><br>
                                        <input type="email" name="email" class="form-control" value="{{ $perusahaan->email ?? '' }}" disabled>
                                    </div>
                                </li>
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Alamat</strong><br>
                                        <input type="text" name="alamat" class="form-control" value="{{ $perusahaan->alamat ?? '' }}" disabled>
                                    </div>
                                </li>
                                <li class="list-group-card">
                                    <div>
                                        <strong class="list-group-text">Logo Perusahaan</strong><br>
                                        <input type="file" name="logo" class="form-control" accept="image/*" disabled>
                                    </div>
                                </li>
                            </ul>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="edit-button">Edit</button>
                                <button type="button" class="btn btn-secondary d-none" id="cancel-button">Batal</button>
                                <button type="submit" class="btn btn-primary d-none" id="save-button">Simpan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Gambar Logo Perusahaan -->
                    <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                    @php
                        $logoPath = $perusahaan->logo ? asset('image/' . $perusahaan->logo) : asset('img/setting/question.png');
                    @endphp
                    <img src="{{ $logoPath }}" alt="Logo Perusahaan" class="company-image" id="company-photo-preview">
                    <!-- Debugging -->
                    <p>Path Gambar: {{ $logoPath }}</p>
                    </div>
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
            const photoInput = document.querySelector('input[name="logo"]');
            const photoPreview = document.getElementById("company-photo-preview");

            // Set gambar default jika tidak ada
            const defaultImage = "/img/setting/question.png"; // Path gambar default
            if (!photoPreview.src) {
                photoPreview.src = defaultImage; // Set gambar default awal
            }

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
                    photoPreview.src = defaultImage; // Set gambar default jika tidak ada file
                }
            });
        });
    </script>
@endsection
