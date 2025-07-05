<h1><?= htmlspecialchars($title) ?></h1>

<div class="leaderboard-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Nama Pengguna</th>
                <th>Skor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leaderboard)): ?>
                <tr>
                    <td colspan="3">Peringkat belum tersedia atau belum ada yang menyelesaikan tryout ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($leaderboard as $entry): ?>
                    <tr class="<?= ($entry['rank'] <= 3) ? 'top-rank' : '' ?>">
                        <td class="rank-cell">
                            <?php if ($entry['rank'] == 1): ?> 磊 <!-- Gold Medal -->
                            <?php elseif ($entry['rank'] == 2): ?> 賂 <!-- Silver Medal -->
                            <?php elseif ($entry['rank'] == 3): ?> 雷 <!-- Bronze Medal -->
                            <?php else: ?>
                                <?= $entry['rank'] ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($entry['full_name']) ?> (@<?= htmlspecialchars($entry['username']) ?>)</td>
                        <td><?= number_format($entry['score'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>