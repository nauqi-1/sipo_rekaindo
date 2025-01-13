<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/user-manage.css') }}">
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
        <div class="breadcrumb">
            <a href="#">Home</a> / <a href="#">Setting</a> / <a href="#">User Management</a>
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
                        <tr>
                            <td>
                                <div class="user-info">
                                    <img src="/img/user-manage/me1.jpg" alt="User Image" class="rounded-circle">
                                    <div class="text-info">
                                        <span>Fidela</span>
                                        <br><small>fidel@gmail.com</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">Admin</span></td>
                            <td>Keuangan</td>
                            <td>Staff Keuangan</td>
                            <td>081-222-123</td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <img src="/img/user-manage/Edit.png" alt="edit">
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <img src="/img/user-manage/Trash.png" alt="delete">
                                </button>
                            </td>
                        </tr>
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
                <form method="POST" action="{{ route('user.add') }}">
                    @csrf
                    <div class="modal-header">
                        <img src="/img/user-manage/addUser.png" alt="addUser" style="margin-right: 10px;">
                        <h5 class="modal-title" id="addUserModalLabel"><b>Add User</b></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">User ID :</label>
                                <input type="text" name="user_id" id="user_id" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email_id" class="form-label">Email ID :</label>
                                <input type="text" name="email_id" id="email_id" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name :</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name :</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username :</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password :</label>
                                <input type="text" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password :</label>
                                <input type="text" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi" class="form-label">Select Divisi</label>
                                <select name="divisi" id="divisi" class="form-control">
                                    <option value="Divisi A">HR & GA</option>
                                    <option value="Divisi B">Pemasaran</option>
                                    <option value="Divisi C">Keuangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Select Position</label>
                                <select name="position" id="position" class="form-control">
                                    <option value="Manager">Super Admin</option>
                                    <option value="Supervisor">Supervisor/Manager</option>
                                    <option value="Admin">Admin Divisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Module Permissions</label>
                            <div>
                                <input type="radio" name="role" value="super_admin" id="role_super_admin" required>
                                <label for="role_super_admin">Super Admin</label>
                                <input type="radio" name="role" value="admin" id="role_admin" required>
                                <label for="role_admin">Supervisor</label>
                                <input type="radio" name="role" value="supervisor" id="role_supervisor" required>
                                <label for="role_supervisor">Admin Divisi</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Add User</button> -->
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#successModal">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay Success -->
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
                <form method="POST" action="{{ route('user.add') }}">
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
                                <label for="email_id" class="form-label">Email ID :</label>
                                <input type="text" name="email_id" id="email_id" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name :</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name :</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username :</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number :</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password :</label>
                                <input type="text" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password :</label>
                                <input type="text" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="divisi" class="form-label">Select Divisi</label>
                                <select name="divisi" id="divisi" class="form-control">
                                    <option value="Divisi A">HR & GA</option>
                                    <option value="Divisi B">Pemasaran</option>
                                    <option value="Divisi C">Keuangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Select Position</label>
                                <select name="position" id="position" class="form-control">
                                    <option value="Manager">Super Admin</option>
                                    <option value="Supervisor">Supervisor/Manager</option>
                                    <option value="Admin">Admin Divisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Module Permissions</label>
                            <div>
                                <input type="radio" name="role" value="super_admin" id="role_super_admin" required>
                                <label for="role_super_admin">Super Admin</label>
                                <input type="radio" name="role" value="admin" id="role_admin" required>
                                <label for="role_admin">Supervisor</label>
                                <input type="radio" name="role" value="supervisor" id="role_supervisor" required>
                                <label for="role_supervisor">Admin Divisi</label>
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
