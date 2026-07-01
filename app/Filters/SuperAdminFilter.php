<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SuperAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        if (($user['role'] ?? null) !== 'superadmin') {
            return redirect()->to('/')->with('error', 'Bu sayfaya erişim yetkiniz yok.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // noop (Boş bırakıyoruz)
    }
}

