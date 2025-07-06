<?php
// Ambil flash message dari sesi (jika ada)
$flashMessage = $flash_message ?? null; // Menggunakan variabel dari controller

// Siapkan variabel untuk view dengan nilai default yang aman
$message_text = '';
$message_type = 'info'; // Default ke 'info' jika tidak ada tipe

if (!empty($flashMessage) && is_array($flashMessage)) {
    // Pastikan kunci 'message' dan 'type' ada sebelum digunakan
    $message_text = $flashMessage['message'] ?? 'Pesan tidak diketahui.';
    $message_type = $flashMessage['type'] ?? 'info';
}
?>

<?php if (!empty($message_text)): ?>
    <div class="flash-message <?= htmlspecialchars($message_type) ?>">
        <?= htmlspecialchars($message_text) ?>
    </div>
<?php endif; ?>

<h3>Detail Batch</h3>
<p><strong>ID Batch:</strong> <?= $batch['batch_id'] ?></p>
<p><strong>File Excel:</strong> <?= htmlspecialchars($batch['original_filename_excel']) ?></p>
<p><strong>File ZIP Gambar:</strong> <?= htmlspecialchars($batch['original_filename_zip'] ?? '<em>Belum diunggah</em>') ?></p>
<p><strong>Status:</strong> <span class="status-badge status-<?= $batch['status'] ?>"><?= $batch['status'] ?></span></p>

<hr>

<!-- Bagian Upload ZIP -->
<div class="upload-zip-container">
    <h4>Tahap 2: Upload Gambar (Opsional)</h4>
    <p>Jika soal Anda memiliki gambar, kompres semua gambar ke dalam satu file .zip. Namai setiap file gambar sesuai format berikut, di mana **`[baris]`** adalah nomor baris di Excel:</p>
<ul>
    <li>Gambar Pertanyaan: `[baris]-q.png` (contoh: `2-q.png` untuk soal di baris 2)</li>
    <li>Gambar Penjelasan: `[baris]-e.png` (contoh: `3-e.jpg` untuk penjelasan di baris 3)</li>
    <li>Gambar Jawaban A: `[baris]-a.png` (contoh: `4-a.gif` untuk jawaban A di baris 4)</li>
    <li>Gambar Jawaban B: `[baris]-b.png` (contoh: `4-b.png` untuk jawaban B di baris 4)</li>
    <li>...dan seterusnya untuk `-c`, `-d`, `-e`.</li>
</ul>
    <form action="/admin/upload/zip/<?= $batch['batch_id'] ?>" method="POST" enctype="multipart/form-data"> <?= \App\Utils\CSRF::field() ?>
    <input type="file" name="zip_file" accept=".zip" required>
    <button type="submit" class="button button-secondary">Upload ZIP</button>
</form>
</div>

<hr>

<!-- Review Data -->
<h3>Review Data Staging</h3>
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Baris</th>
                <th>Subjek</th>
                <th>Pertanyaan</th>
                <th>Jawaban (JSON)</th>
                <th>Gambar Soal</th>
                <th>Gambar Penjelasan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stagedQuestions as $q): ?>
            <tr>
                <td><?= $q['row_number_in_excel'] ?></td>
                <td><?= htmlspecialchars($q['subject_name']) ?></td>
                <td><?= htmlspecialchars(substr($q['question_text'], 0, 50)) ?>...</td>
                <td><pre><code><?= htmlspecialchars(json_encode(json_decode($q['answers_data']), JSON_PRETTY_PRINT)) ?></code></pre></td>
                <td><?= htmlspecialchars($q['question_image_filename'] ?? '-') ?></td>
                <td><?= htmlspecialchars($q['explanation_image_filename'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<hr>

<!-- Tombol Finalisasi -->
<div class="finalize-container">
    <h4>Tahap 3: Finalisasi</h4>
    <p>Setelah Anda yakin semua data sudah benar dan gambar (jika ada) telah terunggah, klik tombol di bawah ini untuk memindahkan semua data ke database utama. <strong>Tindakan ini tidak dapat diurungkan.</strong></p>
    <form action="/admin/upload/finalize/<?= $batch['batch_id'] ?>" method="POST"> <?= \App\Utils\CSRF::field() ?>
        <button type="submit" class="button button-primary" onclick="return confirm('Anda yakin ingin memfinalisasi batch ini?')">Finalisasi dan Masukkan ke Sistem</button>
    </form>
</div>