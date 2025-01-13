<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar and Navbar</title>
    <link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="/icon/2.png" alt="Logo Reka">
        </div>
        <ul class="menu">
            <p>MENU</p>
            <li><a href="#"><img src="/icon/ikon1.png" alt="Ikon Dashboard">&nbsp; Dashboard</a></li>
            <li><a href="#"><img src="/icon/ikon2.png" alt="Ikon Memo">&nbsp; Memo</a></li>
            <li><a href="#"><img src="/icon/ikon3.png" alt="Ikon Undangan Rapat">&nbsp; Undangan Rapat</a></li>
            <li><a href="#"><img src="/icon/ikon4.png" alt="Ikon Risalah Rapat">&nbsp; Risalah Rapat</a></li>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img src="/icon/ikon5.png" alt="Ikon Arsip">&nbsp; Arsip<img class="opsi-icon1" src="/icon/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">Memo</a></li>
                    <li><a href="#">Undangan Rapat</a></li>
                    <li><a href="#">Risalah Rapat</a></li>
                </ul>
            </li>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img src="/icon/ikon6.png" alt="Ikon Laporan">&nbsp; Laporan<img class="opsi-icon2" src="/icon/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">Memo</a></li>
                    <li><a href="#">Undangan Rapat</a></li>
                    <li><a href="#">Risalah Rapat</a></li>
                </ul>
            </li>
            <p class="other-title">OTHERS</p>
            <li class="submenu">
                <a href="#" class="toggle-submenu"><img class="setting" src="/icon/vector.png" alt="Ikon Setting">&nbsp; Setting<img class="opsi-icon3" src="/icon/ikon10.png" alt="ikon panah opsi"></a>
                <ul class="submenu-items">
                    <li><a href="#">User Management</a></li>
                    <li><a href="#">Data Lembaga</a></li>
                    <li><a href="#">Edit Profile</a></li>
                </ul>
            </li>
            <li><a href="#"><img src="/icon/ikon8.png" alt="Ikon Memo">&nbsp; Info</a></li>
        </ul>
    </div>

    <!-- Navbar -->
    <div class="navbar" id="navbar">
        <button class="toggle-btn" id="toggleSidebar">
            &#9776;
        </button>
        <div class="navbar-user">
            <img src="/icon/me1.jpg" alt="User Icon" class="user-icon">
            <img src="/icon/dropdown.png" alt="Dropdown Arrow" class="dropdown-arrow">
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