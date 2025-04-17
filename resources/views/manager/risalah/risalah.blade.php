ris manager

@extends('layouts.manager')

@section('title', 'Risalah Manager')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('manager.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
            </div>
            <h1>Risalah Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="surat">
            <div class="header-tools">
                <div class="search-filter">
                    <div class="dropdown">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Status</option>
                            <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Diterima</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Diproses</option>
                            <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" id="tgl_dibuat_awal" name="tgl_dibuat_awal" class="form-control date-placeholder" value="{{ request('tgl_dibuat_awal') }}" placeholder="Tanggal Awal" onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Awal'; }" onchange="this.form.submit()">
                    </div>
                    <i class="bi bi-arrow-right"></i>
                    <div class="input-icon-wrapper" style="position: relative; width: 150px;">
                        <input type="text" id="tgl_dibuat_akhir" name="tgl_dibuat_akhir"
                            class="form-control date-placeholder" value="{{ request('tgl_dibuat_akhir') }}" placeholder="Tanggal Akhir"
                            onfocus="this.type='date'" onblur="if(!this.value){ this.type='text'; this.placeholder='Tanggal Akhir'; }" onchange="this.form.submit()">
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn btn-search d-flex align-items-center" style="gap: 5px;">
                            <img src="/img/memo-admin/search.png" alt="search" style="width: 20px; height: 20px;">
                            <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Cari" value="{{ request('search') }}" onchange="this.form.submit()" style="outline: none; box-shadow: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Tanggal Risalah
                        <button class="data-md">
                            <a href="" style="color:rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                        </button>
                    </th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Tanggal Disahkan
                        <button class="data-md">
                            <a href="" style="color: rgb(135, 135, 148); text-decoration: none;"><span class="bi-arrow-down-up"></span></a>
                        </button>
                    </th>
                    <th>Divisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($risalahs as $index =>$risalah)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ $risalah->risalah->status == 'reject' ? 'text-danger' : ($risalah->risalah->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $risalah->risalah->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($risalah->risalah->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $risalah->risalah->seri_surat }}</td>
                    <td>{{ $risalah->risalah->nomor_risalah }}</td>
                    <td>{{ $risalah->risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $risalah->risalah->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                    <td>
                        @if ($risalah->risalah->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($risalah->risalah->status == 'pending')

                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route ('persetujuan.risalah',['id'=>$risalah->risalah->id_risalah])}}" class="btn btn-sm1">
                            <img src="/img/undangan/share.png" alt="share">
                        </a>
                        <a class="btn btn-sm3" href="{{route ('view.risalah',['id'=>$risalah->risalah->id_risalah])}}">

                            <img src="/img/undangan/viewBlue.png" alt="view">
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection