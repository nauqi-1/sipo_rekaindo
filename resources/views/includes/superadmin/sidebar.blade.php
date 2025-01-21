 <!-- [ Sidebar Menu ] start -->
 <nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('dashboard') }}" class="b-brand text-primary">
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
          <a href="{{ route('superadmin.dashboard') }}" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon1.png" alt="" srcset=""></span><span class="pc-mtext">Dashboard</span></a
          >
        </li>
        <li class="pc-item">
          <a href="{{ route('memo.superadmin') }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon2.png" alt="" srcset=""></span>
            <span class="pc-mtext">Memo</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="{{ route('undangan.superadmin') }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon3.png" alt="" srcset=""></span>
            <span class="pc-mtext">Undangan Rapat</span>
          </a>
        </li>
        <li class="pc-item">
          <a href="{{ route('risalah.superadmin') }}" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon4.png" alt="" srcset=""></span>
            <span class="pc-mtext">Risalah Rapat</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon5.png" alt="" srcset=""></span><span class="pc-mtext">Arsip</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('arsip-memo.superadmin') }}">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-undangan.superadmin') }}" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="{{ route('arsip-risalah.superadmin') }}" class="pc-link">Risalah Rapat</span></a></li>
          </ul>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon6.png" alt="" srcset=""></span><span class="pc-mtext">Laporan</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Memo</a></li>
            <li class="pc-item pc-hasmenu"><a href="#!" class="pc-link">Undangan Rapat</span></a></li>
            <li class="pc-item pc-hasmenu"><a href="#!" class="pc-link">Risalah Rapat</span></a></li>
          </ul>
        </li>
        <li class="pc-item pc-caption">
          <label>OTHER</label>
          <i class="ti ti-brand-chrome"></i>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"><img src="/assets/images/ikon7.png" alt="" srcset=""></span><span class="pc-mtext">Settings</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('user.manage') }}">User Management</a></li>
            <li class="pc-item pc-hasmenu"><a href="#!" class="pc-link">Data Perusahaan</span></a></li>
          </ul>
        </li>
        <li class="pc-item">
          <a href="../other/sample-page.html" class="pc-link">
            <span class="pc-micon"><img src="/assets/images/ikon8.png" alt="" srcset=""></span>
            <span class="pc-mtext">info</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
