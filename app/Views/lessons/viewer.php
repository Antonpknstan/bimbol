<div class="breadcrumbs">
    <a href="/">Beranda</a> »
    <a href="/courses">Kursus</a> »
    <a href="/course/<?= $course['course_id'] ?>"><?= htmlspecialchars($course['title']) ?></a> »
    <span><?= htmlspecialchars($lesson['title']) ?></span>
</div>

<div class="lesson-viewer-container">
    <h1><?= htmlspecialchars($lesson['title']) ?></h1>

    <div class="lesson-content">
        <?php
        // Menampilkan konten berdasarkan tipenya
        switch ($lesson['content_type']):
            case 'text_content':
                echo nl2br(htmlspecialchars($lesson['content_data']));
                break;

            case 'video_embed':
                // Untuk keamanan, pastikan hanya iframe dari sumber terpercaya yang diizinkan
                // Kode ini mengasumsikan content_data adalah kode embed lengkap dari YouTube/Vimeo
                echo '<div class="responsive-iframe-container">' . $lesson['content_data'] . '</div>';
                break;

            case 'video_url':
                echo '<video width="100%" controls><source src="' . htmlspecialchars($lesson['content_data']) . '" type="video/mp4">Browser Anda tidak mendukung tag video.</video>';
                break;

            case 'audio_url':
                echo '<audio controls><source src="' . htmlspecialchars($lesson['content_data']) . '" type="audio/mpeg">Browser Anda tidak mendukung tag audio.</audio>';
                break;

            case 'document_pdf':
                // Pastikan path ke file PDF benar
                echo '<iframe src="/storage/uploads/' . htmlspecialchars($lesson['content_data']) . '" width="100%" height="600px"></iframe>';
                break;
            
            case 'downloadable_file':
                echo '<a href="/storage/uploads/' . htmlspecialchars($lesson['content_data']) . '" class="button button-primary" download>Unduh Materi</a>';
                break;
            
            case 'assessment_link':
                // content_data berisi ID asesmen
                echo '<a href="/assessment/start/' . htmlspecialchars($lesson['content_data']) . '" class="button button-primary">Mulai Kuis Terkait</a>';
                break;

            default:
                echo '<p>Tipe konten tidak didukung.</p>';
        endswitch;
        ?>
    </div>

    <div class="lesson-actions">
        <?php if (\App\Utils\Session::has('user')): ?>
            <?php if ($isCompleted): ?>
                <p class="completion-status completed">✔ Anda telah menyelesaikan pelajaran ini.</p>
            <?php else: ?>
                <button id="markCompleteBtn" class="button button-primary" data-lesson-id="<?= $lesson['lesson_id'] ?>">Tandai Selesai</button>
                <span id="completionMessage" style="margin-left: 10px; color: green; display: none;"></span>
            <?php endif; ?>
        <?php else: ?>
            <p>Silakan <a href="/login">login</a> untuk melacak kemajuan belajar Anda.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (\App\Utils\Session::has('user') && !$isCompleted): ?>
<script>
    document.getElementById('markCompleteBtn').addEventListener('click', function() {
        const lessonId = this.getAttribute('data-lesson-id');
        const btn = this;
        const msgElement = document.getElementById('completionMessage');

        btn.textContent = 'Memproses...';
        btn.disabled = true;

        fetch(`/progress/mark-lesson-complete/${lessonId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Nanti kita akan tambahkan X-CSRF-TOKEN di sini untuk keamanan
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Ganti tombol dengan pesan sukses
                const successMsg = document.createElement('p');
                successMsg.className = 'completion-status completed';
                successMsg.innerHTML = '✔ ' + data.message;
                btn.parentNode.replaceChild(successMsg, btn);
                msgElement.style.display = 'none';
            } else {
                msgElement.textContent = 'Gagal: ' + data.message;
                msgElement.style.color = 'red';
                msgElement.style.display = 'inline';
                btn.textContent = 'Tandai Selesai';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            msgElement.textContent = 'Terjadi kesalahan jaringan.';
            msgElement.style.color = 'red';
            msgElement.style.display = 'inline';
            btn.textContent = 'Tandai Selesai';
            btn.disabled = false;
        });
    });
</script>
<?php endif; ?>