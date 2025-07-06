<?php
namespace App\Controllers;

use App\Models\Package;
use App\Models\Purchase;
use App\Utils\Session;
use App\Models\Notification;

class PurchaseController extends BaseController
{
    public function buy(int $id)
    {
        // Middleware akan menangani jika user belum login, tapi ini double check
        if (!Session::has('user')) {
            $this->redirect('/login');
            return;
        }

        $packageModel = new Package();
        $package = $packageModel->findById($id);

        if (!$package) {
            // Handle jika paket tidak ditemukan atau tidak aktif
            $this->redirect('/packages');
            return;
        }
        
        $purchaseModel = new Purchase();
        $purchaseData = [
            'transaction_id' => 'BIMBEL-' . time() . '-' . uniqid(),
            'user_id' => Session::get('user')['id'],
            'package_id' => $package['package_id'],
            'price_at_purchase' => $package['price']
        ];

$purchaseId = $purchaseModel->create($purchaseData);

// --- SIMULASI PAYMENT GATEWAY ---
// Di dunia nyata, di sini Anda akan memanggil API payment gateway
// dan redirect ke URL yang mereka berikan.
// Kita akan langsung redirect ke halaman sukses simulasi.

// Mensimulasikan callback dari payment gateway yang terjadi beberapa detik kemudian
$this->simulatePaymentSuccess($purchaseData['transaction_id'], $package['duration_days']);

$notificationModel = new Notification();
$notificationModel->create(
    Session::get('user')['id'],
    'Pembelian Berhasil!',
    'Paket "' . $package['name'] . '" Anda sekarang aktif. Selamat belajar!',
    'purchase',
    $purchaseId // Ambil dari $purchaseModel->create()
);

$this->render('purchases/success', ['title' => 'Pembelian Berhasil']);
    }
    
    /**
     * Ini adalah FUNGSI SIMULASI.
     * Seharusnya ini adalah endpoint webhook terpisah yang dipanggil oleh payment gateway.
     */
    private function simulatePaymentSuccess($transactionId, $durationDays)
    {
        $purchaseModel = new Purchase();
        $purchaseModel->updateStatusToSuccess($transactionId, $durationDays);
    }
    

    public function history()
    {
        $purchaseModel = new Purchase();
        $purchases = $purchaseModel->findByUser(Session::get('user')['id']);
        
        $this->render('purchases/history', [
            'title' => 'Riwayat Pembelian',
            'purchases' => $purchases
        ]);
    }
}