<h1>Hasil Asesmen: <?= htmlspecialchars($title) ?></h1>

<div class="assessment-summary">
    <p>Skor Anda: <span class="score-value"><?= number_format($attempt['score'], 2) ?></span></p>
    <p>Total Benar: <span class="correct-count"><?= $attempt['total_correct'] ?></span></p>
    <p>Total Salah: <span class="incorrect-count"><?= $attempt['total_incorrect'] ?></span></p>
    <p>Tidak Dijawab: <span class="unanswered-count"><?= $attempt['total_unanswered'] ?></span></p>
    <p>Status: <span class="status-badge status-<?= $attempt['status'] ?>"><?= ucfirst($attempt['status']) ?></span></p>
</div>

<h2>Detail Jawaban Anda</h2>
<div class="question-details">
    <?php foreach ($attempt['details'] as $index => $detail): ?>
        <div class="detail-card <?= $detail['is_correct'] ? 'correct' : 'incorrect' ?>">
            <h3>Pertanyaan <?= $index + 1 ?>:</h3>
            <p class="question-text"><?= nl2br(htmlspecialchars($detail['question_text'])) ?></p>
            
            <p><strong>Jawaban Anda:</strong>
                <?php if ($detail['chosen_answer_id']): ?>
                    <?= htmlspecialchars($detail['chosen_answer_text']) ?>
                <?php else: ?>
                    <em>Tidak Dijawab</em>
                <?php endif; ?>
                <?php if (!empty($detail['chosen_answer_image_url'])): ?>
                    <img src="/storage/uploads/<?= htmlspecialchars($detail['chosen_answer_image_url']) ?>" alt="Jawaban Anda" class="answer-image-small">
                <?php endif; ?>
            </p>
            
            <p><strong>Jawaban Benar:</strong>
                <?php if (!empty($detail['correct_answers'])): ?>
                    <?= htmlspecialchars(implode(', ', $detail['correct_answers'])) ?>
                <?php else: ?>
                    <em>Tidak ada jawaban benar yang terdaftar.</em>
                <?php endif; ?>
            </p>

            <?php if (!empty($detail['explanation'])): ?>
                <div class="explanation">
                    <h4>Penjelasan:</h4>
                    <p><?= nl2br(htmlspecialchars($detail['explanation'])) ?></p>
                    <?php if (!empty($detail['explanation_image_url'])): ?>
                        <img src="/storage/uploads/<?= htmlspecialchars($detail['explanation_image_url']) ?>" alt="Penjelasan" class="explanation-image">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<a href="/dashboard" class="button">Kembali ke Dashboard</a>