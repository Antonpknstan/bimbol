<?php
namespace App\Controllers;

use App\Models\User;
use App\Utils\Session;
use App\Services\EmailService;

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

        // Ubah cara memanggil create, dan ambil user ID yang baru
    $userId = $userModel->createAndGetId($_POST); // Kita perlu membuat method ini
    
    if ($userId) {
        // Buat token verifikasi
        $token = bin2hex(random_bytes(32));
        $userModel->setVerificationToken($userId, $token); // Buat method ini

        // Kirim email verifikasi
        $emailService = new EmailService();
        $emailService->sendVerificationEmail($_POST['email'], $token); // Buat method ini di service

        // Redirect ke halaman pemberitahuan
        $this->render('auth/please_verify', [
            'title' => 'Verifikasi Email Anda'
        ], 'layout/auth');
    } else {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Gagal mendaftarkan akun.']);
        $this->redirect('/register');
    }
    }

    public function verifyEmail(string $token)
{
    $userModel = new User();
    $success = $userModel->verifyEmailByToken($token); // Buat method ini

    if ($success) {
        Session::set('flash_message', ['type' => 'success', 'message' => 'Email berhasil diverifikasi! Silakan login.']);
    } else {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Tautan verifikasi tidak valid atau telah kedaluwarsa.']);
    }
    $this->redirect('/login');
}

    public function login()
    {
        $userModel = new User();
    $user = $userModel->findByUsernameOrEmail($_POST['username']);

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        // PENGECEKAN BARU: Pastikan email sudah diverifikasi
        if (empty($user['email_verified_at'])) {
            Session::set('flash_message', ['type' => 'error', 'message' => 'Akun Anda belum diverifikasi. Silakan cek email Anda.']);
            $this->redirect('/login');
            return;
        }
            Session::set('user', [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'full_name' => $user['full_name']
            ]);
            $this->redirect('/dashboard');
} else {
    Session::set('flash_message', ['type' => 'error', 'message' => 'Username atau password salah.']);
    $this->redirect('/login');
}
    }

    public function showForgotPasswordForm()
{
    $this->render('auth/forgot_password', ['title' => 'Lupa Password'], 'layout/auth');
}

public function sendResetLink()
{
    $email = $_POST['email'] ?? null;
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Format email tidak valid.']);
        $this->redirect('/forgot-password');
        return;
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);
    
    if ($user) {
        // Buat token dan kirim email
        $token = bin2hex(random_bytes(32));
        $userModel->setPasswordResetToken($user['user_id'], $token);
        
        $emailService = new EmailService();
        $emailService->sendPasswordResetEmail($email, $token);
    }

    // Selalu tampilkan pesan sukses untuk mencegah user enumeration
    Session::set('flash_message', ['type' => 'success', 'message' => 'Jika email Anda terdaftar, Anda akan menerima tautan untuk mengatur ulang password.']);
    $this->redirect('/forgot-password');
}

public function showResetPasswordForm(string $token)
{
    $userModel = new User();
    $user = $userModel->findByValidResetToken($token);

    if (!$user) {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Tautan atur ulang password tidak valid atau telah kedaluwarsa.']);
        $this->redirect('/forgot-password');
        return;
    }

    $this->render('auth/reset_password', [
        'title' => 'Atur Ulang Password',
        'token' => $token
    ], 'layout/auth');
}

public function processPasswordReset()
{
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (empty($token) || empty($password) || $password !== $passwordConfirm) {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Data tidak valid atau password tidak cocok.']);
        $this->redirect('/reset-password/' . $token);
        return;
    }

    $userModel = new User();
    $user = $userModel->findByValidResetToken($token);

    if (!$user) {
        Session::set('flash_message', ['type' => 'error', 'message' => 'Tautan atur ulang password tidak valid atau telah kedaluwarsa.']);
        $this->redirect('/forgot-password');
        return;
    }
    
    $userModel->resetPassword($user['user_id'], $password);
    
    Session::set('flash_message', ['type' => 'success', 'message' => 'Password Anda telah berhasil diatur ulang. Silakan login.']);
    $this->redirect('/login');
}
    
    public function logout()
    {
        Session::destroy();
        $this->redirect('/');
    }
}