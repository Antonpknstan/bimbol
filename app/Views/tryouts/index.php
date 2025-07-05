<h1><?= htmlspecialchars($title) ?></h1>
<p>Uji kemampuan Anda dan bersaing dengan peserta lain dalam tryout berbatas waktu kami.</p>

<div class="tryout-list">
    <?php if (empty($tryouts)): ?>
        <p>Tidak ada tryout yang aktif saat ini. Cek kembali nanti!</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Tryout</th>
                    <th>Materi</th>
                    <th>Dimulai</th>
                    <th>Berakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tryouts as $tryout): 
                    $now = new DateTime();
                    $startTime = new DateTime($tryout['start_time']);
                    $endTime = new DateTime($tryout['end_time']);
                    $isOngoing = ($now >= $startTime && $now <= $endTime);
                    $isUpcoming = ($now < $startTime);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($tryout['name']) ?></td>
                        <td><?= htmlspecialchars($tryout['assessment_title']) ?></td>
                        <td><?= $startTime->format('d M Y, H:i') ?></td>
                        <td><?= $endTime->format('d M Y, H:i') ?></td>
                        <td>
                            <?php if ($isOngoing): ?>
                                <a href="/assessment/start/<?= $tryout['assessment_id'] ?>" class="button button-primary">Kerjakan Sekarang</a>
                                <a href="/tryout/leaderboard/<?= $tryout['tryout_period_id'] ?>" class="button button-secondary">Lihat Peringkat</a>
                            <?php elseif ($isUpcoming): ?>
                                <span class="status-badge" style="background-color: #17a2b8;">Akan Datang</span>
                            <?php else: ?>
                                <span class="status-badge" style="background-color: #6c757d;">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>