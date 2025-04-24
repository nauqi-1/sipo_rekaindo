<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper">
    <div class="ms-auto">
      <ul class="list-unstyled">
       <!-- Notifikasi -->
<li class="dropdown pc-h-item position-relative">
    <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0" id="notif-toggle">
        <i class="ti ti-bell"></i>
        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notif-count">
            0
        </span>
    </a>

    <!-- Overlay Notifikasi -->
    <div class="notif-overlay" id="notif-overlay">
        <div class="notif-header">
            <h5>Notifikasi</h5>
            <button class="btn-close" id="notif-close">&times;</button>
        </div>

        <div class="notif-body" id="notif-body">
            <p class="text-muted text-center">Memuat notifikasi...</p>
        </div>
    </div>
</li>



        <!-- User Profile -->
        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown">
          @if(Auth::user()->profile_image)
            <img src="data:image/jpeg;base64,{{ Auth::user()->profile_image }}" alt="user-image" class="user-avtar" style=" width: 40px; height: 40px; object-fit: cover; border-radius: 50%;" />
          @else
            <img src="../assets/images/user/default1.png" alt="user-image" class="user-avtar" />
          @endif
            <span><i class="ti ti-settings"></i></span>
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header">
              <h4 style="text-transform: capitalize;">Selamat Pagi, {{ Auth::user()->username }} </h4>
              <p class="text-muted" style="text-transform: capitalize;">{{ Auth::user()->role->nm_role }} </p>
              <a href="{{ route('edit-profile.superadmin') }}" class="dropdown-item">
                <i class="ti ti-user"></i> <span>Profil</span>
              </a>
              <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ti ti-logout"></i> <span>Keluar</span>
              </a>
              <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
              </form>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>

<!-- [ Header ] end -->

<!-- Tambahkan CSS untuk Notifikasi Overlay -->
<style>
  .notif-overlay {
    position: absolute;
    top: 50px;
    right: 0;
    width: 300px;
    background: white;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    display: none;
    z-index: 999;
  }

  .notif-header {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .notif-body {
    max-height: 300px;
    overflow-y: auto;
  }

  .notif-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
  }

  .notif-item h6 {
    margin: 0;
  }

  .btn-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
  }
</style>

<!-- Tambahkan JavaScript -->
<script>
  document.getElementById('notif-toggle').addEventListener('click', function () {
    let overlay = document.getElementById('notif-overlay');
    overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
  });

  document.getElementById('notif-close').addEventListener('click', function () {
    document.getElementById('notif-overlay').style.display = 'none';
  });

  
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil jumlah notifikasi yang belum dibaca
        function fetchNotificationCount() {
            fetch("{{ route('notifications.count') }}")
                .then(response => response.json())
                .then(data => {
                    let count = data.count;
                    let notifCountElement = document.getElementById("notif-count");
                    notifCountElement.textContent = count > 0 ? count : "";
                });
        }
        
        function formatDate(dateString) {
            let options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };
            return new Intl.DateTimeFormat('id-ID', options).format(new Date(dateString)).replace(',', ' -');
        }

        // Ambil daftar notifikasi
        function fetchNotifications() {
            fetch("{{ route('notifications.index') }}")
                .then(response => response.json())
                .then(data => {
                    let notifBody = document.getElementById("notif-body");
                    notifBody.innerHTML = ""; // Kosongkan isi sebelumnya

                    if (data.notifications.length === 0) {
                        notifBody.innerHTML = '<p class="text-muted text-center">Tidak ada notifikasi terbaru.</p>';
                    } else {
                        data.notifications.forEach(notif => {
                            let notifItem = document.createElement("div");
                            notifItem.className = "notif-item" + (!notif.dibaca ? " bg-light" : ""); // Tambahkan highlight jika belum dibaca
                            notifItem.innerHTML = `
                                <h4><strong>${notif.judul}</strong></h4>
                                <p><strong>Perihal:</strong> ${notif.judul_document}</p>
                                <p><strong>Tanggal:</strong> ${formatDate(notif.updated_at)}</p>
                            `;
                            notifBody.appendChild(notifItem);
                        });
                    }
                });
        }

        // Tandai semua notifikasi sebagai sudah dibaca
        document.getElementById("notif-toggle").addEventListener("click", function() {
            fetch("{{ route('notifications.markAsRead') }}")
                .then(response => response.json())
                .then(() => {
                    fetchNotificationCount();
                });

            fetchNotifications();
        });

        // Jalankan saat halaman dimuat
        fetchNotificationCount();
    });

</script>
