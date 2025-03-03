@extends('layouts.superadmin')

@section('title', 'Laporan Undangan Rapat')
      
@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="#"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Laporan Undangan Rapat</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper">
            <div class="breadcrumb" style="gap: 5px;">
                <a href="#">Beranda</a>/<a href="#">Laporan</a>/<a href="#" style="color: #565656;">Laporan Undangan Rapat</a>
            </div>
        </div>
    </div>

    <!-- form add udangan -->
    <div class="laporan">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('cetak-laporan-undangan.filter') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card-blue">
                                <img src="/img/laporan/tanggal.png" alt="date">
                                <span>Tanggal Awal</span>
                            </div>
                            <input type="date" name="tgl_awal" id="tgl_awal" class="form-control" required>
                            <p>* Masukkan tanggal awal filter data undangan!</p>
                        </div> 
                        <div class="col-md-6">
                            <div class="card-blue">
                                <img src="/img/laporan/tanggal.png" alt="date">
                                <span>Tanggal Akhir</span>
                            </div>
                            <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control" required>
                            <p>* Masukkan tanggal akhir filter data undangan!</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="filter-button">Filter</button>
                        <button type="button" class="btn btn-secondary" id="cancel-button">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
