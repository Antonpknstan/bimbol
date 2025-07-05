<h1><?= htmlspecialchars($title) ?></h1>
<p>Jelajahi berbagai pilihan kursus yang kami sediakan untuk meningkatkan keahlian Anda.</p>

<div class="course-grid">
    <?php if (empty($courses)): ?>
        <p>Belum ada kursus yang tersedia saat ini.</p>
    <?php else: ?>
        <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div class="course-card-body">
                    <span class="course-subject"><?= htmlspecialchars($course['subject_name'] ?? 'Umum') ?></span>
                    <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                    <p class="course-description"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                </div>
                <div class="course-card-footer">
                    <a href="/course/<?= $course['course_id'] ?>" class="button button-secondary">Lihat Detail</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>