<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - Panel Admin' : 'Panel Admin' ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        body { display: flex; }
        .admin-sidebar { width: 250px; background: #343a40; color: #fff; min-height: 100vh; padding: 20px; }
        .admin-sidebar h3 { margin: 0 0 20px; }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li a { color: #ccc; text-decoration: none; display: block; padding: 10px; border-radius: 4px; }
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active { background: #495057; color: #fff; }
        .admin-content { flex-grow: 1; padding: 30px; }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <h3>Panel Admin</h3>
        <ul>
            <li><a href="/admin/dashboard" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="/admin/users" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : '' ?>">Manajemen User</a></li>
            <li><a href="/admin/upload/questions" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/upload/questions') !== false) ? 'active' : '' ?>">Upload Pertanyaan</a></li>
            <li><a href="/admin/reports" class="<?= (strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false) ? 'active' : '' ?>">Laporan Masalah</a></li>
            <li><a href="/">Kembali ke Situs</a></li>
        </ul>
    </div>
    <div class="admin-content">
        <h1><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
        <!-- Di sinilah konten admin akan disuntikkan -->
        <?= $content ?? '' ?>
    </div>
</body>
</html>