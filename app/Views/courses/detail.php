<div class="course-detail-header">
    <h1><?= htmlspecialchars($course['title']) ?></h1>
    <p class="course-description-full"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
</div>

<div class="module-list">
    <h2>Materi Pembelajaran</h2>
    <?php if (empty($modules)): ?>
        <p>Materi untuk kursus ini akan segera tersedia.</p>
    <?php else: ?>
        <?php foreach ($modules as $module): ?>
    <div class="module-item">
        <h3><?= htmlspecialchars($module['title']) ?></h3>
        <?php if (!empty($module['pre_test_assessment_id'])): ?>
            <p>
                <a href="/assessment/start/<?= $module['pre_test_assessment_id'] ?>" class="button button-sm">Mulai Pre-Test</a>
            </p>
        <?php endif; ?>
        <ul class="lesson-list">
            <?php if (empty($module['lessons'])): ?>
                <li class="lesson-item">Belum ada pelajaran di modul ini.</li>
            <?php else: ?>
                <?php foreach ($module['lessons'] as $lesson):
                    // Pastikan $lesson dan propertinya ada sebelum digunakan
                    $lessonId = $lesson['lesson_id'] ?? null;
                    $lessonTitle = $lesson['title'] ?? 'Judul Pelajaran Tidak Tersedia';
                ?>
                    <li class="lesson-item">
                        <?php if ($lessonId): // Hanya buat link jika lessonId valid ?>
                            <a href="/lesson/<?= htmlspecialchars($lessonId) ?>">
                                <span class="lesson-icon"></span>
                                <?= htmlspecialchars($lessonTitle) ?>
                            </a>
                        <?php else: ?>
                            <span class="lesson-icon"></span>
                            <?= htmlspecialchars($lessonTitle) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php if (!empty($module['post_test_assessment_id'])): ?>
            <p style="text-align: right;">
                <a href="/assessment/start/<?= $module['post_test_assessment_id'] ?>" class="button button-sm">Mulai Post-Test</a>
            </p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    <?php endif; ?>
</div>