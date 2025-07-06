<header>
    <nav class="container">
        <a href="/" class="brand"><?= $_ENV['APP_NAME'] ?></a>
        <ul class="main-nav">
            <li><a href="/">Beranda</a></li>
            <li><a href="/courses">Kursus</a></li>
            <li><a href="/packages">Paket</a></li>
            <li><a href="/tryouts">Tryout</a></li>
        </ul>
        <ul class="user-nav">
            <?php if (\App\Utils\Session::has('user')): ?>
                <!-- Dropdown Notifikasi -->
                <li class="notification-dropdown">
                    <a href="#" id="notificationBell">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span id="notificationBadge" class="badge"></span>
                    </a>
                    <div id="notificationPanel" class="dropdown-panel">
                        <div class="panel-header">Notifikasi</div>
                        <div id="notificationList" class="panel-body">
                            <!-- Notifikasi akan dimuat di sini oleh JS -->
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </li>
                <!-- Akhir Dropdown Notifikasi -->

                <li><a href="/dashboard">Dashboard</a></li>
                <li><a href="/purchases/history">Riwayat</a></li>
                <li><a href="/logout" class="button">Logout</a></li>
            <?php else: ?>
                <li><a href="/login">Login</a></li>
                <li><a href="/register" class="button">Daftar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if (\App\Utils\Session::has('user')): ?>
<script>
// --- JavaScript untuk Notifikasi ---
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    const badge = document.getElementById('notificationBadge');
    const panel = document.getElementById('notificationPanel');
    const list = document.getElementById('notificationList');

    let isPanelOpen = false;
    let hasFetched = false;

    // Fungsi untuk mengambil notifikasi
    function fetchNotifications() {
        if (hasFetched) return; // Jangan fetch ulang jika sudah ada
        
        list.innerHTML = '<div class="loading-spinner"></div>'; // Tampilkan loading
        
        fetch('/notifications/fetch')
            .then(response => response.json())
            .then(data => {
                hasFetched = true;
                badge.textContent = data.unread_count > 0 ? data.unread_count : '';
                badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
                
                list.innerHTML = ''; // Hapus loading spinner
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="empty-state">Tidak ada notifikasi.</div>';
                    return;
                }
                
                data.notifications.forEach(notif => {
                    const item = document.createElement('a');
                    item.href = '#'; // Nanti bisa diarahkan ke URL terkait
                    item.className = 'notification-item' + (notif.is_read ? '' : ' unread');
                    item.innerHTML = `
                        <strong>${notif.title}</strong>
                        <p>${notif.message}</p>
                        <small>${new Date(notif.sent_at).toLocaleString('id-ID')}</small>
                    `;
                    list.appendChild(item);
                });
            })
            .catch(err => {
                console.error('Fetch notif error:', err);
                list.innerHTML = '<div class="empty-state">Gagal memuat notifikasi.</div>';
            });
    }

    // Fungsi untuk menandai notifikasi sebagai dibaca
    function markAsRead() {
        // Hanya kirim request jika ada notifikasi belum dibaca
        if (parseInt(badge.textContent) > 0) {
            fetch('/notifications/mark-read', { method: 'POST' })
                .then(() => {
                    badge.style.display = 'none';
                    badge.textContent = '0';
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                });
        }
    }

    // Event listener untuk tombol lonceng
    bell.addEventListener('click', function(event) {
        event.preventDefault();
        isPanelOpen = !isPanelOpen;
        panel.style.display = isPanelOpen ? 'block' : 'none';

        if (isPanelOpen) {
            fetchNotifications();
            // Tandai dibaca setelah panel dibuka
            setTimeout(markAsRead, 2000); // Beri jeda 2 detik sebelum menandai
        }
    });

    // Menutup dropdown jika klik di luar
    document.addEventListener('click', function(event) {
        if (!bell.contains(event.target) && !panel.contains(event.target)) {
            isPanelOpen = false;
            panel.style.display = 'none';
        }
    });

    // Panggil pertama kali untuk mendapatkan jumlah unread
    fetch('/notifications/fetch')
        .then(res => res.json())
        .then(data => {
            badge.textContent = data.unread_count > 0 ? data.unread_count : '';
            badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
        });
});
</script>
<?php endif; ?>