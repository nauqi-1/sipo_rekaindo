@extends('layouts.superadmin')

@section('content')
<div class="container mt-4">
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
    <div class="card p-4">
        <h4 class="fw-bold">Data Perusahaan</h4>
        <div class="row">
            <!-- Kolom Form -->
            <div class="col-md-8">
                <form id="formPerusahaan" action="{{ route('data-perusahaan.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Instansi</label>
                        <input type="text" class="form-control" name="nama_instansi" value="{{ $perusahaan->nama_instansi ?? '' }}" readonly required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Situs Web</label>
                        <input type="text" class="form-control" name="alamat_web" value="{{ $perusahaan->alamat_web ?? '' }}" readonly required>

                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Telepon</label>
                        <input type="text" class="form-control" name="telepon" value="{{ $perusahaan->telepon ?? '' }}" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $perusahaan->email ?? '' }}" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" name="alamat" readonly required>{{ $perusahaan->alamat ?? '' }}</textarea>
                    </div>
                    @if(Auth::user()->role->nm_role == 'superadmin')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Logo Perusahaan</label>
                        <input type="file" class="form-control" name="logo" accept="image/*" disabled>
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
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <div class="border rounded p-3" style="width: 250px; height: 250px; display: flex; align-items: center; justify-content: center;">
                    @if ($perusahaan && $perusahaan->logo)
                        <img src="{{ asset('storage/logos/' . $perusahaan->logo) }}" alt="Logo Perusahaan" class="img-fluid rounded" style="max-width: 100%; max-height: 100%;">
                    @else
                        <img src="{{ asset('default-logo.png') }}" alt="No Logo" class="img-fluid" style="opacity: 0.5; width: 80%;">
                    @endif
                </div>
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
</script>
@endsection