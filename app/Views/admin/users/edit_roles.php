<p>Pilih peran yang ingin Anda tetapkan untuk pengguna <strong><?= htmlspecialchars($user['full_name']) ?></strong> (@<?= htmlspecialchars($user['username']) ?>).</p>

<div class="form-container" style="max-width: 600px;">
    <form action="/admin/users/<?= $user['user_id'] ?>/roles" method="POST"> <?= \App\Utils\CSRF::field() ?>
        <div class="form-group">
            <label>Peran yang Tersedia:</label>
            
            <?php if (empty($allRoles)): ?>
                <p>Tidak ada peran yang dapat ditetapkan.</p>
            <?php else: ?>
                <?php foreach ($allRoles as $role): ?>
                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            name="roles[]" 
                            id="role_<?= $role['role_id'] ?>" 
                            value="<?= $role['role_id'] ?>"
                            <?php 
                                // Cek apakah ID peran ini ada di dalam array peran yang sudah dimiliki pengguna
                                if (in_array($role['role_id'], $userRoleIds)) {
                                    echo 'checked';
                                }
                            ?>
                        >
                        <label for="role_<?= $role['role_id'] ?>">
                            <strong><?= htmlspecialchars($role['name']) ?></strong>
                            <small><?= htmlspecialchars($role['description']) ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="button button-primary">Simpan Perubahan</button>
            <a href="/admin/users" class="button button-secondary">Batal</a>
        </div>
    </form>
</div>


<style>
/* Tambahkan style ini ke file CSS utama Anda atau letakkan di sini untuk pengujian cepat */
.checkbox-group {
    display: flex;
    align-items: center;
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
}
.checkbox-group input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 15px;
}
.checkbox-group label {
    display: flex;
    flex-direction: column;
    cursor: pointer;
    margin: 0;
}
.checkbox-group label strong {
    font-size: 1.1em;
}
.checkbox-group label small {
    color: #666;
}
.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}
.form-actions .button {
    flex-grow: 1;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    padding: 12px;
}
</style>