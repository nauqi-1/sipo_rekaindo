@extends('layouts.superadmin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Manajemen Pengguna</h1>
    </div>
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a> / <a href="#">Pengaturan</a> / <a href="#" style="color: #565656;">Manajemen Pengguna</a>
            </div>
            <form method="GET" action="{{ route('user.manage') }}" class="search-filter d-flex gap-2">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'asc') }}">
                <input type="hidden" name="view" value="{{ $view }}">

                <label style="margin: 0; padding-bottom: 25px; padding-right: 12px; color: #565656;">
                    Show
                    <select name="per_page" onchange="this.form.submit()" style="color: #565656; padding: 2px 5px;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    entries
                </label>
            </form>

        </div>
    </div>

    <!-- Wrapper untuk elemen di luar card -->
    <div class="user-manage">
        <div class="header-tools">
            <h2 class="title">Pengguna</h2>
            <div class="search-filter">
                <div class="d-flex gap-2">
                    <form action="{{ route('user.manage') }}" method="GET"
                        class="d-flex align-items-center btn btn-search" style="gap: 5px;">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                        <input type="hidden" name="sort" value="{{ request('sort', 'asc') }}">
                        <input type="hidden" name="view" value="{{ $view }}">

                        <button type="submit" class="border-0 bg-transparent p-0">
                            <img src="/img/user-manage/search.png" alt="search"
                                style="width: 20px; height: 20px; cursor: pointer;">
                        </button>

                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-0 bg-transparent"
                            placeholder="Cari berdasarkan nama ..."
                            style="outline: none; box-shadow: none;">
                    </form>
                </div>

                <div class="dropdown m-3">
                    <button class="btn btn-dropdown dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Filter</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.manage', array_merge(request()->all(), ['sort' => 'asc'])) }}">
                                Urutkan abjad A-Z
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('user.manage', array_merge(request()->all(), ['sort' => 'desc'])) }}">
                                Urutkan abjad Z-A
                            </a>
                        </li>
                    </ul>
                </div>

                @if($view === 'deleted')
                <button class="btn btn-add"
                    onclick="window.location='{{ route('user.manage', array_merge(request()->all(), ['view' => 'active'])) }}'">
                    User Aktif
                </button>
                @else
                <button class="btn btn-danger"
                    onclick="window.location='{{ route('user.manage', array_merge(request()->all(), ['view' => 'deleted'])) }}'">
                    User Non-Aktif
                </button>
                @endif

                <!-- Add User Button to Open Mod    al -->
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Tambah Pengguna</button>
            </div>
        </div>
        <!-- Card untuk tabel -->
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
                        @if($view !== 'deleted')
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                @if($user->profile_image)
                                <img src="data:image/jpeg;base64,{{ $user->profile_image }}" alt="user-image" class="user-avtar" style=" width: 40px; height: 40px; object-fit: cover; border-radius: 50%;" />
                                @else
                                <img src="../assets/images/user/default1.png" alt="user-image" class="user-avtar" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;" />
                                @endif
                                <div class="text-info">
                                    <span>{{ $user->firstname }} {{ $user->lastname }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-email">
                                <span>{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($user->role->nm_role == 'superadmin')
                            <span class="badge role-superadmin">superadmin</span>
                            @elseif ($user->role->nm_role == 'direktur')
                            <span class="badge role-manager">direktur</span>
                            @elseif ($user->role->nm_role == 'admin')
                            <span class="badge role-admin">admin</span>
                            @else
                            <span class="badge role-manager">manager</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->divisi->nm_divisi ?? $user->department->name_department ?? $user->director->name_director ?? '-' }} <!-- Menampilkan nama divisi -->
                        </td>
                        <td>
                            {{ $user->position->nm_position ?? 'No Position Assigned' }} <!-- Menampilkan nama posisi -->
                        </td>
                        <td>{{ $user->phone_number }}</td>
                        @if($view !== 'deleted')
                        <td>
                            <button type="button" class="btn btn-edit"
                                data-bs-toggle="modal"
                                data-bs-target="#editUserModal"
                                data-id="{{ $user->id }}"
                                data-firstname="{{ $user->firstname }}"
                                data-lastname="{{ $user->lastname }}"
                                data-username="{{ $user->username }}"
                                data-email="{{ $user->email }}"
                                data-phone="{{ $user->phone_number }}"
                                data-role="{{ $user->role_id_role }}"
                                data-position="{{ $user->position_id_position }}"
                                data-parent="{{ $user->parent_id }}">
                                <img src="/img/user-manage/Edit1.png" alt="edit">
                            </button>

                            <button type="button" class="btn btn-delete"
                                data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                data-user-id="{{ $user->id }}" data-route="{{ route('user-manage.destroy', $user->id) }}">
                                <img src="/img/user-manage/Trash1.png" alt="delete">
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links('pagination::bootstrap-5') }}
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
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ID Pengguna</label>
                            <input type="text" name="id" id="id" class="form-control" disabled autocomplete="id">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email :<span style="color : red;"> *</span></label>
                            <input type="email" name="email" id="email" class="form-control" required autocomplete="email">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">Nama Depan :<span style="color : red;"> *</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required autocomplete="firstname">
                            <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Nama Akhir :<span style="color : red;"> *</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required autocomplete="lastname">
                            <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nama Pengguna :<span style="color : red;"> *</span></label>
                            <input type="text" name="username" id="username" class="form-control" required autocomplete="username">
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">No. Telpon :<span style="color : red;"> *</span></label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" required autocomplete="phone_number">
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Kata Sandi :<span style="color : red;"> *</span></label>
                            <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="Masukkan Min. 8 karakter">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi :<span style="color : red;"> *</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            @php
                            function renderOrgRecursive($node) {
                            if(isset($node->name_director)) {
                            $label = "Direktur: ".htmlspecialchars($node->name_director);
                            $margin = 0; $border = 'primary'; $bg = 'primary';
                            $type = 'director'; $id = $node->id_director; $name = $node->name_director;
                            } elseif(isset($node->nm_divisi)) {
                            $label = "Divisi: ".htmlspecialchars($node->nm_divisi);
                            $margin = 20; $border = 'secondary'; $bg = 'secondary';
                            $type = 'divisi'; $id = $node->id_divisi; $name = $node->nm_divisi;
                            } elseif(isset($node->name_department)) {
                            $label = "Departemen: ".htmlspecialchars($node->name_department);
                            $margin = 40; $border = 'info'; $bg = 'info';
                            $type = 'department'; $id = $node->id_department; $name = $node->name_department;
                            } elseif(isset($node->name_section)) {
                            $label = "Bagian: ".htmlspecialchars($node->name_section);
                            $margin = 60; $border = 'success'; $bg = 'success';
                            $type = 'section'; $id = $node->id_section; $name = $node->name_section;
                            } elseif(isset($node->name_unit)) {
                            $label = "Unit: ".htmlspecialchars($node->name_unit);
                            $margin = 80; $border = 'warning'; $bg = 'warning';
                            $type = 'unit'; $id = $node->id_unit; $name = $node->name_unit;
                            } else {
                            return;
                            }

                            $idUnique = uniqid('accordion_');
                            $deleteUrl = route('organization.delete', ['type' => $type, 'id' => $id]);

                            $hasChildren =
                            (!empty($node->subDirectors)) ||
                            (!empty($node->divisi)) ||
                            (!empty($node->department)) ||
                            (!empty($node->section)) ||
                            (!empty($node->unit));
                            }

                            @endphp

                            @if($mainDirector)
                            @php renderOrgRecursive($mainDirector); @endphp
                            @endif
                            <label for="divisi_id_divisi" class="form-label">Pilih Posisi<span style="color : red;"> *</span></label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- Pilih Posisi --</option>
                                @php
                                function renderOrgOptions($node, $level = 0) {
                                $indent = str_repeat('&nbsp;', $level * 4);
                                if(isset($node->name_director))
                                echo "<option value='{$node->id_director}' data-type='director'>{$indent}Direktur: {$node->name_director}</option>";
                                elseif(isset($node->nm_divisi))
                                echo "<option value='{$node->id_divisi}' data-type='divisi'>{$indent}--> Divisi: {$node->nm_divisi}</option>";
                                elseif(isset($node->name_department))
                                echo "<option value='{$node->id_department}' data-type='department'>{$indent}-----> Departemen: {$node->name_department}</option>";
                                elseif(isset($node->name_section))
                                echo "<option value='{$node->id_section}' data-type='section'>{$indent}--------> Bagian: {$node->name_section}</option>";
                                elseif(isset($node->name_unit))
                                echo "<option value='{$node->id_unit}' data-type='unit'>{$indent}-----------> Unit: {$node->name_unit}</option>";

                                if(isset($node->subDirectors))
                                foreach ($node->subDirectors as $subDir)
                                renderOrgOptions($subDir, $level+1);
                                if(isset($node->divisi))
                                foreach ($node->divisi as $div)
                                renderOrgOptions($div, $level+1);
                                if(isset($node->department)) {
                                if(isset($node->name_director))
                                foreach ($node->department->whereNull('divisi_id_divisi') as $dept)
                                renderOrgOptions($dept, $level+1);
                                if(isset($node->nm_divisi))
                                foreach ($node->department as $dept)
                                renderOrgOptions($dept, $level+1);
                                }
                                if(isset($node->section))
                                foreach ($node->section as $sec)
                                renderOrgOptions($sec, $level+1);
                                if(isset($node->unit)) {
                                if(isset($node->name_department) && $node->unit->whereNull('section_id_section'))
                                foreach ($node->unit->whereNull('section_id_section') as $unit)
                                renderOrgOptions($unit, $level+1);
                                if(isset($node->name_section))
                                foreach ($node->unit as $unit)
                                renderOrgOptions($unit, $level+1);
                                }
                                }
                                if($mainDirector) renderOrgOptions($mainDirector);
                                @endphp
                            </select>
                            <input type="hidden" name="parent_type" id="parent_type">
                        </div>
                        <div class="col-md-6">
                            <label for="position_id_position" class="form-label">Pilih Jabatan<span style="color : red;"> *</span></label>
                            <select name="position_id_position" id="position_id_position" class="form-control" required autofocus autocomplete="position_id_position">
                                @foreach($positions as $position)
                                <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="izin">
                            <label for="role_id_role" class="form-izin">Izin Akses<span style="color : red;"> *</span></label>
                            @foreach ($roles as $role)
                            <label for="role_{{ $role->id_role }}">{{ $role->nm_role }}</label>
                            <input type="radio" name="role_id_role" value="{{ $role->id_role }}" id="role_{{ $role->id_role }}" required autofocus autocomplete="role_id_role">
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editUserForm">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <img src="/img/user-manage/addUser.png" alt="editUser" style="margin-right: 10px;">
                    <h5 class="modal-title"><b>Edit Pengguna</b></h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body sama persis dengan form Amanda -->
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ID Pengguna</label>
                            <input type="text" id="edit_id_display" class="form-control" disabled>
                            <input type="hidden" name="id" id="edit_id">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email :<span style="color : red;"> *</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_firstname" class="form-label">Nama Depan :<span style="color : red;"> *</span></label>
                            <input type="text" name="firstname" id="edit_firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lastname" class="form-label">Nama Akhir :<span style="color : red;"> *</span></label>
                            <input type="text" name="lastname" id="edit_lastname" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Nama Pengguna :<span style="color : red;"> *</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">No. Telpon :<span style="color : red;"> *</span></label>
                            <input type="text" name="phone_number" id="edit_phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">Kata Sandi :</label>
                            <input type="password" name="password" id="edit_password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password_confirmation" class="form-label">Konfirmasi Kata Sandi :</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_parent_id" class="form-label">Pilih Posisi<span style="color : red;"> *</span></label>
                            <select id="edit_parent_id" class="parent-select form-control" name="parent_id" data-target="edit" required>
                                <option value="">-- Pilih Posisi --</option>
                                @php if($mainDirector) renderOrgOptions($mainDirector); @endphp
                            </select>
                            <input type="hidden" name="parent_type" id="edit_parent_type">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_position" class="form-label">Pilih Jabatan<span style="color : red;"> *</span></label>
                            <select class="position-select form-control" id="edit_position" name="position" data-target="edit" required disabled>
                                @foreach($positions as $position)
                                <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="izin">
                            <label class="form-izin">Izin Akses<span style="color : red;"> *</span></label>
                            @foreach ($roles as $role)
                            <label for="edit_role_{{ $role->id_role }}">{{ $role->nm_role }}</label>
                            <input type="radio" name="role_id_role" value="{{ $role->id_role }}" id="edit_role_{{ $role->id_role }}">
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay Add User Success -->
<div class="modal fade" id="successAddUserModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Menambahkan Pengguna</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay Add User Success -->
<div class="modal fade" id="successEditUserModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Success Icon -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="mb-3" style="width: 80px; height: 80px;">
                <!-- Success Message -->
                <h5 class="modal-title" id="successModalLabel"><b>Sukses</b></h5>
                <p class="mt-2">Berhasil Mengubah Pengguna</p>
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

<!-- Overlay Delete User -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/img/user-manage/question_Vector.png" alt="Question Mark Icon" class="mb-3" style="width: 80px; height: 80px;">
            <h5 class="modal-title mb-4" id="deleteModalLabel">Hapus user?</h5>
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Oke</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay Confirmation Delete Success -->
<div class="modal fade" id="deleteUserSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <!-- Close Button -->
                <img src="/img/user-manage/success icon component.png" alt="Success Icon" class="my-3" style="width: 80px;">
                <!-- Success Message -->
                <h5><b>Berhasil Menghapus User</b></h5>
            </div>
        </div>
    </div>
</div>

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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let editUserModal = document.getElementById("editUserModal");

        // Saat modal Edit ditampilkan
        editUserModal.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget;

            // Ambil data dari tombol
            let id = button.getAttribute("data-id");
            let firstname = button.getAttribute("data-firstname");
            let lastname = button.getAttribute("data-lastname");
            let username = button.getAttribute("data-username");
            let email = button.getAttribute("data-email");
            let phone = button.getAttribute("data-phone");
            let role = button.getAttribute("data-role");
            let position = button.getAttribute("data-position");
            let parent = button.getAttribute("data-parent");

            // Set form action
            let form = document.getElementById("editUserForm");
            form.action = "/user-manage/update/" + id;

            // Isi input form
            document.getElementById("edit_id_display").value = id;
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_firstname").value = firstname || "";
            document.getElementById("edit_lastname").value = lastname || "";
            document.getElementById("edit_username").value = username || "";
            document.getElementById("edit_email").value = email || "";
            document.getElementById("edit_phone").value = phone || "";
            document.getElementById("edit_position").value = position || "";

            // SET parent & parent_type dari <option data-type="...">
            const editParentSelect = document.getElementById("edit_parent_id");
            const parentTypeInput = document.getElementById("edit_parent_type");

            if (editParentSelect) {
                // set value parent (id)
                if (parent) editParentSelect.value = parent;

                // ambil data-type dari option yang terpilih
                const selected = editParentSelect.options[editParentSelect.selectedIndex];
                const parentType = selected ? (selected.getAttribute("data-type") || selected.dataset.type || "") : "";

                parentTypeInput.value = parentType;

                // trigger change supaya mapping posisi (jika ada) ikut sinkron
                editParentSelect.dispatchEvent(new Event("change"));
            }

            // Set role
            if (role) {
                let roleInput = document.getElementById("edit_role_" + role);
                if (roleInput) roleInput.checked = true;
            }
        });
        const editParentSelect = document.getElementById("edit_parent_id");
        if (editParentSelect) {
            editParentSelect.addEventListener("change", function() {
                const selected = this.options[this.selectedIndex];
                const parentType = selected ? (selected.getAttribute("data-type") || selected.dataset.type || "") : "";
                document.getElementById("edit_parent_type").value = parentType;
            });
        }
    });
    // Update parent_type saat ganti parent_id manual di dropdown
    document.getElementById("edit_parent_id").addEventListener("change", function() {
        let selected = this.options[this.selectedIndex];
        let parentType = selected.dataset.type || null;
        document.getElementById("edit_parent_type").value = parentType;

        console.log("parentType set to:", parentType);
    });





    document.addEventListener('DOMContentLoaded', function() {
        const parentSelect = document.getElementById('parent_id');
        const parentTypeInput = document.getElementById('parent_type');

        parentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.getAttribute('data-type') || '';
            parentTypeInput.value = type;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const parentSelect = document.getElementById('parent_id');
        const positionSelect = document.getElementById('position_id_position');
        const allPositions = @json($positions);

        // Mapping tipe induk -> array id_position
        const positionMap = {
            'director': [1], // Direktur
            'divisi': [2, 3, 4], // GM, SM, PJ SM
            'department': [3, 4, 5, 6, 7, 8], // SM, PJ SM, Manajer, SPV, PJ M, PJ SPV
            'section': [5, 6, 7, 8, 9], // Manajer, SPV, PJ M, PJ SPV, Staff
            'unit': [9] // Staff
        };

        function updatePositions() {
            const selectedOption = parentSelect.options[parentSelect.selectedIndex];
            const type = selectedOption ? selectedOption.getAttribute('data-type') : null;

            // Kosongkan posisi
            positionSelect.innerHTML = '';

            if (type && positionMap[type]) {
                // Enable select
                positionSelect.disabled = false;

                // Tampilkan hanya posisi yang sesuai mapping
                let filtered = allPositions.filter(pos => positionMap[type].includes(pos.id_position));
                filtered.forEach(pos => {
                    let opt = document.createElement('option');
                    opt.value = pos.id_position;
                    opt.textContent = pos.nm_position;
                    positionSelect.appendChild(opt);
                });
            } else {
                // Kalau belum pilih parent, disable
                positionSelect.disabled = true;
                // Tampilkan placeholder
                let opt = document.createElement('option');
                opt.textContent = '-- Pilih posisi setelah pilih induk --';
                positionSelect.appendChild(opt);
            }
        }

        // Saat load halaman langsung disable dulu
        updatePositions();

        // Saat induk diubah
        parentSelect.addEventListener('change', updatePositions);
    });


    document.addEventListener('DOMContentLoaded', function() {
        const parentSelect = document.getElementById('parent_id');
        const parentTypeInput = document.getElementById('parent_type');

        parentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.getAttribute('data-type') || '';
            parentTypeInput.value = type;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const allPositions = @json($positions);

        const positionMap = {
            'director': [1],
            'divisi': [2, 3, 4],
            'department': [3, 4, 5, 6, 7, 8],
            'section': [5, 6, 7, 8, 9],
            'unit': [9]
        };

        // Ambil semua parent select
        document.querySelectorAll('.parent-select').forEach(parentSelect => {
            const target = parentSelect.getAttribute('data-target');
            const positionSelect = document.querySelector(
                `.position-select[data-target="${target}"]`
            );

            function updatePositions() {
                const selectedOption = parentSelect.options[parentSelect.selectedIndex];
                const type = selectedOption ? selectedOption.getAttribute('data-type') : null;

                positionSelect.innerHTML = '';

                if (type && positionMap[type]) {
                    positionSelect.disabled = false;
                    let filtered = allPositions.filter(pos => positionMap[type].includes(pos.id_position));
                    filtered.forEach(pos => {
                        let opt = document.createElement('option');
                        opt.value = pos.id_position;
                        opt.textContent = pos.nm_position;
                        positionSelect.appendChild(opt);
                    });
                } else {
                    positionSelect.disabled = true;
                    let opt = document.createElement('option');
                    opt.textContent = '-- Pilih posisi setelah pilih induk --';
                    positionSelect.appendChild(opt);
                }
            }

            // Jalankan saat pertama kali buka (misal modal edit sudah ada value)
            updatePositions();

            // Jalankan setiap kali parent berubah
            parentSelect.addEventListener('change', updatePositions);
        });
    });


    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function() {
        let deleteUserModal = document.getElementById("deleteUserModal");
        let deleteUserForm = document.getElementById("deleteUserForm");
        let deleteUserSuccessModal = new bootstrap.Modal(document.getElementById("deleteUserSuccessModal"));

        // Event ketika modal delete user ditampilkan
        deleteUserModal.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget;
            let route = button.getAttribute("data-route");
            deleteUserForm.setAttribute("action", route);
        });

        // Event ketika form delete dikirim
        deleteUserForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman form default

            let formAction = deleteUserForm.getAttribute("action");

            fetch(formAction, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    _method: "DELETE"
                })
            }).then(response => {
                if (response.ok) {
                    let modalInstance = bootstrap.Modal.getInstance(deleteUserModal);
                    modalInstance.hide();

                    setTimeout(() => {
                        deleteUserSuccessModal.show();
                        setTimeout(() => {
                            location.reload(); // Refresh halaman setelah 2 detik
                        }, 1500);
                    }, 500);
                }
            }).catch(error => console.error("Error:", error));
        });
    });

    // Event listener untuk modal sukses tambah user
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
        let successAddUserModal = new bootstrap.Modal(document.getElementById("successAddUserModal"));
        successAddUserModal.show();
        setTimeout(() => {
            successAddUserModal.hide();
        }, 1500);
        @elseif(session('error')) {
            var errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
            document.getElementById("errorPasswordMessage").innerText = "{{ session('error') }}";
            errorModal.show();
        }
        @endif
    });

    // Event listener untuk modal sukses tambah user
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
        let successEditUserModal = new bootstrap.Modal(document.getElementById("successEditUserModal"));
        successEditUserModal.show();
        setTimeout(() => {
            successEditUserModal.hide();
        }, 1500);
        @endif
    });
</script>
@endsection