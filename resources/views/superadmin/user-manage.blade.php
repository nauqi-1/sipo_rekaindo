<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/user-manage.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>User Management</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a> / <a href="#">Setting</a> / <a href="#">User Management</a>
                </div>
            </div>
        </div>

        <!-- Wrapper untuk elemen di luar card -->
        <div class="header-tools">
            <h2 class="title">User</h2>
            <div class="search-filter">
                <button class="input-group-text" id="search-icon">
                    <i class="bi bi-search"></i>
                </button>
                <input type="text" class="form-control" placeholder="Search by name...">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#">Aplhabetically A-Z</a></li>
                        <li><a class="dropdown-item" href="#">Aplhabetically Z-A</a></li>
                    </ul>
                </div>
                <!-- Add User Button to Open Mod    al -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add User</button>
            </div>
        </div>

        <!-- Card untuk tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th>Divisi</th>
                            <th>Position</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <img src="/img/user-manage/me1.jpg" alt="User Image" class="rounded-circle">
                                <div class="text-info">
                                    <span>{{ $user->firstname }} {{ $user->lastname }}</span>
                                    <br><small>{{ $user->email }}</small>
                                </div>
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
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="{{ $user->id }}">
                                    <img src="/img/user-manage/Edit.png" alt="edit">
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $user->id }}">
                                    <img src="/img/user-manage/Trash.png" alt="delete">
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    <p>Showing data 1 to 8 of 256K entries</p>
                    <ul>
                        <li class="active">1</li>
                        <li>2</li>
                        <li>3</li>
                        <li>...</li>
                    </ul>
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
                        <h5 class="modal-title" id="addUserModalLabel"><b>Add User</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_user" class="form-label">User ID :</label>
                                <input type="text" name="id_user" id="id_user" class="form-control" required autocomplete="id_user">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email :</label>
                                <input type="text" name="email" id="email" class="form-control" required autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name :</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" required autocomplete="firstname">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name :</label>
                                <input type="text" name="lastname" id="lastname" class="form-control" required autocomplete="lastname">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username :</label>
                                <input type="text" name="username" id="username" class="form-control" required autocomplete="username">
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required autocomplete="phone_number">
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password"  class="form-label">Password :</label>
                                <input type="text" name="password" id="password" class="form-control" required autocomplete="new-password">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password :</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi_id_divisi" class="form-label">Select Divisi</label>
                                <select name="divisi_id_divisi" id="divisi_id_divisi" class="form-control" required autofocus autocomplete="divisi_id_divisi">
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nm_divisi }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position_id_position" class="form-label">Select Position</label>
                                <select name="position_id_position" id="position_id_position" class="form-control" required autofocus autocomplete="position_id_position">
                                @foreach($positions as $position)
                                    <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role_id_role" class="form-label">Module Permissions</label>
                            <div>
                            @foreach ($roles as $role)
                                <input type="radio" name="role_id_role" value="{{ $role->id }}" id="role_{{ $role->id }}" required autofocus autocomplete="role_id_role">
                                <label for="role_{{ $role->id }}">{{ $role->nm_role }}</label>
                             @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Add User</button> -->
                        <button type="submit" class="btn btn-primary" >Add User</button>
                        <!-- data-bs-toggle="modal" data-bs-target="#successModal" -->
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


    <!-- Modal Edit User (Overlay) -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('user-manage/add') }}">
                    @csrf
                    <div class="modal-header">
                        <img src="/img/user-manage/editUser.png" alt="addUser" style="margin-right: 10px;">
                        <h5 class="modal-title" id="addUserModalLabel"><b>Edit User</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">User ID :</label>
                                <input type="text" name="user_id" id="user_id" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email :</label>
                                <input type="text" name="email" id="email" class="form-control" required autofocus autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name :</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" required autofocus autocomplete="firstname">
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name :</label>
                                <input type="text" name="lastname" id="lastname" class="form-control" required autofocus autocomplete="lastname">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username :</label>
                                <input type="text" name="username" id="username" class="form-control" required autofocus autocomplete="username">
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required autofocus autocomplete="phone_number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password :</label>
                                <input type="text" name="password" id="password" class="form-control" required autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password :</label>
                                <input type="text" name="confirm_password" id="confirm_password" class="form-control" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi" class="form-label">Select Divisi</label>
                                <select name="divisi" id="divisi" class="form-control">
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nm_divisi }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Select Position</label>
                                <select name="position" id="position" class="form-control">
                                @foreach($positions as $position)
                                    <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Module Permissions</label>
                            <div>
                            @foreach ($roles as $role)
                                <input type="radio" name="role" value="{{ $role->id }}" id="role_{{ $role->id }}" required>
                                <label for="role_{{ $role->id }}">{{ $role->nm_role }}</label>
                             @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Add User</button> -->
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editSuccessModal">Save changes</button>
                    </div>
                </form>
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

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>