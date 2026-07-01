<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UnitModel;
use App\Models\UserModel;

class UnitsController extends BaseController
{
    protected $unitModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
        helper(['form']);
    }

    // Figma listesi
public function index()
{
    $user = session('user');
    if (!$user) {
        return redirect()->to('/login');
    }

    // Rol seçimi
    $realRole = $user['role'] ?? 'member';
    $demoRole = session('demo_role');
    $roleUsed = $demoRole ?: $realRole;

    // ❌ SUPERADMIN değilse erişim yok
    if ($roleUsed !== 'superadmin') {
        return redirect()->to('/meetings');
    }

    $unitModel = $this->unitModel;
    $userModel = new UserModel();

    $units = $unitModel
        ->select('units.*, users.name AS manager_name')
        ->join('users', 'users.id = units.manager_id', 'left')
        ->orderBy('units.id', 'DESC')
        ->findAll();

    $managers = $userModel
        ->where('role', 'manager')
        ->orderBy('name')
        ->findAll();

    return view('units/index', [
        'units'    => $units,
        'managers' => $managers,
        'role'     => $roleUsed
    ]);
}




    // Yeni Birim Ekle
    public function store()
{
    $validation = \Config\Services::validation();

    $rules = [
        'name' => 'required|min_length[2]|max_length[120]',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Birim adı 2-120 karakter olmalıdır.'
        ]);
    }

    $saved = $this->unitModel->insert([
        'name'        => $this->request->getPost('name'),
        'manager_id'  => $this->request->getPost('manager_id') ?: null,
    ]);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Yeni birim başarıyla eklendi.',
        'id'      => $saved
    ]);
}


    // Düzenleme Modalı (AJAX)
    public function edit($id)
    {
        $unit = $this->unitModel->find($id);
        if (!$unit) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $managers = (new UserModel())
            ->where('role', 'manager')
            ->orderBy('name')
            ->findAll();

        return view('units/modal_edit', [
            'unit'     => $unit,
            'managers' => $managers
        ]);
    }

    // Güncelle
    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[120]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Birim adı 2-120 karakter olmalıdır.');
        }

        $this->unitModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'manager_id'  => $this->request->getPost('manager_id') ?: null,
        ]);

        return redirect()->to('/units')->with('success', 'Birim başarıyla güncellendi.');
    }

    // Soft delete
    public function delete($id)
    {
        $this->unitModel->delete($id);
        return redirect()->to('/units')->with('success', 'Birim silindi.');
    }

public function select()
{
    // Formdan gelen değerler
    $unitId   = $this->request->getPost('unit_id');
    $demoRole = $this->request->getPost('demo_role');

    // 1) Birim seçimi
    if ($unitId !== null) {
        session()->set('selected_unit_id', $unitId);
    }

    // 2) Demo rol seçimi
    if ($demoRole !== null) {

        // Boş gönderilirse demo role sıfırlanır
        if ($demoRole === '') {
            session()->remove('demo_role');
        } else {
            session()->set('demo_role', $demoRole);
        }
    }

    // Sayfayı yenile
    return redirect()->back();
}


}
