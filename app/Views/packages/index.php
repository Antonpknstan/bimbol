<h1><?= htmlspecialchars($title) ?></h1>
<p>Investasikan untuk masa depan Anda dengan memilih paket terbaik kami.</p>

<div class="package-grid">
    <?php if (empty($packages)): ?>
        <p>Saat ini belum ada paket yang tersedia.</p>
    <?php else: ?>
        <?php foreach ($packages as $package): ?>
            <div class="package-card">
                <div class="package-header">
                    <h3><?= htmlspecialchars($package['name']) ?></h3>
                    <div class="package-price">Rp <?= number_format($package['price'], 0, ',', '.') ?></div>
                </div>
                <div class="package-body">
                    <p><?= nl2br(htmlspecialchars($package['description'])) ?></p>
                    <p><strong>Durasi Aktif:</strong> <?= $package['duration_days'] ?> hari</p>
                </div>
                <div class="package-footer">
                    <form action="/purchase/<?= $package['package_id'] ?>" method="POST">
                        <button type="submit" class="button button-primary">Beli Sekarang</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>