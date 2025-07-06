<h1><?= htmlspecialchars($title) ?></h1>
<p>Sisa Waktu: <span id="timer"><?= $attempt['time_limit_minutes'] ?? 'Tidak Terbatas' ?> menit</span></p>

<form action="/assessment/submit/<?= $attempt['attempt_id'] ?>" method="POST" id="assessmentForm">
    <?= \App\Utils\CSRF::field() ?>
    <input type="hidden" name="attempt_id" value="<?= $attempt['attempt_id'] ?>">

    <?php foreach ($attempt['questions'] as $index => $question): ?>
        <div class="question-card">
            <p class="question-number">Pertanyaan <?= $index + 1 ?></p>
            <div class="question-text"><?= nl2br(htmlspecialchars($question['question_text'])) ?></div>
            <?php if (!empty($question['question_image_url'])): ?>
                <img src="/storage/uploads/<?= htmlspecialchars($question['question_image_url']) ?>" alt="Gambar Pertanyaan" class="question-image">
            <?php endif; ?>

            <div class="answers-list">
                <?php foreach ($question['answers'] as $answer): ?>
                    <label class="answer-option">
                        <input type="radio" name="answers[<?= $question['question_id'] ?>]" value="<?= $answer['answer_id'] ?>" required>
                        <?= htmlspecialchars($answer['answer_text']) ?>
                        <?php if (!empty($answer['answer_image_url'])): ?>
                            <img src="/storage/uploads/<?= htmlspecialchars($answer['answer_image_url']) ?>" alt="Gambar Jawaban" class="answer-image">
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="button button-primary">Selesai Asesmen</button>
</form>

<script>
    // JavaScript untuk timer (opsional, bisa lebih kompleks)
    <?php if ($attempt['time_limit_minutes']): ?>
    let minutes = <?= (int) $attempt['time_limit_minutes'] ?>;
    let seconds = 0;
    let timerDisplay = document.getElementById('timer');

    function updateTimer() {
        timerDisplay.textContent = `${minutes} menit ${seconds < 10 ? '0' : ''}${seconds} detik`;
        if (minutes === 0 && seconds === 0) {
            clearInterval(timerInterval);
            alert('Waktu habis! Asesmen akan disubmit secara otomatis.');
            document.getElementById('assessmentForm').submit();
        } else if (seconds === 0) {
            minutes--;
            seconds = 59;
        } else {
            seconds--;
        }
    }

    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Panggil sekali untuk inisialisasi tampilan
    <?php endif; ?>
</script>