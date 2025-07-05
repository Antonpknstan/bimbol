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
                <ul class="lesson-list">
                    <?php if (empty($module['lessons'])): ?>
                        <li class="lesson-item">Belum ada pelajaran di modul ini.</li>
                    <?php else: ?>
                        <?php foreach ($module['lessons'] as $lesson): ?>
                            <li class="lesson-item">
                                <span class="lesson-icon"></span>
                                <?= htmlspecialchars($lesson['title']) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>