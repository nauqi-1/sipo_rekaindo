@extends('layouts.manager')

@section('title', 'Admin')

@section('content')
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Beranda</h1>
        </header>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Selamat datang <strong>{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}</strong> di <span
                    class="system-name">Sistem Persuratan!</span> Anda login sebagai <span
                    class="role-badge">{{Auth::user()->position->nm_position}}</span></p>
        </div>

        <!-- Overview Section -->
        <div class="overview-container">
            <h3>Tinjauan</h3>
            <div class="overview-cards">
                <div class="overview-card">
                    <h4>MEMO</h4>
                    <a href="{{route('memo.admin')}}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/memo.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahMemo }}</span>
                        <span class="text">Memo</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>RISALAH RAPAT</h4>
                    <a href="{{ route('risalah.admin') }}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/risalah.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahRisalah }}</span>
                        <span class="text">Risalah Rapat</span>
                    </p>
                </div>
                <div class="overview-card">
                    <h4>UNDANGAN RAPAT</h4>
                    <a href="{{ route('undangan.admin') }}" class="tampil">Lihat Semua</a>
                    <hr>
                    <p>
                        <button><img src="/img/dashboard/undangan.png" alt="memo"></button>
                        <span class="jumlah">{{ $jumlahUndangan }}</span>
                        <span class="text">Undangan Rapat</span>
                    </p>
                </div>
            </div>
        </div>
        @foreach ($notifikasiByDate as $tanggal => $list)
            <div class="things-to-do-container">
                <h3>{{ $tanggal }}</h3>
                @foreach ($list as $notif)
                    <div class="things-to-do-list">
                        <form action="#">
                            <div
                                class="{{ $loop->iteration % 3 == 1 ? 'icon1' : ($loop->iteration % 3 == 2 ? 'icon2' : 'icon3') }}">
                                <img src="/img/dashboard/memoy.png" alt="Icon">
                            </div>
                        </form>
                        <div class="content">
                            <h4><a href="#">{{ $notif->judul }}</a></h4>
                            <p>{{ \Carbon\Carbon::parse($notif->updated_at)->format('g:i a') }} •
                                <span>{{ $notif->judul_document }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
@endsection