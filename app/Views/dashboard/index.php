<h1>Selamat Datang, <?= htmlspecialchars(\App\Utils\Session::get('user')['full_name'] ?? 'Pengguna') ?>!</h1>
<p>Anda telah berhasil login dan berada di halaman dashboard.</p>