<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipe</th>
                <th>ID Item</th>
                <th>Deskripsi Masalah</th>
                <th>Pelapor</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= $report['report_id'] ?></td>
                    <td><?= htmlspecialchars(ucfirst($report['report_type'])) ?></td>
                    <td><?= $report['item_id_ref'] ?></td>
                    <td><?= htmlspecialchars($report['problem_description']) ?></td>
                    <td><?= htmlspecialchars($report['reporter_username']) ?></td>
                    <td><span class="status-badge status-<?= $report['status'] ?>"><?= $report['status'] ?></span></td>
                    <td>
                        <form action="/admin/reports/<?= $report['report_id'] ?>/status" method="POST"> <?= \App\Utils\CSRF::field() ?>
                            <select name="status">
                                <option value="in_review" <?= $report['status'] == 'in_review' ? 'selected' : '' ?>>In Review</option>
                                <option value="resolved" <?= $report['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                            <button type="submit" class="button button-sm">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>