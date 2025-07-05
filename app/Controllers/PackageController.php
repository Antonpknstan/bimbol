<?php
namespace App\Controllers;

use App\Models\Package;

class PackageController extends BaseController
{
    public function index()
    {
        $packageModel = new Package();
        $packages = $packageModel->findAllActive();

        $this->render('packages/index', [
            'title' => 'Pilih Paket Belajar',
            'packages' => $packages
        ]);
    }
}