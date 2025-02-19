@extends('layouts.superadmin')

@section('title', 'Profil')
      
@section('content')
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
    <div class="profile">
        <div class="card">
            <div class="card-header">
                <label for="label" class="heading-profile">Foto Profil</label>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="modal-content">
                        <div class="profil-container" style="margin: 30px;">
                            <img id="profileImage" class="rounded-circle" src="/img/setting/default-logo.png" alt="profile-logo">
                            <div class="profil-info">
                                <h5>Super Admin</h5>
                                <p>superadmin@gmail.com</p>
                                <button id="editPhotoButton" data-bs-toggle="modal" data-bs-target="#uploadModal">Edit Foto</button>
                            </div>
                        </div>
                        <hr style="padding: 0; margin: 0;">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="user_id" class="form-label">ID Pengguna :</label>
                                    <input type="text" name="user_id" id="user_id" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email_id" class="form-label">Email :</label>
                                    <input type="text" name="email_id" id="email_id"  required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Nama Depan :</label>
                                    <input type="text" name="first_name" id="first_name" required autocomplete="firstname">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nama Akhir :</label>
                                    <input type="text" name="last_name" id="last_name" required autocomplete="lastname">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Nama Pengguna :</label>
                                    <input type="text" name="username" id="username"  required autocomplete="username">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">No. Telepon :</label>
                                    <input type="text" name="phone_number" id="phone_number" required autocomplete="phone_number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Kata Sandi :</label>
                                    <input type="password" name="password" id="password"  required autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi :</label>
                                    <input type="password" name="confirm_password" id="confirm_password" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Divisi :</label>
                                    <input type="text" name="divisi" id="divisi"  required autocomplete="divisi">
                                </div>
                                <div class="col-md-6">
                                    <label for="divisi" class="form-label">Posisi :</label>
                                    <input type="text" name="position" id="position" required autocomplete="position">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel">Batal</button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
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
@endsection