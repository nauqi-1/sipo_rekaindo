@extends('layouts.app')

@section('title', 'Informasi SIPO')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Info</h1>
    </div>
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Info</a>
            </div>
        </div>
    </div>

    <!-- form add memo -->
    <div class="info">
        <div class="card">
            <div class="bg-container">
                <p>Tentang Sistem<br><br>Sistem manajemen persuratan ini dirancang untuk memudahkan pengelolaan Memo, Undangan Rapat, dan Risalah Rapat di dalam ruang lingkup PT Rekaindo Global Jasa. Sistem ini memungkinkan pembuatan, pengeditan, persetujuan, dan pengarsipan dokumen secara efisien.</p>
            </div>
            <div class="reka-info">
                <img src="/img/reka-info.png" alt="">
            </div>
        </div>
    </div>
</div>
@endsection