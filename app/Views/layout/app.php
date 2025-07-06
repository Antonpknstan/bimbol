<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul halaman akan diisi secara dinamis -->
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' . $_ENV['APP_NAME'] : $_ENV['APP_NAME'] ?></title>
    <!-- Link ke file CSS utama kita -->
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

    <?php require __DIR__ . '/../partials/header.php'; // Memasukkan header ?>

    <main class="container">
        <?php require __DIR__ . '/../partials/flash_message.php'; ?>
        <!-- Di sinilah konten spesifik halaman akan disuntikkan -->
        <?= $content ?? '' ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; // Memasukkan footer ?>

</body>
</html>