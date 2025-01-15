<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="{{asset('css/sidebar.css')}}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="/img/sidebar/LOGO REKA 1.png" alt="Logo Reka">
        </div>
        <ul class="menu">
            <p>MENU</p>
            <li><a href="#"><img src="/img/sidebar/ikon1.png" alt="Ikon Dashboard">&nbsp; Dashboard</a></li>
            <li><a href="#"><img src="/img/sidebar/ikon2.png" alt="Ikon Memo">&nbsp; Memo</a></li>
            <li><a href="#"><img src="/img/sidebar/ikon3.png" alt="Ikon Undangan Rapat">&nbsp; Undangan Rapat</a></li>
            <li><a href="#"><img src="/img/sidebar/ikon4.png" alt="Ikon Risalah Rapat">&nbsp; Risalah Rapat</a></li>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img src="/img/sidebar/ikon5.png" alt="Ikon Arsip">&nbsp; Arsip<img class="opsi-icon1" src="/img/sidebar/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">Memo</a></li>
                    <li><a href="#">Undangan Rapat</a></li>
                    <li><a href="#">Risalah Rapat</a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img src="/img/sidebar/ikon6.png" alt="Ikon Laporan">&nbsp; Laporan<img class="opsi-icon2" src="/img/sidebar/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">Memo</a></li>
                    <li><a href="#">Undangan Rapat</a></li>
                    <li><a href="#">Risalah Rapat</a></li>
                </ul>
            </li>
            <p class="other-title">OTHERS</p>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img class="setting" src="/img/sidebar/vector.png" alt="Ikon Setting">&nbsp; Setting<img class="opsi-icon3" src="/img/sidebar/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">User Management</a></li>
                    <li><a href="#">Data Lembaga</a></li>
                    <li><a href="#">Edit Profile</a></li>
                </ul>
            </li>
            <li><a href="#"><img src="/img/sidebar/ikon8.png" alt="Ikon Memo">&nbsp; Info</a></li>
        </ul>
    </div>

    <!-- Navbar -->
    <div class="navbar" id="navbar">
        <button class="toggle-btn" id="toggleSidebar">
            &#9776;
        </button>
        <div class="navbar-user">
            <img src="/img/sidebar/me.png" alt="User Icon" class="user-icon">
            <img src="/img/sidebar/vector.png" alt="Dropdown Arrow" class="dropdown-arrow">
            <ul class="dropdown-menu">
                <li><a href="#">Profile</a></li>
                <li><a href="#">Setting</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>
    </div>

    <script>
        const toggleSubmenus = document.querySelectorAll('.toggle-submenu');

        toggleSubmenus.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = toggle.nextElementSibling;
                submenu.classList.toggle('show');
            });
        });

        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const navbar = document.getElementById('navbar');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            navbar.classList.toggle('hidden');
        });

        const navbarUser = document.querySelector('.navbar-user');

        navbarUser.addEventListener('click', () => {
            navbarUser.classList.toggle('active');
        });
    </script>
</body>
</html>