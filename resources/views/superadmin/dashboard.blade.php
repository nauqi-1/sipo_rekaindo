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
                    <p>
                        <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                        <a href="#"><span>4</span> Memo</a>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <p>
                        <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                        <a href="#"><span>7</span> Risalah Rapat</a>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <p>
                        <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                        <a href="#"><span>7</span> Undangan Rapat</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Things to Do Section -->
        <div class="things-to-do-container">
            <h3>Aktivitas</h3>
            <div class="things-to-do-list">
                <div class="things-to-do-item">
                    <div class="icon">
                        <button>
                            <img src="/img/dashboard/tinjau-memo.png" alt="Memo Icon" style="width: 22px; height: 20px;">
                        </button>
                    </div>
                    <div class="content">
                        <h4><a href="#">Meninjau Memo</a></h4>
                        <p>Tinjau memo untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>

                <div class="things-to-do-item">
                    <div class="icon">
                        <button>
                            <img src="/img/dashboard/tambah-user.png" alt="User Icon" style="width: 22px; height: 20px;">
                        </button>
                    </div>
                    <div class="content">
                        <h4><a href="#">Tambah User Baru</a></h4>
                        <p>Tinjau untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>

                <div class="things-to-do-item">
                    <div class="icon">
                        <button>
                            <img src="/img/dashboard/tinjau-permintaan.png" alt="Surat Icon" style="width: 22px; height: 20px;">
                        </button>
                    </div>
                    <div class="content">
                        <h4><a href="#">Meninjau Permintaan Surat</a></h4>
                        <p>Tinjau permintaan surat untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>
            </div>
        </div>
    </div>
@endsection
