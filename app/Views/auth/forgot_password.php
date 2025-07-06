<form action="/forgot-password" method="POST">
    <?= \App\Utils\CSRF::field() ?>
    <p>Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.</p>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <button type="submit" class="button button-primary">Kirim Tautan Reset</button>
    <div class="form-footer">
        Ingat password Anda? <a href="/login">Login</a>
    </div>
</form>