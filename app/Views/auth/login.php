<form action="/login" method="POST">
    <div class="form-group">
        <label for="username">Username atau Email</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="button button-primary">Login</button>
    <div class="form-footer">
        Belum punya akun? <a href="/register">Daftar di sini</a>
    </div>
</form>