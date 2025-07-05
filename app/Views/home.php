<?php
// Tidak ada lagi <!DOCTYPE>, <head>, atau <body> di sini.
// Hanya konten spesifik untuk halaman ini.
?>

<h1><?= htmlspecialchars($title) ?></h1>
<p><?= htmlspecialchars($description) ?></p>
<p><small>Halaman ini berhasil dimuat melalui sistem routing dan dibungkus oleh master layout!</small></p>