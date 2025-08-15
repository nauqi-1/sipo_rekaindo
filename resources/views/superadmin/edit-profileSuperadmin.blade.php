@extends('layouts.superadmin')

@section('title', 'Edit Profil')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', sans-serif;
    }

    .card {
        border: none;
        background-color: #fff;
    }

    h4.fw-semibold {
        font-weight: 600;
    }

    .form-label {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.875rem;
    }

    input.form-control {
        font-size: 0.9rem;
        padding: 0.6rem 0.75rem;
        border-radius: 0.65rem;
    }

    input.form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    input[readonly] {
        background-color: #f1f1f1;
        color: #888;
    }

    #profileImagePreview {
        transition: 0.3s ease;
    }

    #profileImagePreview:hover {
        opacity: 0.8;
        transform: scale(1.02);
    }

    #photoOverlay,
    #confirmOverlay {
        backdrop-filter: blur(3px);
        z-index: 1050;
    }

    .btn {
        border-radius: 2rem;
        font-size: 0.875rem;
    }

    .btn-warning {
        background-color: #fbbf24;
        border-color: #fbbf24;
        color: #000;
    }

    .btn-warning:hover {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }

    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .input-group-text {
        background-color: #f3f4f6;
        border: 1px solid #ced4da;
        padding: 0.55rem 0.75rem;
        border-radius: 0.65rem;
    }

    .rounded-circle.shadow {
        border: 3px solid #e2e8f0;
    }

    .w-45 {
        width: 45%;
    }

    @media (max-width: 768px) {
        .card {
            padding: 1.5rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        #editButtons {
            text-align: center;
        }
    }
</style>

