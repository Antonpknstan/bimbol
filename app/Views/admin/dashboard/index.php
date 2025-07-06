<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Pengguna</h3>
        <p><?= $totalUsers ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Kursus</h3>
        <p><?= $totalCourses ?></p>
    </div>
    <div class="stat-card">
        <h3>Pembelian Sukses</h3>
        <p><?= $totalSuccessPurchases ?></p>
    </div>
</div>

<div class="dashboard-recent-users">
    <h3>Pengguna Terbaru</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($recentUsers as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
/* Tambahkan ke main.css atau di sini untuk testing */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    text-align: center;
}
.stat-card h3 {
    margin: 0 0 10px;
    font-size: 1.1em;
    color: #555;
}
.stat-card p {
    margin: 0;
    font-size: 2.5em;
    font-weight: bold;
    color: #007bff;
}
.dashboard-recent-users {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>