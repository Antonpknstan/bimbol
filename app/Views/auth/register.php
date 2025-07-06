<form action="/register" method="POST"> <?= \App\Utils\CSRF::field() ?>
    <div class="form-group">
        <label for="full_name">Nama Lengkap</label>
        <input type="text" id="full_name" name="full_name" required>
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="phone_number">Nomor Telepon (opsional)</label>
        <input type="tel" id="phone_number" name="phone_number" placeholder="+628123456789">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="button button-primary">Daftar</button>
    <div class="form-footer">
        Sudah punya akun? <a href="/login">Login di sini</a>
    </div>
</form>