@extends('layouts.manager')

@section('title', 'Memo Masuk')

@section('content')
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="#"><img src="/img/memo-supervisor/Vector_back.png" alt="back"></a>
            </div>
            <h1>Memo Masuk</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="breadcrumb" style="gap: 5px; width: 83%;">
                    <a href="#">Beranda</a>/<a href="#" style="color: #565656;">Memo Masuk</a>
                </div>
                <form method="GET" action="{{ route('memo.diterima', Auth::user()->id) }}" class="d-flex gap-2">

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
                </form>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="surat">
            <div class="header-tools">
                <div class="search-filter">
                    <form method="GET" action="{{ route('memo.diterima', Auth::user()->id) }}" class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="dropdown d-flex gap-3" style="position:relative; width: 300px;">
                    <select name="divisi_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Memo</option>
                        <option value="own" {{ request('divisi_filter') == 'own' ? 'selected' : '' }}>Memo Keluar</option>
                        <option value="other" {{ request('divisi_filter') == 'other' ? 'selected' : '' }}>Memo Masuk</option>
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
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Tanggal Memo
                        <button class="data-md">
                           <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'memo.tgl_dibuat','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                        </button>
                    </th>
                    <th>Seri</th>
                    <th>Dokumen</th>
                    <th>Tanggal Disahkan
                        <button class="data-md">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'memo.tgl_disahkan','sort_direction' => $sortDirection === 'desc' ? 'asc' : 'desc']) }}"
                            style="color:rgb(135, 135, 148); text-decoration: none;">
                            <span class="bi-arrow-down-up"></span>
                        </a>
                        </button>
                    </th>
                    <th>Divisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($memoDiterima as $index => $kirim)
                <tr>
                    <td class="nomor">{{ $index + 1 }}</td>
                   
                        <td class="nama-dokumen {{ $kirim->status == 'reject' ? 'text-danger' : ($kirim->status == 'pending' ? 'text-warning' : 'text-success') }}">
                        {{ $kirim->memo->judul }}</td>
                    
                    <!-- <td>{{ $kirim->memo->tgl_dibuat }}</td> -->
                    <td>{{ $kirim->memo->tgl_dibuat ? \Carbon\Carbon::parse($kirim->memo->tgl_dibuat)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $kirim->memo->seri_surat }}</td>
                    <td>{{ $kirim->memo->nomor_memo }}</td>
                    <td>{{ $kirim->memo->tgl_disahkan ? \Carbon\Carbon::parse($kirim->memo->tgl_disahkan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $kirim->memo->divisi->nm_divisi ?? 'No Divisi Assigned' }}</td>
                    <td>
                        
                            @if ($kirim->status == 'reject')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($kirim->status == 'pending')
                                <span class="badge bg-warning">Diproses</span>
                            @elseif ($kirim->status == 'correction')
                                <span class="badge bg-danger">Dikoreksi</span>
                            @else
                                <span class="badge bg-success">Diterima</span>
                            @endif
                        
                    </td>
                    <td>
                        <a class="btn btn-sm3" href="{{ route('view.memo-diterima',$kirim->id_document) }}">
                            <img src="/img/memo-supervisor/viewBlue.png" alt="view">
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $memoDiterima->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endsection