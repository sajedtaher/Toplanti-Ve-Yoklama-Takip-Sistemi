<?php
///bu kod demo linkine girince kullanıcıyı otomatik içeri alıyor.
namespace App\Controllers;

use App\Models\UnitModel;
use CodeIgniter\HTTP\RedirectResponse;

class DemoController extends BaseController
{
    public function login(): RedirectResponse
{
    // Eski oturum seçimlerini temizle
    session()->remove('user');
    session()->remove('demo_role');
    session()->remove('is_demo');
    session()->remove('selected_unit_id');

    $unitModel = new UnitModel();

    $computerUnit = $unitModel
        ->where('name', 'Bilgisayar Mühendisliği')
        ->first();

    $unitId = $computerUnit['id'] ?? null;

    session()->set('user', [
        'id'      => 0,
        'name'    => 'Ahmet Yılmaz', //Demo Kullanıcı yerine DR. Ahmet Yılmaz yazdım
        'email'   => 'demo@example.com',
        'unit_id' => $unitId,
        'role'    => 'superadmin', 
    ]);

    session()->set('is_demo', true);

    // Rol kutusu varsayılan olarak Sistem Yöneticisi olsun
    session()->set('demo_role', 'superadmin');

    // Birim seçimi varsayılan olarak Tüm Birimler olsun
    session()->remove('selected_unit_id');

    return redirect()->to('/meetings');
}

    public function changeRole(): RedirectResponse
{
    $allowedRoles = ['superadmin', 'manager', 'member'];

    $role = $this->request->getPost('role');

    if (! in_array($role, $allowedRoles, true)) {
        return redirect()->back();
    }

    $user = session('user');

    if (! $user) {
        return redirect()->to('/login');
    }

    /*
     * ÖNEMLİ:
     * Burada user['role'] veya user['unit_id'] değiştirmiyoruz.
     * Çünkü bu kutu sadece demo rolü değiştirmeli.
     * Kullanıcının gerçek birimi aynı kalmalı.
     */
    session()->set('demo_role', $role);

    /*
     * Superadmin moduna geçince, tüm birimler görünsün diye
     * seçili birim filtresini temizliyoruz.
     */
    if ($role === 'superadmin') {
        session()->remove('selected_unit_id');
    }

    return redirect()->to('/meetings');
}
}