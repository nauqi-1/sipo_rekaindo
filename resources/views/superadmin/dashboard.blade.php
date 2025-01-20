@extends('layouts.app')

@section('title', 'Superadmin')

@section('content')
<div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>{{ Auth::user()->username }}</strong> di <span class="system-name">Sistem Persuratan!</span> Anda login sebagai <span class="role-badge">Super Admin</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Overview</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <p><strong>4</strong> Memo</p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <p><strong>7</strong> Risalah Rapat</p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <p><strong>15</strong> Undangan Rapat</p>
                </div>
            </div>
        </div>

        <!-- Things to Do Section -->
        <div class="things-to-do-container">
            <h3>Things to do</h3>
            <div class="things-to-do-list">
                <div class="things-to-do-item">
                    <div class="icon">
                        <img src="/assets/images/icon-memo.png" alt="Memo Icon">
                    </div>
                    <div class="content">
                        <h4>Meninjau Memo</h4>
                        <p>Tinjau memo untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>

                <div class="things-to-do-item">
                    <div class="icon">
                        <img src="/assets/images/icon-user.png" alt="User Icon">
                    </div>
                    <div class="content">
                        <h4>Tambah User Baru</h4>
                        <p>Tinjau untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>

                <div class="things-to-do-item">
                    <div class="icon">
                        <img src="/assets/images/icon-surat.png" alt="Surat Icon">
                    </div>
                    <div class="content">
                        <h4>Meninjau Permintaan Surat</h4>
                        <p>Tinjau permintaan surat untuk kelangkah selanjutnya</p>
                    </div>
                    <div class="date">Hari ini</div>
                </div>
            </div>
        </div>
    </div>
@endsection
