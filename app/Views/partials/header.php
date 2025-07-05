<header>
    <nav class="container">
        <a href="/" class="brand"><?= $_ENV['APP_NAME'] ?></a>
        <ul>
            <li><a href="/">Beranda</a></li>
            <li><a href="/courses">Kursus</a></li>
            <?php if (\App\Utils\Session::has('user')): ?>
                <li><a href="/dashboard">Dashboard</a></li>
                <li><a href="/logout" class="button">Logout</a></li>
            <?php else: ?>
                <li><a href="/login">Login</a></li>
                <li><a href="/register" class="button">Daftar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>