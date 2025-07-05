<h1><?= htmlspecialchars($title) ?></h1>
<table class="data-table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Paket</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Kedaluwarsa</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($purchases)): ?>
            <tr>
                <td colspan="5">Anda belum pernah melakukan pembelian.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($purchases as $p): ?>
                <tr>
                    <td><?= date('d M Y, H:i', strtotime($p['purchase_date'])) ?></td>
                    <td><?= htmlspecialchars($p['package_name']) ?></td>
                    <td>Rp <?= number_format($p['price_at_purchase'], 0, ',', '.') ?></td>
                    <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td><?= $p['expiry_date'] ? date('d M Y', strtotime($p['expiry_date'])) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>