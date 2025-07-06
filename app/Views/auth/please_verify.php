<?php
// File ini akan secara otomatis dibungkus oleh layout/auth.php oleh BaseController,
// jadi kita tidak perlu tag <html>, <head>, atau <body> di sini.
?>

<div class="verification-notice">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>

    <h3>Satu Langkah Lagi!</h3>
    <p>
        Kami telah mengirimkan tautan verifikasi ke alamat email Anda.
        <br>
        Silakan periksa kotak masuk (dan folder spam) Anda untuk mengaktifkan akun.
    </p>
    <div class="form-footer">
        Sudah verifikasi? <a href="/login">Lanjutkan ke Login</a>
    </div>
</div>


<style>
/* Tambahkan ke main.css atau letakkan di sini untuk pengujian cepat */
.verification-notice {
    text-align: center;
    padding: 20px;
}
.verification-notice .feather-mail {
    color: #007bff;
    margin-bottom: 20px;
}
.verification-notice h3 {
    margin-bottom: 15px;
    font-size: 1.5em;
}
.verification-notice p {
    line-height: 1.6;
    color: #555;
}
</style>