@extends('layouts.app')

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
    <div class="data-perusahaan">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Bagian input form -->
                    <div class="col-lg-8">
                        <div>
                            <h5 class="heading-company"><b>Data Perusahaan</b></h5>
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
                                    <strong class="list-group-text">Alamat Situs Web</strong><br>
                                    <label><input type="text" name="alamat-web" class="form-control" disabled></label>
                                </div>
                            </li>
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Telepon</strong><br>
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
                            @if(Auth::user()->role == 'superadmin')
                            <li class="list-group-card">
                                <div>
                                    <strong class="list-group-text">Logo Perusahaan</strong><br>
                                    <label for="company-photo">
                                        <input type="file" id="company-photo" accept="image/*" class="form-control" disabled>
                                    </label>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-lg-4 d-flex flex-column align-items-center justify-content-center">
                        <img src="" alt="Logo Perusahaan" class="company-image" id="company-photo-preview">
                    </div>
                </div>
                <div class="modal-footer">
                @if(Auth::user()->role == 'superadmin')
                    <button type="button" class="btn btn-primary" id="edit-button">Edit</button>
                @endif
                    <button type="button" class="btn btn-secondary d-none" id="cancel-button">Batal</button>
                    <button type="submit" class="btn btn-primary d-none" id="save-button">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection