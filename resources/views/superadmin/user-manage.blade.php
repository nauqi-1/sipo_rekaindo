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
                    <form action="{{ route('user.manage') }}" method="GET" class="d-flex align-items-center btn btn-search" style="gap: 5px;">
                        <button type="submit" class="border-0 bg-transparent p-0" style="outline: none; box-shadow: none;">
                            <img src="/img/user-manage/search.png" alt="search" style="width: 20px; height: 20px; cursor: pointer;">
                        </button>                            
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-transparent" placeholder="Cari berdasarkan nama ..." style="outline: none; box-shadow: none;">
                    </form>
                </div>

                <div class="dropdown m-3">
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
                        @if($user->profile_image)
                            <img src="data:image/jpeg;base64,{{ $user->profile_image }}" alt="user-image" class="user-avtar" style=" width: 40px; height: 40px; object-fit: cover; border-radius: 50%;" />
                        @else
                            <img src="../assets/images/user/default1.png" alt="user-image" class="user-avtar" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;"/>
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
                        <button type="submit" class="btn btn-edit">
                            <img src="/img/user-manage/Edit1.png" alt="edit">
                        </button>
                        </form>
                        <button type="button" class="btn btn-delete" 
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                            data-user-id="{{ $user->id }}" data-route="{{ route('user-manage.destroy', $user->id) }}">
                            <img src="/img/user-manage/Trash1.png" alt="delete">
                        </button>
                        </td>
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
                            <label for="id" class="form-label">ID Pengguna :</label>
                            <input type="text" name="id" id="id" class="form-control" disabled autocomplete="id">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email :<span style="color : red;"> *</span></label>
                            <input type="email" name="email" id="email" class="form-control"  required autocomplete="email">
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
                            <label for="password"  class="form-label">Kata Sandi :<span style="color : red;"> *</span></label>
                            <input type="text" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="Masukkan Min. 8 karakter">
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

    // Event Listener Overlay delete
    document.addEventListener("DOMContentLoaded", function () {
        let deleteUserModal = document.getElementById("deleteUserModal");
        let deleteUserForm = document.getElementById("deleteUserForm");
        let deleteUserSuccessModal = new bootstrap.Modal(document.getElementById("deleteUserSuccessModal"));

        // Event ketika modal delete user ditampilkan
        deleteUserModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            let route = button.getAttribute("data-route");
            deleteUserForm.setAttribute("action", route);
        });

        // Event ketika form delete dikirim
        deleteUserForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Mencegah pengiriman form default

            let formAction = deleteUserForm.getAttribute("action");

            fetch(formAction, {
                method: "POST", // Laravel menangani DELETE dengan _method
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
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
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            let successAddUserModal = new bootstrap.Modal(document.getElementById("successAddUserModal"));
            successAddUserModal.show();
            setTimeout(() => {
                successAddUserModal.hide();
            }, 1500);
        @endif
    });

    // Event listener untuk modal sukses tambah user
    document.addEventListener("DOMContentLoaded", function () {
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