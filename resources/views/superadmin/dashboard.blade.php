<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadmin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Dashboard</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <p class="welcome-text">Selamat datang Mawar di <span style="color: #00B087;">SIPO Reka!</span>  Anda login sebagai 
                    <span class="role">Super Admin</span></p>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
            <h4><b>Overview</b></h4><br>
                <!-- Card Memo -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title">MEMO</h6>
                                <a href="#" class="btn btn-link p-0">View All</a>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <a href="#" class="icon-box">
                                    <img src="/img/dashboard/memo.png" alt="Memo" class="me-3">
                                </a>
                                <p class="card-text display-6 mb-0">4<span> Memo</span></p>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Card Risalah Rapat -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title">RISALAH RAPAT</h6>
                                <a href="#" class="btn btn-link p-0">View All</a>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <a href="#" class="icon-box">
                                    <img src="/img/dashboard/risalah.png" alt="Risalah Rapat" class="me-3">
                                </a>
                                <p class="card-text display-6 mb-0">7<span> Risalah Rapat</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card Undangan Rapat -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title">UNDANGAN RAPAT</h6>
                                <a href="#" class="btn btn-link p-0">View All</a>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <a href="#" class="icon-box">
                                    <img src="/img/dashboard/undangan.png" alt="Undangan Rapat" class="me-3">
                                </a>
                                <p class="card-text display-6 mb-0">15<span> Undangan Rapat</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Things to do -->
         <div class="card to-do">
            <div class="card-body">
            <h4><b>Things to do</b></h4>
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item d-flex align-items-center">
                        <div class="d-flex align-items-start">
                            <a href="#" class="icon-box">
                                <img src="/img/dashboard/tinjau-memo.png" alt="Memo" class="me-3">
                            </a>
                                <div>
                                <strong>Meninjau Memo</strong> <br>
                                <small class="text-muted">Tinjau memo untuk kelangkah selanjutnya</small>
                            </div>
                        </div>
                        <span class="badge bg-primary ms-auto">Hari ini</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <div class="d-flex align-items-start">
                            <a href="#" class="icon-box">
                                <img src="/img/dashboard/tambah-user.png" alt="Memo" class="me-3">
                            </a>
                            <div>
                                <strong>Tambah User Baru</strong> <br>
                                <small class="text-muted">Tinjau untuk kelangkah selanjutnya</small>
                            </div>
                        </div>
                        <span class="badge bg-primary ms-auto">Hari ini</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <div class="d-flex align-items-start">    
                            <a href="#" class="icon-box">
                                <img src="/img/dashboard/tinjau-permintaan.png" alt="Memo" class="me-3">
                            </a>
                            <div>
                                <strong>Meninjau Permintaan Surat</strong> <br>
                                <small class="text-muted">Tinjau permintaan surat untuk kelangkah selanjutnya</small>
                            </div>
                        </div>
                        <span class="badge bg-primary ms-auto">Hari ini</span>
                    </li>
                </ul>
            </div>
            </div>
         </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
