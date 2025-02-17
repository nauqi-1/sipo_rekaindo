@extends('layouts.app')

@section('title', 'Superadmin')

@section('content')
<div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Beranda</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>{{ Auth::user()->username }}</strong> di <span class="system-name">Sistem Persuratan!</span> Anda login sebagai <span class="role-badge">Super Admin</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Tinjauan</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <hr>
                    <form action="">
                        <p>
                            <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                            <span><span>4</span> Memo</span>
                        </p>
                    </form>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <hr>
                    <form action="">
                        <p>
                            <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                            <span><span>7</span> Risalah Rapat</span>
                        </p>
                    </form>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <hr>
                    <form action="">
                        <p>
                            <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                            <span><span>7</span>  Undangan Rapat</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Things to Do Section -->
        <div class="things-to-do-container">
            <h3>Aktivitas</h3>
            <div class="things-to-do-list">
                <form action="">
                    <div class="things-to-do-item">
                        <div class="icon">
                            <button>
                                <img src="/img/dashboard/tinjau-memo.png" alt="Memo Icon" style="width: 22px; height: 20px;">
                            </button>
                        </div>
                        <div class="content">
                            <form action="">
                                <h4>Meninjau Memo</h4>
                                <p>Tinjau memo untuk kelangkah selanjutnya</p>
                            </form>
                        </div>
                        <div class="date">Hari ini</div>
                    </div>
                </form>
                
                <form action="">
                    <div class="things-to-do-item">
                        <div class="icon">
                            <button>
                                <img src="/img/dashboard/tambah-user.png" alt="User Icon" style="width: 22px; height: 20px;">
                            </button>
                        </div>
                        <div class="content">
                            <form action="">
                                <h4>Tambah User</h4>
                                <p>Tambah user baru untuk kelangkah selanjutnya</p>
                            </form>                        
                        </div>
                        <div class="date">Hari ini</div>
                    </div>
                </form>
                
                <form action="">
                    <div class="things-to-do-item">
                        <div class="icon">
                            <button>
                                <img src="/img/dashboard/tinjau-permintaan.png" alt="Surat Icon" style="width: 22px; height: 20px;">
                            </button>
                        </div>
                        <div class="content">
                            <form action="">
                                <h4>Meninjau Permintaan Surat</h4>
                                <p>Tinjau permintaan surat untuk kelangkah selanjutnya</p>
                            </form>
                        </div>
                        <div class="date">Hari ini</div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
@endsection
