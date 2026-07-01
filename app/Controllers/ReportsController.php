<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MeetingModel;
use App\Models\UserModel;
use App\Models\MeetingParticipantModel;
use App\Models\UnitModel;

class ReportsController extends BaseController
{
    public function attendance()
{
    $session = session();
    $user = $session->get('user');

    if (!$user) {
        return redirect()->to('/login');
    }

    // Demo modda gerçek rol yerine seçilen demo rolünü kullan
    $role = session('is_demo')
        ? (session('demo_role') ?? ($user['role'] ?? 'member'))
        : ($user['role'] ?? 'member');

    // Sistem Yöneticisi ise tüm birimler / seçili birim mantığı çalışsın
    if ($role === 'superadmin') {
        return $this->attendanceForSuperAdmin();
    }

    // Birim Yöneticisi veya Üye ise kesinlikle kendi birimine göre rapor gelsin
    $user['role'] = $role;

    if (empty($user['unit_id'])) {
        $unitModel = new \App\Models\UnitModel();
        $firstUnit = $unitModel->orderBy('id', 'ASC')->first();
        $user['unit_id'] = $firstUnit['id'] ?? null;
    }

    session()->set('selected_unit_id', $user['unit_id']);

    return $this->attendanceForUnit($user);
}
    /* =====================================================
       SUPERADMIN
       ===================================================== */

    private function attendanceForSuperAdmin()
    {
        $session = session();
        $selectedUnit = $session->get('selected_unit_id');

        if (empty($selectedUnit)) {
            return $this->attendanceSummary();
        }

        return $this->attendanceDetail($selectedUnit);
    }

    private function attendanceSummary()
{
    $meetingModel = new MeetingModel();
    $unitModel    = new UnitModel();
    $userModel    = new UserModel();
    $mpModel      = new MeetingParticipantModel();

    $units = $unitModel->orderBy('name')->findAll();
    $unitSummary = [];

    foreach ($units as $unit) {

        // Bu birime ait son 10 toplantı alınır
        $meetings = $meetingModel
            ->where('unit_id', $unit['id'])
            ->orderBy('start_at', 'DESC')
            ->limit(10)
            ->findAll();

        $meetingIds = array_column($meetings, 'id');

        // Bu birime ait kullanıcılar alınır
        $users = $userModel
            ->where('unit_id', $unit['id'])
            ->findAll();

        if (empty($users)) {
            continue;
        }

        $userIds = array_column($users, 'id');

        $rows = [];

        // Eğer bu birimin toplantısı varsa yoklama kayıtları alınır
        if (!empty($meetingIds) && !empty($userIds)) {
            $rows = $mpModel
                ->whereIn('meeting_id', $meetingIds)
                ->whereIn('user_id', $userIds)
                ->findAll();
        }

        $came = 0;

        foreach ($rows as $r) {
            if ($r['status'] === 'geldi') {
                $came++;
            }
        }

        $total = count($users) * count($meetings);
        $avg = $total > 0 ? round(($came / $total) * 100) : 0;

        $unitSummary[] = [
            'unit_id'        => $unit['id'],
            'unit_name'      => $unit['name'],
            'meeting_count'  => count($meetings),
            'avg_attendance' => $avg,
            'last_meeting'   => $meetings[0]['start_at'] ?? null
        ];
    }

    return view('reports/attendance', [
        'mode'        => 'summary',
        'unitSummary' => $unitSummary,
        'role'        => 'superadmin'
    ]);
}

    private function attendanceDetail($unitId)
    {
        return $this->buildDetailReport($unitId, 'superadmin');
    }

    /* =====================================================
       MANAGER
       ===================================================== */

    private function attendanceForUnit($user)
    {
        return $this->buildDetailReport($user['unit_id'], $user['role']);
    }

    /* =====================================================
       ORTAK DETAY RAPORU
       ===================================================== */

    private function buildDetailReport($unitId, $role)
{
    $meetingModel = new MeetingModel();
    $userModel    = new UserModel();
    $mpModel      = new MeetingParticipantModel();

    // Sadece seçilen birime ait son 10 toplantı alınır
    $meetings = $meetingModel
        ->where('unit_id', $unitId)
        ->orderBy('start_at', 'DESC')
        ->limit(10)
        ->findAll();

    $meetingIds = array_column($meetings, 'id');

    // Sadece seçilen birime ait kullanıcılar alınır
    $users = $userModel
        ->where('unit_id', $unitId)
        ->orderBy('name')
        ->findAll();

    $userIds = array_column($users, 'id');

    $rows = [];

    // Boş whereIn hatası olmaması için kontrol
    if (!empty($meetingIds) && !empty($userIds)) {
        $rows = $mpModel
            ->whereIn('meeting_id', $meetingIds)
            ->whereIn('user_id', $userIds)
            ->findAll();
    }

    $table = [];

    foreach ($rows as $r) {
        $table[$r['user_id']][$r['meeting_id']] = $r['status'];
    }

    $reportRows = [];

$totalGeldi   = 0;
$totalGelmedi = 0;
$totalIzinli  = 0;

foreach ($users as $u) {
    $present = 0;
    $statuses = [];

    foreach ($meetings as $m) {
        $status = $table[$u['id']][$m['id']] ?? null;
        $statuses[$m['id']] = $status;

        if ($status === 'geldi') {
            $present++;
            $totalGeldi++;
        } elseif ($status === 'gelmedi') {
            $totalGelmedi++;
        } elseif ($status === 'izinli') {
            $totalIzinli++;
        }
    }

    $reportRows[] = [
        'name'          => $u['name'],
        'statuses'      => $statuses,
        'present_count' => $present,
        'meeting_count' => count($meetings),
        'percent'       => count($meetings) > 0
            ? round(($present / count($meetings)) * 100)
            : 0
    ];
}

$totalSlots = count($users) * count($meetings);

$attendanceStats = [
    'geldi_count'   => $totalGeldi,
    'gelmedi_count' => $totalGelmedi,
    'izinli_count'  => $totalIzinli,

    'geldi_percent' => $totalSlots > 0
        ? round(($totalGeldi / $totalSlots) * 100)
        : 0,

    'gelmedi_percent' => $totalSlots > 0
        ? round(($totalGelmedi / $totalSlots) * 100)
        : 0,

    'izinli_percent' => $totalSlots > 0
        ? round(($totalIzinli / $totalSlots) * 100)
        : 0,
];

    return view('reports/attendance', [
    'mode'            => 'detail',
    'meetings'        => $meetings,
    'reportRows'      => $reportRows,
    'attendanceStats' => $attendanceStats,
    'role'            => $role
]);
}

    /* =====================================================
       UNIT SEÇME
       ===================================================== */

    public function selectUnit($unitId)
    {
        session()->set('selected_unit_id', $unitId);
        return redirect()->to('/reports/attendance');
    }
}
