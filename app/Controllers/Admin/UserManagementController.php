<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Role;

class UserManagementController extends BaseController
{
    public function listUsers()
    {
        $userModel = new User();
        $users = $userModel->findAllWithRoles(); // Buat method ini

        $this->render('admin/users/list', [
            'title' => 'Manajemen Pengguna',
            'users' => $users
        ], 'layout/admin');
    }

    public function editUserRoles(int $id) // PERUBAHAN: dari $userId menjadi $id
    {
        $userModel = new User();
        $roleModel = new Role();

        $user = $userModel->findById($id); // Gunakan $id
        if (!$user) {
            // Handle user not found (optional: redirect to 404 or user list)
            $this->redirect('/admin/users'); // Redirect ke daftar user jika user tidak ditemukan
            return;
        }
        $allRoles = $roleModel->findAll();
        $userRoleIds = $userModel->getRoleIds($id); // Gunakan $id

        $this->render('admin/users/edit_roles', [
            'title' => 'Edit Peran: ' . htmlspecialchars($user['full_name']),
            'user' => $user,
            'allRoles' => $allRoles,
            'userRoleIds' => $userRoleIds
        ], 'layout/admin');
    }

    /**
     * Memperbarui peran pengguna berdasarkan kiriman formulir.
     * @param int $id ID pengguna dari URL
     */
    public function updateUserRoles(int $id) // PERUBAHAN: dari $userId menjadi $id
    {
        // Pastikan ini adalah request POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users'); // Atau tampilkan error method not allowed
            return;
        }

        $assignedRoles = $_POST['roles'] ?? []; // Ini akan menjadi array ID peran yang dicentang
        
        $userModel = new User();
        $success = $userModel->syncRoles($id, $assignedRoles); // Gunakan $id

        if ($success) {
            // Opsional: set pesan sukses ke sesi
            // \App\Utils\Session::set('flash_message', ['type' => 'success', 'message' => 'Peran pengguna berhasil diperbarui.']);
        } else {
            // Opsional: set pesan error ke sesi
            // \App\Utils\Session::set('flash_message', ['type' => 'error', 'message' => 'Gagal memperbarui peran pengguna.']);
        }
        
        $this->redirect('/admin/users');
    }
}