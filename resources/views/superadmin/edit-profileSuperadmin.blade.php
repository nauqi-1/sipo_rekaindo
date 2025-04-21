@extends('layouts.superadmin')

@section('title', 'Edit Profil')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ===== Custom Profile Edit Page ===== */
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

.breadcrumb-item + .breadcrumb-item::before {
    content: '/';
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

#photoOverlay {
    backdrop-filter: blur(3px);
    z-index: 1050;
}

#photoOverlay .bg-white {
    border-radius: 1.25rem;
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

.btn-light {
    background-color: #f3f4f6;
    color: #111827;
}

.input-group-text {
    background-color: #f3f4f6;
    border: 1px solid #ced4da;
    padding: 0.55rem 0.75rem;
    border-radius: 0.65rem;
}

small.text-muted {
    font-size: 0.85rem;
}

.rounded-circle.shadow {
    border: 3px solid #e2e8f0;
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
    <div class="card p-4 shadow-sm rounded-4">
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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">NIP</label>
                        <input type="text" class="form-control rounded-3" value="{{ Auth::user()->id }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Email</label>
                        <input type="email" class="form-control rounded-3" value="{{ Auth::user()->email }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Nama Depan</label>
                        <input type="text" name="firstname" class="form-control rounded-3" value="{{ Auth::user()->firstname }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Nama Belakang</label>
                        <input type="text" name="lastname" class="form-control rounded-3" value="{{ Auth::user()->lastname }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Nama Pengguna</label>
                        <input type="text" name="username" class="form-control rounded-3" value="{{ Auth::user()->username }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Nomor Telepon</label>
                        <input type="text" name="phone_number" class="form-control rounded-3" value="{{ Auth::user()->phone_number }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Password</label>
                        <div class="input-group rounded-3">
                            <input type="password" name="password" id="password" class="form-control rounded-start-3" placeholder="********">
                            <span class="input-group-text rounded-end-3" onclick="togglePassword('password', this)" style="cursor:pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted small">Konfirmasi Password</label>
                        <div class="input-group rounded-3">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-start-3" placeholder="********">
                            <span class="input-group-text rounded-end-3" onclick="togglePassword('password_confirmation', this)" style="cursor:pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4" id="profileButtons">
                <button type="button" class="btn btn-warning rounded-pill px-4" onclick="enableEditProfile()">Edit Profil</button>
            </div>
            <div class="text-end mt-4" id="editButtons" style="display: none;">
                <button type="button" class="btn btn-light rounded-pill me-2" onclick="cancelEdit()">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Overlay --}}
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

<script>
    function enableEditProfile() {
        document.getElementById('profileEdit').style.display = 'block';
        document.getElementById('profileButtons').style.display = 'none';
        document.getElementById('editButtons').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('profileEdit').style.display = 'block';
        document.getElementById('profileButtons').style.display = 'block';
        document.getElementById('editButtons').style.display = 'none';
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

    // Tutup overlay jika klik di luar box
    document.getElementById('photoOverlay').addEventListener('click', function (e) {
        if (e.target === this) closeOverlay();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const preview = document.getElementById('profileImagePreview');
        if (preview) preview.style.opacity = '1';
    });
</script>
@endsection
