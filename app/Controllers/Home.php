<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function index1()
{
    echo "HOME OK";

    $user = session('user');

    // Eski birim seçimi temizlensin: Tüm Birimler gelsin
    session()->remove('selected_unit_id');

    // Rol seçimi varsayılan Sistem Yöneticisi olsun
    session()->set('demo_role', 'superadmin');

    // Kullanıcı giriş yapmışsa direkt toplantılara gönder
    if ($user) {
        return redirect()->to('/meetings');
    }

    // Kullanıcı giriş yapmamışsa demo girişe gönder
    return redirect()->to('/demo');
}

}
// yeni home



