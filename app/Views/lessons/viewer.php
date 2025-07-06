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

<div class="report-section">
    <button id="reportBtn" class="button button-sm button-danger">Laporkan Masalah</button>
</div>

<!-- Modal untuk Laporan (awalnya disembunyikan) -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close-button">×</span>
        <h2>Laporkan Masalah</h2>
        <p>Ada masalah dengan pelajaran "<?= htmlspecialchars($lesson['title']) ?>"? Beri tahu kami.</p>
        <form id="reportForm">
            <input type="hidden" name="report_type" value="lesson">
            <input type="hidden" name="item_id" value="<?= $lesson['lesson_id'] ?>">
            <div class="form-group">
                <label for="description">Jelaskan masalahnya:</label>
                <textarea name="description" id="description" rows="4" required></textarea>
            </div>
            <button type="submit" class="button button-primary">Kirim Laporan</button>
        </form>
        <div id="reportResponse"></div>
    </div>
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


// --- JavaScript untuk Modal Laporan ---
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reportModal');
    const reportBtn = document.getElementById('reportBtn');
    const closeBtn = document.querySelector('.modal .close-button');
    const reportForm = document.getElementById('reportForm');
    const reportResponse = document.getElementById('reportResponse');

    if (reportBtn) {
        // Tampilkan modal saat tombol report diklik
        reportBtn.onclick = function() {
            modal.style.display = 'block';
        }
    }
    
    if (closeBtn) {
        // Sembunyikan modal saat tombol close diklik
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
    }

    // Sembunyikan modal saat mengklik di luar area konten modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    if (reportForm) {
        // Tangani pengiriman form laporan dengan AJAX
        reportForm.onsubmit = function(event) {
            event.preventDefault(); // Mencegah form submit biasa

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';
            reportResponse.textContent = '';
            reportResponse.style.color = '';

            fetch('/report/submit', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    reportResponse.textContent = data.message;
                    reportResponse.style.color = 'green';
                    // Kosongkan form dan sembunyikan modal setelah beberapa detik
                    setTimeout(() => {
                        reportForm.reset();
                        modal.style.display = 'none';
                    }, 2000);
                } else {
                    reportResponse.textContent = 'Error: ' + data.message;
                    reportResponse.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                reportResponse.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                reportResponse.style.color = 'red';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim Laporan';
            });
        }
    }
});
</script>
<?php endif; ?>