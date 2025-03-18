@extends('layouts.superadmin')

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
                    <a href="{{route ('memo.superadmin')}}" class="tampil">View All</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                        <span class="jumlah">{{ $Memo }}</span>
                        <span class="text">Memo</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <a href="{{ route ('risalah.superadmin') }}" class="tampil">View All</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                        <span class="jumlah">{{ $Risalah }}</span>
                        <span class="text">Risalah Rapat</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <a href="{{ route ('undangan.superadmin') }}" class="tampil">View All</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                        <span class="jumlah">{{ $Undangan }}</span>
                        <span class="text">Undangan Rapat</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Things to Do Section -->
        <div class="things-to-do-container">
            <h3>Aktivitas</h3>
            <div class="things-to-do-list">
                <a href="{{ route('memo.superadmin') }}" class="things-to-do-item">
                    <div class="icon">
                        <button>
                            <img src="/img/dashboard/tinjau-memo.png" alt="Memo Icon" style="width: 22px; height: 20px;">
                        </button>
                    </div>
                    <div class="content">
                        <h4>Histori Memo</h4>
                        <p>Riwayat memo untuk kelangkah selanjutnya</p>
                    </div>
                </a>
                <a href="{{ route('user.manage') }}" class="things-to-do-item-link">
                    <div class="things-to-do-item">
                        <div class="icon">
                            <button>
                                <img src="/img/dashboard/tambah-user.png" alt="User Icon" style="width: 22px; height: 20px;">
                            </button>
                        </div>
                        <div class="content">
                            <h4>Tambah User Baru</h4>
                            <p>Tinjau untuk kelangkah selanjutnya</p>
                        </div>
                    </div>
                </a>
                <a href="#" class="things-to-do-item-link">
                    <div class="things-to-do-item">
                        <div class="icon">
                            <button>
                                <img src="/img/dashboard/tinjau-permintaan.png" alt="Surat Icon" style="width: 22px; height: 20px;">
                            </button>
                        </div>
                        <div class="content">
                            <h4>Histori Permintaan Surat</h4>
                            <p>Riwayat permintaan surat untuk kelangkah selanjutnya</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
