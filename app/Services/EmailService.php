<?php
namespace App\Services;

// Import class PHPMailer ke dalam namespace global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        // Buat instance baru dari PHPMailer
        $this->mailer = new PHPMailer(true); // true untuk mengaktifkan exceptions

        try {
            // --- Konfigurasi Server SMTP ---
            // Ambil konfigurasi dari file .env
            
            // Aktifkan output debug SMTP yang detail (hanya untuk development)
            // 0 = off, 1 = client messages, 2 = client and server messages
            // Di produksi, ini HARUS di-set ke 0
            $this->mailer->SMTPDebug = ($_ENV['APP_ENV'] === 'local') ? 0 : 0;
            
            // Gunakan SMTP untuk mengirim email
            $this->mailer->isSMTP();
            
            // Host server SMTP (e.g., 'sandbox.smtp.mailtrap.io' atau 'smtp.gmail.com')
            $this->mailer->Host = $_ENV['MAIL_HOST'];
            
            // Aktifkan autentikasi SMTP
            $this->mailer->SMTPAuth = true;
            
            // Username SMTP (email Anda)
            $this->mailer->Username = $_ENV['MAIL_USERNAME'];
            
            // Password SMTP (password aplikasi, bukan password login email biasa)
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
            
            // Tipe enkripsi: 'tls' atau 'ssl'
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            
            // Port TCP untuk koneksi (biasanya 587 untuk TLS, 465 untuk SSL)
            $this->mailer->Port = (int)$_ENV['MAIL_PORT'];

            // --- Konfigurasi Pengirim & Format ---
            
            // Set pengirim email
            $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            
            // Set email sebagai HTML
            $this->mailer->isHTML(true);

        } catch (Exception $e) {
            // Jika konfigurasi awal gagal, catat error
            // Ini jarang terjadi kecuali .env tidak ada
            error_log("Gagal menginisialisasi PHPMailer: {$this->mailer->ErrorInfo}");
        }
    }

    /**
     * Method inti untuk mengirim email.
     *
     * @param string $toEmail Alamat email penerima
     * @param string $subject Judul email
     * @param string $body Konten HTML email
     * @return bool True jika berhasil, false jika gagal.
     */
    private function send(string $toEmail, string $subject, string $body): bool
    {
        try {
            // Tambahkan penerima
            $this->mailer->addAddress($toEmail);
            
            // Set judul dan isi email
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $this->wrapInTemplate($body); // Bungkus dengan template
            $this->mailer->AltBody = strip_tags($body); // Versi teks biasa untuk email client non-HTML

            // Kirim email
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            // Jika pengiriman gagal, catat error detailnya
            error_log("Pesan tidak dapat dikirim. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Mengirim email verifikasi registrasi.
     */
    public function sendVerificationEmail(string $toEmail, string $token): bool
    {
        $verificationLink = rtrim($_ENV['APP_URL'], '/') . '/verify-email/' . $token;
        $subject = 'Verifikasi Alamat Email Anda';
        $body = "
            <h2>Selamat Datang di {$_ENV['APP_NAME']}!</h2>
            <p>Terima kasih telah mendaftar. Silakan klik tautan di bawah ini untuk memverifikasi alamat email Anda:</p>
            <p><a href='{$verificationLink}' class='button'>Verifikasi Email Saya</a></p>
            <p>Jika Anda tidak merasa mendaftar, abaikan saja email ini.</p>
            <p>Terima kasih,<br>Tim {$_ENV['APP_NAME']}</p>
        ";
        return $this->send($toEmail, $subject, $body);
    }

    /**
     * Mengirim email untuk reset password.
     */
    public function sendPasswordResetEmail(string $toEmail, string $token): bool
    {
        $resetLink = rtrim($_ENV['APP_URL'], '/') . '/reset-password/' . $token;
        $subject = 'Atur Ulang Password Akun Anda';
        $body = "
            <h2>Lupa Password?</h2>
            <p>Kami menerima permintaan untuk mengatur ulang password akun Anda. Klik tautan di bawah ini untuk melanjutkan:</p>
            <p><a href='{$resetLink}' class='button'>Atur Ulang Password</a></p>
            <p>Tautan ini akan kedaluwarsa dalam 1 jam. Jika Anda tidak merasa meminta ini, abaikan saja email ini.</p>
            <p>Terima kasih,<br>Tim {$_ENV['APP_NAME']}</p>
        ";
        return $this->send($toEmail, $subject, $body);
    }

    /**
     * Membungkus konten email dengan template HTML yang bagus.
     */
    private function wrapInTemplate(string $content): string
    {
        // Template HTML sederhana untuk email
        return "
            <!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
                    .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
                    .email-header { text-align: center; border-bottom: 1px solid #eeeeee; padding-bottom: 20px; margin-bottom: 20px; }
                    .email-header h1 { margin: 0; color: #333; }
                    .email-body { line-height: 1.6; color: #555; }
                    .email-footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eeeeee; font-size: 0.9em; color: #999; }
                    .button { display: inline-block; background-color: #007bff; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>{$_ENV['APP_NAME']}</h1>
                    </div>
                    <div class='email-body'>
                        {$content}
                    </div>
                    <div class='email-footer'>
                        <p>© " . date('Y') . " {$_ENV['APP_NAME']}. Hak Cipta Dilindungi.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}