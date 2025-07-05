<?php
use Phinx\Seed\AbstractSeed;

class RbacSeeder extends AbstractSeed
{
    public function run()
    {
        // Hapus data lama untuk menghindari duplikasi saat seeding ulang
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute('TRUNCATE TABLE role_permissions');
        $this->execute('TRUNCATE TABLE permissions');
        $this->execute('TRUNCATE TABLE roles');
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        // 1. Definisikan semua Permissions yang ada di aplikasi
        $permissions = [
            ['name' => 'view_admin_dashboard', 'description' => 'Akses ke dashboard utama panel admin'],
            ['name' => 'manage_users', 'description' => 'Melihat, membuat, mengedit, dan menghapus pengguna'],
            ['name' => 'assign_roles', 'description' => 'Menetapkan atau mengubah peran pengguna'],
            ['name' => 'manage_courses', 'description' => 'Membuat, mengedit, dan menghapus kursus dan materinya'],
            ['name' => 'upload_questions_batch', 'description' => 'Mengunggah file Excel berisi pertanyaan'],
            ['name' => 'view_reports', 'description' => 'Melihat dan mengelola laporan masalah dari pengguna'],
        ];
        $this->table('permissions')->insert($permissions)->saveData();
        echo "Permissions seeded.\n";

        // 2. Definisikan Roles
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Memiliki semua akses tanpa batasan.'],
            ['name' => 'Uploader Soal', 'description' => 'Hanya bisa mengunggah pertanyaan batch.'],
            ['name' => 'Manajer Kursus', 'description' => 'Hanya bisa mengelola kursus dan materi pelajaran.'],
            // Role 'Student' sudah kita buat sebelumnya, biarkan saja.
        ];
        $this->table('roles')->insert($roles)->saveData();
        echo "Roles seeded.\n";

        // 3. Hubungkan Roles dengan Permissions
        // Ambil ID dari permissions dan roles yang baru dibuat
        $perms = $this->fetchAll('SELECT permission_id, name FROM permissions');
        $permissionMap = array_column($perms, 'permission_id', 'name');

        $dbRoles = $this->fetchAll('SELECT role_id, name FROM roles');
        $roleMap = array_column($dbRoles, 'role_id', 'name');

        // Definisikan hubungan
        $rolePermissions = [
            // Super Admin mendapatkan SEMUA permissions
            $roleMap['Super Admin'] => array_values($permissionMap),
            // Uploader Soal hanya mendapatkan satu permission
            $roleMap['Uploader Soal'] => [$permissionMap['upload_questions_batch']],
            // Manajer Kursus mendapatkan permission terkait
            $roleMap['Manajer Kursus'] => [$permissionMap['manage_courses']],
        ];

        $rolePermissionsData = [];
        foreach ($rolePermissions as $roleId => $permissionIds) {
            foreach ($permissionIds as $permId) {
                $rolePermissionsData[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permId
                ];
            }
        }
        $this->table('role_permissions')->insert($rolePermissionsData)->saveData();
        echo "Role-Permission links seeded.\n";
        
        // Perbarui user 'admin' menjadi 'Super Admin'
        $this->execute("UPDATE `roles` SET `name` = 'Super Admin' WHERE `name` = 'Admin'");
        echo "Role 'Admin' renamed to 'Super Admin'.\n";
    }
}