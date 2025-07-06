<?php 
// Cek jika flash message ada DAN merupakan array yang memiliki kunci 'message'
if (isset($flash_message) && is_array($flash_message) && !empty($flash_message['message'])):
    // Ambil tipe dengan nilai default 'info' jika tidak ada
    $messageType = htmlspecialchars($flash_message['type'] ?? 'info');
    // Ambil pesan
    $messageText = htmlspecialchars($flash_message['message']);
?>
    <div class="flash-message <?= $messageType ?>">
        <?= $messageText ?>
    </div>
<?php endif; ?>

<p>Gunakan formulir ini untuk mengunggah pertanyaan dalam jumlah besar menggunakan file Excel.</p>
<p>Pastikan format file Anda sesuai dengan template. Unduh template di sini: 
    <a href="/assets/templates/Template_Upload_Pertanyaan.xlsx" download="Template_Upload_Pertanyaan.xlsx" class="button button-sm button-info">Unduh Template Excel</a>
</p>
<br>

<div class="form-container" style="max-width: 600px; margin: 0 auto;">
    <!-- PASTIKAN BARIS INI BENAR -->
    <form action="/admin/upload/excel" method="POST" enctype="multipart/form-data"> <?= \App\Utils\CSRF::field() ?>
        <div class="form-group">
            <label for="excel_file">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls" required>
        </div>
        <button type="submit" class="button button-primary">Upload dan Lanjutkan ke Review</button>
    </form>
</div>

<div class="format-info">
    <h3>Format Excel yang Diharapkan:</h3>
    <p>Baris pertama harus berisi header. Data pertanyaan dimulai dari baris kedua.</p>
    <p><strong>Pastikan urutan kolom sesuai di bawah ini:</strong></p>
    <table class="excel-format-table">
    <table class="excel-format-table">
    <thead>
        <tr>
            <th>Kolom</th>
            <th>Header</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>A</td><td>Subject Name</td><td>Nama Subjek (e.g., Matematika Dasar)</td></tr>
        <tr><td>B</td><td>Question Text</td><td>Teks Pertanyaan</td></tr>
        <tr><td>C</td><td>Answer Option A</td><td>Teks Pilihan Jawaban A</td></tr>
        <tr><td>D</td><td>Answer Option B</td><td>Teks Pilihan Jawaban B</td></tr>
        <tr><td>E</td><td>Answer Option C</td><td>Teks Pilihan Jawaban C (Opsional)</td></tr>
        <tr><td>F</td><td>Answer Option D</td><td>Teks Pilihan Jawaban D (Opsional)</td></tr>
        <tr><td>G</td><td>Answer Option E</td><td>Teks Pilihan Jawaban E (Opsional)</td></tr>
        <tr><td>H</td><td><strong>Correct Answer Column</strong></td><td><strong>Huruf kolom jawaban benar (A, B, C, D, atau E)</strong></td></tr>
        <tr><td>I</td><td><strong>Score for Answer A</strong></td><td><strong>Opsional: Nilai skor untuk Pilihan A</strong></td></tr>
        <tr><td>J</td><td><strong>Score for Answer B</strong></td><td><strong>Opsional: Nilai skor untuk Pilihan B</strong></td></tr>
        <tr><td>K</td><td><strong>Score for Answer C</strong></td><td><strong>Opsional: Nilai skor untuk Pilihan C</strong></td></tr>
        <tr><td>L</td><td><strong>Score for Answer D</strong></td><td><strong>Opsional: Nilai skor untuk Pilihan D</strong></td></tr>
        <tr><td>M</td><td><strong>Score for Answer E</strong></td><td><strong>Opsional: Nilai skor untuk Pilihan E</strong></td></tr>
        <tr><td>N</td><td>Explanation Text</td><td>Teks Penjelasan</td></tr>
    </tbody>
</table>
<p class="note"><strong>Penting:</strong> Gambar untuk soal, penjelasan, dan jawaban akan dideteksi secara otomatis dari file .zip yang diunggah pada tahap review. Cukup namai file gambar sesuai format yang ditentukan.</p>
</div>

<style>
/* --- Styling untuk Halaman Upload --- */

/* Flash Message */
.flash-message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid transparent;
    font-weight: bold;
}
.flash-message.success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.flash-message.error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
.flash-message.info { /* Tambahan jika ada pesan info */
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

/* Form Container */
.form-container {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

/* File Input Styling (bisa lebih kompleks dengan JS untuk custom look) */
.form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f8f8f8;
}
.form-group input[type="file"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Format Info Section */
.format-info {
    background-color: #f0f8ff;
    border-left: 5px solid #007bff;
    padding: 20px;
    border-radius: 8px;
}
.format-info h3 {
    margin-top: 0;
    color: #007bff;
}
.format-info ul {
    list-style: disc;
    margin-left: 20px;
    padding-left: 0;
    line-height: 1.6;
}
.format-info ul li {
    margin-bottom: 5px;
}
.format-info pre {
    background-color: #e9ecef;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.9em;
    white-space: pre-wrap; /* Memastikan baris baru di JSON terlihat */
    word-wrap: break-word; /* Memecah kata panjang */
}
.format-info h4 {
    margin-top: 20px;
    color: #333;
}
.format-info .note {
    font-size: 0.9em;
    color: #777;
    margin-top: 15px;
}

/* Utility button small */
.button.button-sm {
    padding: 5px 10px;
    font-size: 0.9em;
    display: inline-block; /* Agar bisa berdampingan dengan teks */
}
.button.button-info {
    background-color: #17a2b8;
    color: #fff;
}
.button.button-info:hover {
    background-color: #138496;
}
</style>