<h1>Selamat Datang, <?= htmlspecialchars(\App\Utils\Session::get('user')['full_name'] ?? 'Pengguna') ?>!</h1>
<p>Anda telah berhasil login dan berada di halaman dashboard.</p>

<div style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <h2>Asesmen Cepat</h2>
    <p>Coba asesmen demo untuk menguji kemampuan Anda.</p>
    <!-- Ganti '1' dengan ID asesmen yang valid dari database Anda -->
    <a href="/assessment/start/1" class="button button-primary">Mulai Asesmen Demo</a>
</div>