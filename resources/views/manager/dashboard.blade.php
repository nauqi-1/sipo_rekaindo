@extends('layouts.manager')

@section('title', 'Manager')

@section('content')
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Beranda</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>{{ Auth::user()->username }}</strong> di <span class="system-name">Sistem Persuratan!</span> Anda login sebagai <span class="role-badge">Manager</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Tinjauan</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <a href="{{route ('memo.diterima')}}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahMemo }}</span>
                        <span class="text">Memo</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <a href="{{ route ('risalah.manager') }}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahRisalah }}</span>
                        <span class="text">Risalah Rapat</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <a href="{{ route ('undangan.manager') }}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahUndangan }}</span>
                        <span class="text">Undangan Rapat</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="things-to-do-container">
            <!-- History hari ini -->
            <h3>Hari Ini</h3>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon1">
                        <img src="/img/dashboard/memoy.png" alt="Memo Icon">
                    </div>
                </form>
                <div class="content">
                    <h4><a href="#">Memo Ekspor</a></h4>
                    <p>5:12 pm • <span>Diterima</span></p>
                </div>
            </div>
            <div class="things-to-do-list">   
                <form action="#">
                    <div class="icon2">
                        <img src="/img/dashboard/surat.png" alt="Undangan Icon">
                    </div>
                </form>
                <div class="content">
                    <h4><a href="#">Surat Undangan Kebijakan</a></h4>
                    <p>5:12 pm • <span>Diterima</span></p>
                </div>
            </div>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon3">
                        <img src="/img/dashboard/vectory.png" alt="Risalah Icon">
                    </div>
                </form>
                <div class="content">
                    <h4><a href="#">Risalah Rapat HUT</a></h4>
                    <p>5:12 pm • <span>Ditolak</span></p>
                </div>
            </div>
        </div>
            
        <!-- History kemarin -->
        <div class="things-to-do-container">
            <h3>Jumat, 28 Oktober</h3>
            <div class="things-to-do-list">
                <form action="#">
                    <div class="icon1">
                        <img src="/img/dashboard/memoy.png" alt="Memo Icon">
                    </div>
                </form>
                <div class="content">
                    <h4><a href="#">Memo Penyusunan</a></h4>
                    <p>5:12 pm • <span>Ditolak</span></p>
                </div>
            </div>
            <div class="things-to-do-list">   
                <form action="#">
                    <div class="icon2">
                        <img src="/img/dashboard/surat.png" alt="Undangan Icon">
                    </div>
                </form>
                <div class="content">
                    <h4><a href="#">Surat Undangan Internal Pelaksanaan</a></h4>
                    <p>5:12 pm • <span>Diterima</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection
