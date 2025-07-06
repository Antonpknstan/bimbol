<?php
$flashMessage = \App\Utils\Session::get('flash_message');
if ($flashMessage): 
    // Hapus pesan setelah ditampilkan
    \App\Utils\Session::set('flash_message', null);
?>
<div class="flash-message <?= htmlspecialchars($flashMessage['type'] ?? 'info') ?>">
    <?= htmlspecialchars($flashMessage['message'] ?? 'Pesan tidak diketahui.') ?>
</div>
<?php endif; ?>