<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Super Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/memo.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a>/<a href="#">Memo</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <select class="form-select" style="width: 150px;">
                    <option>Status</option>
                    <option>Approve</option>
                    <option>Reject</option>
                    <option>Pending</option>
                </select>
                <input type="text" class="form-control date-placeholder" placeholder="Data Dibuat" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 200px;">
                <img src="/img/memo-superadmin/panah.png" alt="panah" class="icon-panah">
                <input type="text" class="form-control date-placeholder" placeholder="Data Keluar" onfocus="(this.type='date')" onblur="(this.type='text')" style="width: 200px;">                
            </div>
            <div class="d-flex gap-2">
                <div class="btn btn-primary d-flex align-items-center" style="gap: 5px;">
                    <img src="/img/memo-superadmin/search.png" alt="search" style="width: 20px; height: 20px;">
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Search" style="outline: none; box-shadow: none;">
                </div>
            </div>
            <button class="btn btn-success"><a href="{{route('superadmin.add-memo')}}" style="text-decoration: none; color: #878790;">+ Add Memo </a></button>
        </div>

        <!-- Table -->
        <table class="table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Data Dibuat</th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Data Disahkan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 3; $i++)
                <tr>
                    <td class="nomor">{{ $i }}</td>
                    <td class="nama-dokumen {{ $i % 3 == 0 ? 'text-danger' : ($i % 2 == 0 ? 'text-warning' : 'text-success') }}">Memo Monitoring Risiko</td>
                    <td>21-10-2024</td>
                    <td>1596</td>
                    <td>837.06/REKA/GEN/VII/2024</td>
                    <td>22-10-2024</td>
                    <td>
                        @if ($i % 3 == 0)
                            <span class="badge bg-danger">Reject</span>
                        @elseif ($i % 2 == 0)
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-success">Approve</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm"><img src="/img/memo-superadmin/share.png" alt="share"></button>
                        <button class="btn btn-sm"><img src="/img/memo-superadmin/Delete.png" alt="Delete"></button>
                        <button class="btn btn-sm"><img src="/img/memo-superadmin/edit.png" alt="edit"></button>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Pagination -->
        <!-- <div class="d-flex justify-content-end">
            <nav>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">...</a></li>
                </ul>
            </nav>
        </div> -->
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>