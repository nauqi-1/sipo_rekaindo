@extends('layouts.superadmin')

@section('title', 'Edit Manajemen Pengguna')
      
@section('content')
<div class="container">
    <div class="modal fade show" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('user-manage/update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <img src="/img/user-manage/editUser.png" alt="editUser" style="margin-right: 10px;">
                        <h5 class="modal-title" id="editUserModalLabel"><b>Edit Pengguna</b></h5>
                        <button type="button" class="btn-close" onclick="window.location='#'" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id" class="form-label">ID Pengguna :</label>
                                <input type="text" name="id" id="id" class="form-control" value="{{ $user->id }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email :</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required autofocus autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Nama Depan :</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname', $user->firstname) }}" required autofocus autocomplete="firstname">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Nama Belakang :</label>
                                <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname', $user->lastname) }}" required autofocus autocomplete="lastname">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nama Pengguna :</label>
                                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required autofocus autocomplete="username">
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">No. Telpon :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required autofocus autocomplete="phone_number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Kata Sandi :</label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi :</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi_id_divisi" class="form-label">Pilih Divisi</label>
                                <select name="divisi_id_divisi" id="divisi_id_divisi" class="form-control">
                                    @foreach($divisi as $d)
                                        <option value="{{ $d->id_divisi }}" {{ old('divisi_id_divisi', $user->divisi_id_divisi) == $d->id_divisi ? 'selected' : '' }}>
                                            {{ $d->nm_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position_id_position" class="form-label">Pilih Posisi</label>
                                <select name="position_id_position" id="position_id_position" class="form-control">
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id_position }}" {{ old('position_id_position', $user->position_id_position) == $position->id_position ? 'selected' : '' }}>
                                            {{ $position->nm_position }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="izin">
                                <label for="role_id_role" class="form-izin">Izin Akses</label>
                                @foreach ($roles as $role)
                                    <label for="role_{{ $role->id_role }}">{{ $role->nm_role }}</label>
                                    <input type="radio" name="role_id_role" value="{{ $role->id_role }}" id="role_{{ $role->id_role }}" {{ old('role_id_role', $user->role_id_role) == $role->id_role ? 'checked' : '' }} required>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" onclick="window.location='#'">Batal</button>
                        <button type="submit" class="btn btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection