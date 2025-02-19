@extends('layouts.superadmin')

@section('title', 'User Management')
      
@section('content')
<div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Manajemen Pengguna</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a> / <a href="#">Pengaturan</a> / <a href="#" style="color: #565656;">Manajemen Pengguna</a>
                </div>
            </div>
        </div>

        <!-- Wrapper untuk elemen di luar card -->
        <div class="user-manage">
        <div class="header-tools">
            <h2 class="title">Pengguna</h2>
            <div class="search-filter">
                <div class="d-flex gap-2">
                    <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                        <img src="/img/user-manage/search.png" alt="search" style="width: 20px; height: 20px;">
                        <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari berdasarkan nama ..." style="outline: none; box-shadow: none;">
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-dropdown dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Filter</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.manage', ['sort' => 'asc']) }}" style="justify-content: center; text-align: center;">
                                Urutkan abjad A-Z
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('user.manage', ['sort' => 'desc']) }}" style="justify-content: center; text-align: center;">
                                Urutkan abjad Z-A
                            </a>
                        </li>
                    </ul>
                </div>

                
                <!-- Add User Button to Open Mod    al -->
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Tambah Pengguna</button>
            </div>
        </div>
        <!-- Card untuk tabel -->
        <!-- <div class="card"> -->
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Izin</th>
                            <th>Divisi</th>
                            <th>Posisi</th>
                            <th>No. Telp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <img src="\img\user-manage\user.png" alt="User Image" class="rounded-circle-light">
                                <div class="text-info">
                                    <span>{{ $user->firstname }} {{ $user->lastname }}</span>
                                    <!-- <br><small>{{ $user->email }}</small> -->
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-email">
                                <span>{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                             {{ $user->role->nm_role ?? 'No Role Assigned' }} <!-- Menampilkan nama role -->
                            </span>
                        </td>
                        <td>
                            {{ $user->divisi->nm_divisi ?? 'No Divisi Assigned' }} <!-- Menampilkan nama divisi -->
                        </td>
                        <td>
                            {{ $user->position->nm_position ?? 'No Position Assigned' }} <!-- Menampilkan nama posisi -->
                        </td>
                        <td>{{ $user->phone_number }}</td>
                            <td>
                            <form method="POST" action="{{ route('user-manage.edit', $user->id) }}" style="display: inline;">
                            @csrf
                            @method('GET') <!-- Use GET to navigate to the edit page -->
                            <button type="submit" class="btn btn-edit btn-sm">
                                <img src="/img/user-manage/Edit.png" alt="edit">
                            </button>
                            </form>
                            <form method="POST" action="{{ route('user-manage.destroy', $user->id) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                                <button type="submit" class="btn btn-delete btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <img src="/img/user-manage/Trash.png" alt="delete">
                                </button>
                            </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            </div>
        </div>
    </div>

    <!-- Modal Add User (Overlay) -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('user-manage/add') }}">
                    @csrf
                    <div class="modal-header">
                        <img src="/img/user-manage/addUser.png" alt="addUser" style="margin-right: 10px;">
                        <h5 class="modal-title" id="addUserModalLabel"><b>Tambah Pengguna</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id" class="form-label">ID Pengguna :</label>
                                <input type="text" name="id" id="id" class="form-control"  required autocomplete="id">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email :</label>
                                <input type="text" name="email" id="email" class="form-control"  required autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Nama Depan :</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" required autocomplete="firstname">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Nama Akhir :</label>
                                <input type="text" name="lastname" id="lastname" class="form-control" required autocomplete="lastname">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nama Pengguna :</label>
                                <input type="text" name="username" id="username" class="form-control" required autocomplete="username">
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">No. Telpon :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required autocomplete="phone_number">
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password"  class="form-label">Kata Sandi :</label>
                                <input type="text" name="password" id="password" class="form-control" required autocomplete="new-password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi :</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi_id_divisi" class="form-label">Pilih Divisi</label>
                                <select name="divisi_id_divisi" id="divisi_id_divisi" class="form-control" required autofocus autocomplete="divisi_id_divisi">
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id_divisi }}">{{ $d->nm_divisi }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position_id_position" class="form-label">Pilih Posisi</label>
                                <select name="position_id_position" id="position_id_position" class="form-control" required autofocus autocomplete="position_id_position">
                                @foreach($positions as $position)
                                    <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="izin">
                                <label for="role_id_role" class="form-izin">Izin Akses</label>
                            @foreach ($roles as $role)
                                <label for="role_{{ $role->id_role }}">{{ $role->nm_role }}</label>
                                <input type="radio" name="role_id_role" value="{{ $role->id_role }}" id="role_{{ $role->id_role }}" required autofocus autocomplete="role_id_role">
                             @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-save" data-bs-toggle="modal" data-bs-target="successModal">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay Add User Success -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                    <!-- Success Message -->
                    <h5 class="modal-title" id="successModalLabel"><b>Success</b></h5>
                    <p class="mt-2">Berhasil Menambahkan User</p>
                    <!-- Back Button -->
                    <button type="button" class="btn btn-primary mt-2" data-bs-dismiss="modal">Kembali ke Halaman User</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Overlay Confirmation Edit Success -->
    <div class="modal fade" id="editSuccessModal" tabindex="-1" aria-labelledby="editSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="my-3" style="width: 80px;">
                    <!-- Success Message -->
                    <h5><b>Berhasil Mengubah User</b></h5>
                    <!-- Back Button -->
                    <button class="btn btn-primary mt-4 px-4 py-2" data-bs-dismiss="modal">Back</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay Delete Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Question Mark Icon -->
                    <img src="/img/user-manage/question_Vector.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
                    <!-- Delete Confirmation Text -->
                    <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus user?</h5>
                    <!-- Buttons -->
                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmDelete" data-bs-toggle="modal" data-bs-target="#deleteSuccessModal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay Confirmation Delete Success -->
    <div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Success Icon -->
                    <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="my-3" style="width: 80px;">
                    <!-- Success Message -->
                    <h5><b>Berhasil Menghapus User</b></h5>
                    <!-- Back Button -->
                    <button class="btn btn-primary mt-4 px-4 py-2" data-bs-dismiss="modal">Back</button>
                </div>
            </div>
        </div>
    </div>
@endsection