<div class="container mt-4">
    <div class="card p-4 shadow-sm rounded-4 position-relative">
        <div class="position-absolute top-0 end-0 mt-3 me-3">
            <button type="button" class="btn btn-warning px-4" onclick="enableEditProfile()">Edit Profil</button>
        </div>

        <form id="formProfile" action="{{ route('superadmin.updateProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="profile_image" id="profileImage" class="d-none" accept="image/*">

            <div class="text-center mb-4">
                <div class="position-relative d-inline-block" onclick="openOverlay()" style="cursor:pointer;">
                    @if(Auth::user()->profile_image)
                    <img id="profileImagePreview" src="data:image/jpeg;base64,{{ Auth::user()->profile_image }}" alt="Foto Profil" class="rounded-circle shadow" style="width: 130px; height: 130px; object-fit: cover;">
                    @else
                    <img id="profileImagePreview" src="../assets/images/user/default1.png" alt="No Profile" class="rounded-circle shadow" style="width: 130px; height: 130px; object-fit: cover;">
                    @endif
                </div>
                <h6 class="mt-3 fw-bold">{{ Auth::user()->username }}</h6>
                <small class="text-muted">{{ Auth::user()->email }}</small>
            </div>

            <div id="profileEdit">
                @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fa-solid fa-xmark me-2" style="color: #ff0000; font-size: 20px;"></i>
                    <div>
                        <strong>Perhatian!</strong> Terdapat kesalahan input:
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Posisi</label>
                        <input type="text" class="form-control" value="{{ $position }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Depan</label>
                        <input type="text" name="firstname" class="form-control profile-input" value="{{ Auth::user()->firstname }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="lastname" class="form-control profile-input" value="{{ Auth::user()->lastname }}" required readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" name="username" class="form-control profile-input" value="{{ Auth::user()->username }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone_number" class="form-control profile-input" value="{{ Auth::user()->phone_number }}" required readonly>
                    </div>
                </div>

                <div id="passwordFields" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password Baru">
                                <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                                    <i class="fa fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="invalid-feedback" id="passwordError" style="display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi Password">
                                <span class="input-group-text" onclick="togglePassword('password_confirmation', this)" style="cursor:pointer;">
                                    <i class="fa fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="invalid-feedback" id="passwordConfirmError" style="display: block;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4" id="editButtons" style="display: none;">
                <button type="submit" class="btn btn-primary px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Overlay Edit Foto --}}
<div id="photoOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center"
    style="background: rgba(0,0,0,0.6); z-index: 1050;">
    <div class="bg-white p-4 rounded-4 text-center shadow position-relative" style="width: 300px;">
        <button onclick="closeOverlay()" class="btn-close position-absolute top-0 end-0 m-2"></button>
        <h5 class="mb-3">Ubah Foto Profil</h5>

        <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="triggerImageUpload()">
            <i class="fa fa-pen me-2"></i>Edit Foto
        </button>

        <form action="{{ route('superadmin.deletePhoto') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="fa fa-trash me-2"></i>Hapus Foto
            </button>
        </form>
    </div>
</div>

{{-- Overlay Konfirmasi Foto Baru --}}
<div id="confirmOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center"
    style="background: rgba(0,0,0,0.6); z-index: 1051;">
    <div class="bg-white p-4 rounded-4 text-center shadow position-relative" style="width: 300px;">
        <h5 class="mb-3">Gunakan foto ini?</h5>
        <img id="newImagePreview" src="" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
        <div class="d-flex justify-content-between">
            <button class="btn btn-primary w-45" onclick="confirmImage()">Iya</button>
            <button class="btn btn-secondary w-45" onclick="cancelImage()">Tidak</button>
        </div>
    </div>
</div>

<div class="modal fade" id="successAddProfilModal" tabindex="-1" aria-labelledby="successModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3"
                    style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengubah Profil</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorPasswordModal" tabindex="-1" aria-labelledby="errorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border: 2px solid #dc3545; border-radius: 12px;">
            <div class="modal-body">
                <!-- Error Icon -->
                <i class="fa-solid fa-xmark" style="color: #ff0000; font-size: 80px;"></i>
                <!-- Error Message -->
                <h5 class="modal-title text-danger" id="errorModalLabel"><b>Gagal</b></h5>
                <p class="mt-2 text-dark" id="errorPasswordMessage">Terjadi kesalahan pada input password</p>
            </div>
        </div>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmError = document.getElementById('passwordConfirmError');

    function validatePasswords() {
        let valid = true;

        // validasi password minimal 6 karakter jika ada isian
        if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
            passwordError.textContent = 'Password minimal 6 karakter.';
            passwordInput.classList.add('is-invalid');
            passwordError.style.display = 'block';
            valid = false;
        } else {
            passwordError.textContent = '';
            passwordInput.classList.remove('is-invalid');
            passwordError.style.display = 'none';
        }

        // validasi konfirmasi password
        if (passwordInput.value.length > 0 && passwordConfirmInput.value.length === 0) {
            // password diisi, tapi konfirmasi kosong
            passwordConfirmError.textContent = 'Field wajib diisi.';
            passwordConfirmInput.classList.add('is-invalid');
            passwordConfirmError.style.display = 'block';
            valid = false;
        } else if (passwordConfirmInput.value.length > 0) {
            // konfirmasi diisi, cek sama atau tidak
            if (passwordInput.value !== passwordConfirmInput.value) {
                passwordConfirmError.textContent = 'Konfirmasi password tidak sama dengan password.';
                passwordConfirmInput.classList.add('is-invalid');
                passwordConfirmError.style.display = 'block';
                valid = false;
            } else {
                passwordConfirmError.textContent = '';
                passwordConfirmInput.classList.remove('is-invalid');
                passwordConfirmError.style.display = 'none';
            }
        } else {
            // password kosong dan konfirmasi kosong
            passwordConfirmError.textContent = '';
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmError.style.display = 'none';
        }

        return valid;
    }

    passwordInput.addEventListener('input', validatePasswords);
    passwordConfirmInput.addEventListener('input', validatePasswords);

    const form = document.getElementById('formProfile');
    form.addEventListener('submit', function(event) {
        if (!validatePasswords()) {
            event.preventDefault();
            var errorModal = new bootstrap.Modal(document.getElementById("errorPasswordModal"));
            errorModal.show();
            setTimeout(function() {
                errorModal.hide();
            }, 1500);
            if (passwordInput.classList.contains('is-invalid')) {
                passwordInput.focus();
            } else {
                passwordConfirmInput.focus();
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success') === 'Profil berhasil diperbarui.') // merujuk ke parameter controller risalah store
        var successModal = new bootstrap.Modal(document.getElementById("successAddProfilModal"));
        successModal.show();
        setTimeout(function() {
            successModal.hide();
        }, 1500);
        @endif
    });

    function enableEditProfile() {
        const inputs = document.querySelectorAll('.profile-input');
        inputs.forEach(input => input.removeAttribute('readonly'));

        document.getElementById('editButtons').style.display = 'block';
        document.getElementById('passwordFields').style.display = 'block';
    }

    function openOverlay() {
        document.getElementById('photoOverlay').classList.remove('d-none');
        document.getElementById('photoOverlay').style.display = 'flex';
    }

    function closeOverlay() {
        document.getElementById('photoOverlay').classList.add('d-none');
        document.getElementById('photoOverlay').style.display = 'none';
    }

    function triggerImageUpload() {
        closeOverlay();
        document.getElementById('profileImage').click();
    }

    function togglePassword(fieldId, iconElement) {
        const field = document.getElementById(fieldId);
        const icon = iconElement.querySelector('i');
        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            field.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    let selectedFile = null;
    document.getElementById('profileImage').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('newImagePreview').src = e.target.result;
                document.getElementById('confirmOverlay').classList.remove('d-none');
                document.getElementById('confirmOverlay').style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    function confirmImage() {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImagePreview').src = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
        document.getElementById('confirmOverlay').classList.add('d-none');
        document.getElementById('confirmOverlay').style.display = 'none';
    }

    function cancelImage() {
        document.getElementById('profileImage').value = '';
        document.getElementById('confirmOverlay').classList.add('d-none');
        document.getElementById('confirmOverlay').style.display = 'none';
    }

    document.getElementById('photoOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeOverlay();
    });
</script>
@endsection