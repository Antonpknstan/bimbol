<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' . $_ENV['APP_NAME'] : $_ENV['APP_NAME'] ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <a href="/" class="brand"><?= $_ENV['APP_NAME'] ?></a>
            <h2><?= htmlspecialchars($title) ?></h2>
        </div>
        <!-- Di sinilah konten form (login/register) akan disuntikkan -->
        <?= $content ?? '' ?>
    </div>
</body>
</html>