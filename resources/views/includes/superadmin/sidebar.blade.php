<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route(Auth::user()->role->nm_role . '.dashboard') }}" class="b-brand text-primary">
      <a href="{{ route(Auth::user()->role->nm_role . '.dashboard') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="/assets/images/LOGO.png"/>
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label> MENU </label>
          <i class="ti ti-dashboard"></i>
        </li>
        <li class="pc-item">
          <a href="{{ route(Auth::user()->role->nm_role . '.dashboard') }}" class="pc-link"
          <a href="{{ route(Auth::user()->role->nm_role . '.dashboard') }}" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon1.png" alt="" srcset=""></span><span class="pc-mtext">Dashboard</span></a
          >
        </li>
        @if(Auth::user()->role->nm_role != 'manager')
        @if(Auth::user()->role->nm_role != 'manager')
        <li class="pc-item">
          <a href="{{ route('memo.' . Auth::user()->role->nm_role) }}" class="pc-link">
          <a href="{{ route('memo.' . Auth::user()->role->nm_role) }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon2.png" alt="" srcset=""></span>
            <span class="pc-mtext">Memo</span><span class="pc-arrow">
            <span class="pc-mtext">Memo</span><span class="pc-arrow">
          </a>
        </li>
        @endif
        @if(Auth::user()->role->nm_role == 'manager')
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon3.png" alt="" srcset=""></span><span class="pc-mtext">Memo</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('memo.terkirim',Auth::user()->id) }}">Memo Terkirim</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('memo.diterima',Auth::user()->id) }}" class="pc-link">Memo Diterima</span></a></li>
          </ul>
        </li>
        @endif

        <li class="pc-item">
          <a href="{{ route('undangan.' . Auth::user()->role->nm_role) }}" class="pc-link">
          <a href="{{ route('undangan.' . Auth::user()->role->nm_role) }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon3.png" alt="" srcset=""></span>
            <span class="pc-mtext">Undangan Rapat</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="{{ route('risalah.' . Auth::user()->role->nm_role) }}" class="pc-link">
          <a href="{{ route('risalah.' . Auth::user()->role->nm_role) }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon4.png" alt="" srcset=""></span>
            <span class="pc-mtext">Risalah Rapat</span>
          </a>
        </li>
        @if(Auth::user()->role->nm_role != 'manager')
        @if(Auth::user()->role->nm_role != 'manager')
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon5.png" alt="" srcset=""></span><span class="pc-mtext">Arsip</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('arsip-memo.' . Auth::user()->role->nm_role) }}">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-undangan.' . Auth::user()->role->nm_role) }}" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-risalah.' . Auth::user()->role->nm_role) }}" class="pc-link">Risalah Rapat</span></a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('arsip-memo.' . Auth::user()->role->nm_role) }}">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-undangan.' . Auth::user()->role->nm_role) }}" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-risalah.' . Auth::user()->role->nm_role) }}" class="pc-link">Risalah Rapat</span></a></li>
          </ul>
        </li>
        @endif
        @if(Auth::user()->role->nm_role == 'superadmin')
        @endif
        @if(Auth::user()->role->nm_role == 'superadmin')
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon6.png" alt="" srcset=""></span><span class="pc-mtext">Laporan</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('laporan-memo.superadmin')}}">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('laporan-undangan.superadmin')}}" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('laporan-risalah.superadmin')}}" class="pc-link">Risalah Rapat</span></a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('laporan-memo.superadmin')}}">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('laporan-undangan.superadmin')}}" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('laporan-risalah.superadmin')}}" class="pc-link">Risalah Rapat</span></a></li>
          </ul>
        </li>
        @endif
        @endif
        <li class="pc-item pc-caption">
          <label>LAINNYA</label>
          <label>LAINNYA</label>
          <i class="ti ti-brand-chrome"></i>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon7.png" alt="" srcset=""></span><span class="pc-mtext">Settings</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
          @if(Auth::user()->role->nm_role == 'superadmin')
          @if(Auth::user()->role->nm_role == 'superadmin')
            <li class="pc-item"><a class="pc-link" href="{{ route('user.manage') }}">User Management</a></li>
          @endif
            <li class="pc-item pc-hasmenu"><a href="{{ route('data-perusahaan') }}" class="pc-link">Data Perusahaan</span></a></li>
          @endif
            <li class="pc-item pc-hasmenu"><a href="{{ route('data-perusahaan') }}" class="pc-link">Data Perusahaan</span></a></li>
          </ul>
        </li>
        <li class="pc-item">
          <a href="{{ route('info') }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon8.png" alt="" srcset=""></span>
            <span class="pc-mtext">info</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->