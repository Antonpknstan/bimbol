<form action="/reset-password" method="POST">
    <?= \App\Utils\CSRF::field() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <p>Masukkan password baru Anda di bawah ini.</p>
    <div class="form-group">
        <label for="password">Password Baru</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="password_confirm">Konfirmasi Password Baru</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    <button type="submit" class="button button-primary">Atur Ulang Password</button>
</form>