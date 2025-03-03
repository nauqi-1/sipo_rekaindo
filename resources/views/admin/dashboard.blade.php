@extends('layouts.admin')

@section('title', 'Admin')

@section('content')
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Beranda</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>{{ Auth::user()->username }}</strong> di <span class="system-name">Sistem Persuratan!</span> Anda login sebagai <span class="role-badge-admin">Admin</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Tinjauan</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <p>
                        <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                        <a href="#" style="text-decoration: none;"><span>4</span> Memo</a>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <p>
                        <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                        <a href="#" style="text-decoration: none;"><span>7</span> Risalah Rapat</a>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <p>
                        <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                        <a href="#" style="text-decoration: none;"><span>7</span> Undangan Rapat</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection