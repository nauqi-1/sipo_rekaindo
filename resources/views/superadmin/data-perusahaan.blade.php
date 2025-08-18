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
    <div class="perusahaan">
        <div class="card">
            <h3><b>Data Perusahaan</b></h3>
            <hr>
            <div class="row">
                <!-- Kolom Form -->
                <div class="col-md-8">
                    <form id="formPerusahaan" action="{{ route('data-perusahaan.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
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
                        <div class="col-md-6">
                            <label class="form-label">Nama Instansi</label>
                            <input type="text" class="form-control" name="nama_instansi" value="{{ $perusahaan->nama_instansi ?? '' }}" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat Situs Web</label>
                            <input type="text" class="form-control" name="alamat_web" value="{{ $perusahaan->alamat_web ?? '' }}" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" name="telepon" value="{{ $perusahaan->telepon ?? '' }}" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $perusahaan->email ?? '' }}" readonly required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" readonly required>{{ $perusahaan->alamat ?? '' }}</textarea>
                        </div>
                        @if(Auth::user()->role->nm_role == 'superadmin')
                        <div class="file col-md-6">
                            <label class="form-label">Logo Perusahaan</label>
                            <input type="file" class="form-control" name="logo" accept=".jpg,.jpeg,.png,.svg" disabled>
                        </div>

                        <div id="buttonGroup">
                            <button type="button" class="btn btn-primary" id="editButton">Edit</button>
                            <button type="button" class="btn btn-secondary d-none" id="cancelButton">Batal</button>
                            <button type="submit" class="btn btn-success d-none" id="saveButton">Simpan</button>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Kolom Logo -->
                <div class="col-md-3 d-flex align-items-center justify-content-center">
                    <div class="border rounded p-3" style="width: 250px; height: 250px; display: flex; align-items: center; justify-content: center;">
                        @if(isset($perusahaan) && $perusahaan->logo)
                        <img src="data:image/png;base64,{{ $perusahaan->logo }}" alt="Logo Perusahaan" width="150">
                        @else
                        <p>Logo tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successAddDataPerusahaanModal" tabindex="-1" aria-labelledby="successModalLabel"
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

<div class="modal fade" id="noChange" tabindex="-1" aria-labelledby="successModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3"
                    style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Data Tidak Berubah</b></h5>
                <p class="mt-2">Tidak ada perubahan data</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border: 2px solid #dc3545; border-radius: 12px;">
            <div class="modal-body">
                <!-- Error Icon -->
                <i class="fa-solid fa-xmark" style="color: #ff0000; font-size: 80px;"></i>
                <!-- Error Message -->
                <h5 class="modal-title text-danger" id="errorModalLabel"><b>Gagal</b></h5>
                <p class="mt-2 text-dark" id="errorPasswordMessage">Terjadi kesalahan</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('editButton').addEventListener('click', function() {
        let inputs = document.querySelectorAll('#formPerusahaan input, #formPerusahaan textarea');
        inputs.forEach(input => input.removeAttribute('readonly'));
        document.querySelector('input[name="logo"]').removeAttribute('disabled');

        document.getElementById('editButton').classList.add('d-none');
        document.getElementById('cancelButton').classList.remove('d-none');
        document.getElementById('saveButton').classList.remove('d-none');
    });

    document.getElementById('cancelButton').addEventListener('click', function() {
        let inputs = document.querySelectorAll('#formPerusahaan input, #formPerusahaan textarea');
        inputs.forEach(input => input.setAttribute('readonly', true));
        document.querySelector('input[name="logo"]').setAttribute('disabled', true);

        document.getElementById('editButton').classList.remove('d-none');
        document.getElementById('cancelButton').classList.add('d-none');
        document.getElementById('saveButton').classList.add('d-none');
    });

    document.addEventListener("DOMContentLoaded", function() {
        var successMessage = "{{ session('success') }}";
        var errorMessage = "{{ session('error') }}";

        if (successMessage === 'Data perusahaan berhasil diperbarui') {
            var successModal = new bootstrap.Modal(document.getElementById("successAddDataPerusahaanModal"));
            successModal.show();
            setTimeout(function() {
                successModal.hide();
            }, 1500);
        } else if (successMessage === 'Tidak ada perubahan data yang disimpan.') {
            var successModal = new bootstrap.Modal(document.getElementById("noChange"));
            successModal.show();
            setTimeout(function() {
                successModal.hide();
            }, 1500);
        } else if (errorMessage) {
            var errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
            errorModal.show();
            setTimeout(function() {
                errorModal.hide();
            }, 1500);
        }
    });
</script>
@endsection