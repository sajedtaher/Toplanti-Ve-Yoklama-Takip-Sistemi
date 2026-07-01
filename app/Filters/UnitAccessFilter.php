<?php
namespace App\Filters;

use App\Models\MeetingModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UnitAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $segments = service('uri')->getSegments();
        $id = (int) ($segments[1] ?? 0);
        if (!$id) return redirect()->to('/meetings');

        $meeting = (new MeetingModel())->find($id);
        $user    = session('user');

        // 🟢 1) Kullanıcı yoksa engelle
        if (!$user) {
            return redirect()->to('/login');
        }

        // 🟢 2) Süperadmin HER TOPLANTIYA erişebilir → direkt izin ver
        if ($user['role'] === 'superadmin') {
            return; // filtre tamam
        }

        // 🟢 3) Üye veya yönetici → sadece kendi birimi
        if (!$meeting || (int)$meeting['unit_id'] !== (int)$user['unit_id']) {
            return redirect()->to('/meetings')->with('error', 'Erişim yok.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

