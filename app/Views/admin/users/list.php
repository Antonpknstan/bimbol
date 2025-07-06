<p>Halaman ini menampilkan semua pengguna yang terdaftar di sistem. Anda dapat menetapkan atau mengubah peran mereka.</p>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Email</th>
                <th>Peran (Roles)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada pengguna yang ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php if (!empty($user['roles'])): ?>
                                <?php 
                                // Ubah string "Role A, Role B" menjadi array, lalu buat badge untuk setiap role
                                $roles = explode(', ', $user['roles']);
                                foreach ($roles as $roleName): 
                                ?>
                                    <span class="role-badge"><?= htmlspecialchars($roleName) ?></span>
                                <?php 
                                endforeach;
                                ?>
                            <?php else: ?>
                                <span class="role-badge-none">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php // Super Admin tidak bisa diedit perannya sendiri untuk keamanan ?>
                            <?php if ($user['username'] !== 'admin' && \App\Utils\Session::get('user')['username'] !== $user['username']): ?>
                                <a href="/admin/users/<?= $user['user_id'] ?>/roles" class="button button-sm button-secondary">Edit Peran</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
/* Tambahkan style ini ke file CSS utama Anda atau letakkan di sini untuk pengujian cepat */
.role-badge, .role-badge-none {
    display: inline-block;
    padding: 4px 10px;
    font-size: 0.8em;
    font-weight: bold;
    border-radius: 12px;
    margin-right: 5px;
    margin-bottom: 5px;
}
.role-badge {
    background-color: #007bff;
    color: #fff;
}
.role-badge-none {
    background-color: #6c757d;
    color: #fff;
}
</style>