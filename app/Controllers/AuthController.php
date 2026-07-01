<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attempt(): RedirectResponse
    {
        $email = $this->request->getPost('email');
        $pass  = $this->request->getPost('password');

        $user = (new UserModel())->where('email', $email)->first();
            if ($user && (password_verify($pass, $user['password']) || $user['password'] === $pass)) {
                session()->set('user', [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'unit_id' => $user['unit_id'],
                    'role' => $user['role'],
            ]);
                return redirect()->to('/meetings');
            }
        return redirect()->back()->with('error', 'Geçersiz bilgiler');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
