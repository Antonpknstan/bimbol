<?php

require __DIR__ . '/vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Tidak dapat menemukan file .env untuk Phinx. Pastikan file tersebut ada di root proyek.");
}

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter'   => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'name'      => $_ENV['DB_DATABASE'],
            'user'      => $_ENV['DB_USERNAME'],
            'pass'      => $_ENV['DB_PASSWORD'],
            'port'      => $_ENV['DB_PORT'] ?? 3306,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]
    ],
    'version_order' => 'creation'
];