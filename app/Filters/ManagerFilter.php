<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ManagerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        $role = $user['role'] ?? null;
        // Artık manager VEYA superadmin yetkili
        if (!in_array($role, ['manager', 'superadmin'], true)) {
            return redirect()->to('/meetings')->with('error', 'Yetkiniz yok.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Boş bırakabiliriz
    }
}
