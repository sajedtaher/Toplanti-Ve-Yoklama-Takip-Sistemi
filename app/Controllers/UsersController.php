<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UsersController extends BaseController
{
    
   public function index()
{
    $user = session('user');
    if (!$user) {
        return redirect()->to('/login');
    }

    // 🔥 Rol belirleme
    $realRole = $user['role'] ?? 'member';
    $demoRole = session('demo_role');
    $roleUsed = $demoRole ?: $realRole;

    // 🔥 Kullanıcının gerçek birimi
    $unitId = $user['unit_id'] ?? null;

    $model = new \App\Models\UserModel();
    $model = $model
        ->select('users.*, units.name AS unit_name')
        ->join('units', 'units.id = users.unit_id', 'left');

    // 👉 SUPERADMIN
    if ($roleUsed === 'superadmin') {

        $selectedUnit = session('selected_unit_id');

        if ($selectedUnit) {
            $model->where('users.unit_id', $selectedUnit);
        }

        $unitModel = new \App\Models\UnitModel();
        $units = $unitModel->orderBy('name')->findAll();

        $users = $model->orderBy('users.id', 'DESC')->findAll();

        return view('users/index', [
            'title'  => 'Kişiler',
            'users'  => $users,
            'units'  => $units,
            'role'   => $roleUsed,
            'showUnitSelect' => true,
            'canEdit' => ($roleUsed !== 'member'),
        ]);
    }

    // 👉 MANAGER: kendi birimi
    if ($roleUsed === 'manager') {

        $users = $model
            ->where('users.unit_id', $unitId)
            ->orderBy('users.id', 'DESC')
            ->findAll();

        return view('users/index', [
            'title' => 'Kişiler',
            'users' => $users,
            'role'  => $roleUsed,
            'canEdit' => ($roleUsed !== 'member'),
        ]);
    }

    // 👉 MEMBER: kendi birimi
    $users = $model
        ->where('users.unit_id', $unitId)
        ->orderBy('users.id', 'DESC')
        ->findAll();

    return view('users/index', [
        'title' => 'Kişiler',
        'users' => $users,
        'role'  => $roleUsed,
        'canEdit' => false,
    ]);
}


 public function store()
{
    $model = new UserModel();
    $currentUser = session('user');

    $email = trim($this->request->getPost('email'));

    // Email kontrolü
    if ($model->where('email', $email)->first()) {
        return $this->response->setJSON([
            'success' => false,
            'errors'  => ['email' => 'Bu e-posta zaten kayıtlı.']
        ]);
    }

    // Temel bilgiler
    $data = [
        'name' => trim($this->request->getPost('name')),
        'email' => $email,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    // SUPERADMIN
    if ($currentUser['role'] === 'superadmin') {

        $data['role'] = $this->request->getPost('role');
        $data['unit_id'] = $this->request->getPost('unit_id');

        // 🚨 Unit seçili değilse kayıt yapılmasın
        if (empty($data['unit_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => ['unit_id' => 'Lütfen bir birim seçiniz.']
            ]);
        }

    } else {
        // MANAGER & MEMBER
        $data['role'] = 'member';
        $data['unit_id'] = $currentUser['unit_id'];
    }

    // Rastgele şifre
    $generatedPassword = bin2hex(random_bytes(4));
    $data['password'] = password_hash($generatedPassword, PASSWORD_DEFAULT);

    // Kayıt
    if ($model->insert($data)) {

        // Mail gönderimi
        $this->sendPasswordMail($data['email'], $generatedPassword);

        return $this->response->setJSON(['success' => true]);
    }

    return $this->response->setJSON([
        'success' => false,
        'errors'  => $model->errors(),
    ]);
}


    public function delete($id)
    {
        $model = new UserModel();
        $model->delete($id);
        return redirect()->to('/users');
    }

    public function edit($id)
    {
        $model = new UserModel();
        $data['user'] = $model->find($id);
        $data['title'] = 'Kullanıcı Düzenle';
        return view('users/edit', $data);
    }

    public function update($id)
{
    $model = new UserModel();

    $data = [
        'name'  => $this->request->getPost('name'),
        'email' => $this->request->getPost('email'),
        'role'  => $this->request->getPost('role'),
        'unit_id' => $this->request->getPost('unit_id'),
    ];

    if ($password = $this->request->getPost('password')) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($model->update($id, $data)) {
        return $this->response->setJSON(['success' => true]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'errors'  => $model->errors()
        ]);
    }
}

    private function sendPasswordMail($email, $password)
{
    $emailService = \Config\Services::email();

    $emailService->setTo($email);
    $emailService->setSubject('Toplantı Sistemi - Giriş Bilgileriniz');
    $emailService->setMessage("
        Merhaba,
        Sisteme kaydınız oluşturuldu.
        Giriş şifreniz: {$password}
        Lütfen giriş yaptıktan sonra şifrenizi değiştiriniz.
    ");

    if ($emailService->send()) {
        log_message('info', 'Kullanıcıya e-posta gönderildi: ' . $email);
    } else {
        log_message('error', 'E-posta gönderimi başarısız: ' . $email);
    }
}


}
