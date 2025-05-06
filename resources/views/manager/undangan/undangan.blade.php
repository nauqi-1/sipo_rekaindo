@extends('layouts.manager')

@section('title', 'undangan Manager')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('manager.dashboard')}}"><img src="/img/undangan/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="breadcrumb" style="gap: 5px; width: 83%;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Undangan Rapat</a>
                </div>
                <label style="margin: 0; padding-bottom: 25px; padding-right: 12px; color: #565656;">
                Show
                <select name="per_page" onchange="this.form.submit()" style="color: #565656; padding: 2px 5px;"> 
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                entries
            </label>
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
                    <th>Tanggal Undangan
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
            @foreach ($undangans as $index =>$undangan)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                    <td class="nama-dokumen 
                        {{ $undangan->status == 'reject' ? 'text-danger' : ($undangan->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $undangan->undangan->judul }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($undangan->undangan->tgl_dibuat)->format('d-m-Y') }}</td>
                    <td>{{ $undangan->undangan->seri_surat }}</td>
                    <td>{{ $undangan->undangan->nomor_undangan }}</td>
                    <td>{{ $undangan->undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $undangan->undangan->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    </td>
                    <td>
                        @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')

                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route ('persetujuan.undangan',['id'=>$undangan->undangan->id_undangan])}}" class="btn btn-sm1">
                            <img src="/img/undangan/share.png" alt="share">
                        </a>
                        <a class="btn btn-sm3" href="{{route ('view.undangan',['id'=>$undangan->undangan->id_undangan])}}">

                            <img src="/img/undangan/viewBlue.png" alt="view">
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $undangans->links('pagination::bootstrap-5') }}
    </div>
@endsection