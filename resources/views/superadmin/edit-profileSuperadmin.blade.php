
@extends('layouts.superadmin')


@section('title', 'Edit Profil')

@section('content')
<div class="container mt-4">
    <div class="header">
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

    <div class="card p-4">
        <h4 class="fw-bold">Informasi Profil</h4>
        <div class="row">
            <!-- Kolom Form -->
            <div class="col-md-8">
                <form id="formProfile" action="{{ route('superadmin.updateProfile') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Depan</label>
                        <input type="text" class="form-control" name="firstname" value="{{ Auth::user()->firstname }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Akhir</label>
                        <input type="text" class="form-control" name="lastname" value="{{ Auth::user()->lastname }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon</label>
                        <input type="text" class="form-control" name="phone_number" value="{{ Auth::user()->phone_number }}" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control" name="username" value="{{ Auth::user()->username }}" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Divisi</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->divisi->nm_divisi ?? '-' }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Posisi</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->position->nm_position ?? '-' }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Profil</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>

                    <div id="buttonGroup">
                        <button type="button" class="btn btn-primary" id="editButton">Edit</button>
                        <button type="button" class="btn btn-secondary d-none" id="cancelButton">Batal</button>
                        <button type="submit" class="btn btn-success d-none" id="saveButton">Simpan</button>
                    </div>
                </form>
            </div>

            <!-- Kolom Foto Profil -->
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <div class="border rounded p-3" style="width: 250px; height: 250px; display: flex; align-items: center; justify-content: center;">
                    @if(Auth::user()->profile_image)
                        <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" alt="Foto Profil" class="img-fluid rounded" style="max-width: 100%; max-height: 100%;">
                    @else
                        <img src="{{ asset('default-profile.png') }}" alt="No Profile" class="img-fluid" style="opacity: 0.5; width: 80%;">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('editButton').addEventListener('click', function() {
        let inputs = document.querySelectorAll('#formProfile input');
        inputs.forEach(input => input.removeAttribute('readonly'));
        document.querySelector('input[name="profile_image"]').removeAttribute('disabled');

        document.getElementById('editButton').classList.add('d-none');
        document.getElementById('cancelButton').classList.remove('d-none');
        document.getElementById('saveButton').classList.remove('d-none');
    });

    document.getElementById('cancelButton').addEventListener('click', function() {
        let inputs = document.querySelectorAll('#formProfile input');
        inputs.forEach(input => input.setAttribute('readonly', true));
        document.querySelector('input[name="profile_image"]').setAttribute('disabled', true);

        document.getElementById('editButton').classList.remove('d-none');
        document.getElementById('cancelButton').classList.add('d-none');
        document.getElementById('saveButton').classList.add('d-none');
    });
</script>
@endsection
