<?php
namespace App\Controllers;

use App\Models\User;
use App\Utils\Session;

class AuthController extends BaseController
{
    public function showLoginForm()
    {
        $this->render('auth/login', ['title' => 'Login'], 'layout/auth');
    }

    public function showRegisterForm()
    {
        $this->render('auth/register', ['title' => 'Daftar Akun Baru'], 'layout/auth');
    }

    public function register()
    {
        // Validasi sederhana (di dunia nyata, gunakan library validasi)
        if (empty($_POST['full_name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            // Handle error: field tidak boleh kosong
            $this->redirect('/register');
            return;
        }

        $userModel = new User();
        
        // Cek apakah username atau email sudah ada
        if ($userModel->findByUsernameOrEmail($_POST['username']) || $userModel->findByUsernameOrEmail($_POST['email'])) {
             // Handle error: username/email sudah terdaftar
            $this->redirect('/register');
            return;
        }

        $userData = [
        'full_name' => $_POST['full_name'],
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'phone_number' => $_POST['phone_number'] ?? null // Ambil phone_number
    ];

        // Buat user baru
        $userModel->create($userData);
        
        // Redirect ke halaman login dengan pesan sukses (akan kita buat nanti)
        $this->redirect('/login');
    }

    public function login()
    {
        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($_POST['username']);

        // Jika user ditemukan dan password cocok
        if ($user && password_verify($_POST['password'], $user['password_hash'])) {
            Session::set('user', [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'full_name' => $user['full_name']
            ]);
            $this->redirect('/dashboard');
        } else {
            // Handle error: login gagal
            $this->redirect('/login');
        }
    }
    
    public function logout()
    {
        Session::destroy();
        $this->redirect('/');
    }
}