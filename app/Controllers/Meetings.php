<?php
namespace App\Controllers;

use App\Models\{MeetingModel, UserModel, MeetingParticipantModel, AgendaItemModel, DecisionModel, UnitModel};
use CodeIgniter\I18n\Time;

class Meetings extends BaseController
{

public function index()
    {
// 1️⃣ Kullanıcı oturum kontrolü
$user = session('user');
if (!$user) {
    return redirect()->to('/login');
}

// 2️⃣ Rol belirleme (gerçek + demo)
$realRole = $user['role'] ?? 'member';
$demoRole = session('demo_role');
$roleUsed = $demoRole ?: $realRole;

// 3️⃣ Kullanıcının gerçek birimi
$unitId = $user['unit_id'] ?? null;

        // 4️⃣ Modeller
        $meetingModel = new MeetingModel();
        $userModel    = new UserModel();
        $unitModel    = new UnitModel();

        // 5️⃣ ROL BAZLI GÖRÜNTÜLEME
        if ($roleUsed === 'superadmin') {

            $selectedUnit = session('selected_unit_id');

            if ($selectedUnit) {
                // Seçili birimin toplantıları
                $meetings = $meetingModel
                    ->where('unit_id', $selectedUnit)
                    ->orderBy('start_at', 'DESC')
                    ->findAll();

                $users = $userModel
                    ->where('unit_id', $selectedUnit)
                    ->orderBy('name')
                    ->findAll();

            } else {
                // Tüm birimler
                $meetings = $meetingModel
                    ->orderBy('start_at', 'DESC')
                    ->findAll();

                $users = $userModel
                    ->orderBy('name')
                    ->findAll();
            }
        }

        elseif ($roleUsed === 'manager') {

            // Sadece kendi birimi
            $meetings = $meetingModel
                ->where('unit_id', $unitId)
                ->orderBy('start_at', 'DESC')
                ->findAll();

            $users = $userModel
                ->where('unit_id', $unitId)
                ->orderBy('name')
                ->findAll();
        }

        else { // member

            $meetings = $meetingModel
                ->where('unit_id', $unitId)
                ->orderBy('start_at', 'DESC')
                ->findAll();

            $users = $userModel
                ->where('unit_id', $unitId)
                ->orderBy('name')
                ->findAll();
        }

        $selectedUnit = session('selected_unit_id');
        $showUnitSelect = ($roleUsed === 'superadmin' && empty($selectedUnit));

        // Toplantılara birim adını ekle
        $units = $unitModel->orderBy('name')->findAll();
        $unitMap = array_column($units, 'name', 'id');

        foreach ($meetings as &$m) {
            $m['unit_name'] = $unitMap[$m['unit_id']] ?? '-';
        }
        unset($m);

        return view('meetings/index', [
            'title'     => 'Toplantılar',
            'meetings'  => $meetings,
            'users'     => $users,
            'units'     => $units,
            'role'      => $roleUsed,
            'showUnitSelect' => $showUnitSelect,
            'canEdit' => ($roleUsed !== 'member'),
            'canDelete' => ($roleUsed !== 'member'),
        ]);
    }



    public function create()
    {
        $user = session('user');
        $users = (new UserModel())->where('unit_id', $user['unit_id'])->orderBy('name')->findAll();
        return view('meetings/form', ['users' => $users]);
    }

    public function store()
{
    $post = $this->request->getPost();
    $user = session('user');

    $meetingModel = new \App\Models\MeetingModel();

    // 🧩 Birim belirleme
    $unitId = $post['unit_id'] ?? session('selected_unit_id') ?? $user['unit_id'];

    $data = [
        'start_at'     => $post['start_at'],
        'moderator_id' => $post['moderator_id'],
        'unit_id'      => $unitId,
        'status'       => 'active',
    ];

    $insertId = $meetingModel->insert($data);

    if (!$insertId) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success'=>false]);
        }
        return redirect()->back()->with('error','Toplantı oluşturulamadı!');
    }

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['success'=>true,'meeting_id'=>$insertId]);
    }

    return redirect()->to("/meetings/{$insertId}")->with('success','Toplantı oluşturuldu');
}


    public function show(int $id)
{
    $user = session('user');
    if (!$user) {
        return redirect()->to('/login');
    }

    $meeting = (new MeetingModel())->find($id);
    if (!$meeting) {
        return redirect()->to('/meetings')->with('error', 'Toplantı bulunamadı.');
    }

    // Üye, sadece kendi birimindeki toplantılara girebilir
    if ($user['role'] === 'member' && $meeting['unit_id'] !== $user['unit_id']) {
        return redirect()->to('/meetings')->with('error', 'Bu toplantıya erişim izniniz yok.');
    }

    $users   = (new UserModel())->where('unit_id', $meeting['unit_id'])->findAll();
    
    // 🔹 1) Kullanıcıları çek
    $mpModel = new MeetingParticipantModel();

    // 🔹 2) Varsayılan yoklama KONTROL + INSERT
    $existingCount = $mpModel
        ->where('meeting_id', $id)
        ->countAllResults();

    if ($existingCount === 0) {
        foreach ($users as $u) {
            $mpModel->insert([
                'meeting_id' => $id,
                'user_id'    => $u['id'],
                'status'     => 'geldi',
                'present'    => 1,
            ]);
        }
    }
    
    // 🔹 3) Artık participants’i güvenle çek
    $participants = $mpModel->where('meeting_id', $id)->findAll();

    $agendaItems = (new AgendaItemModel())
        ->select('agenda_items.*, users.name as author_name, users.role as author_role')
        ->join('users', 'users.id = agenda_items.author_id', 'left')
        ->where('meeting_id', $id)
        ->orderBy('agenda_items.id', 'asc')
        ->findAll();

    $decisions = [];
    if ($agendaItems) {
        $ids = array_column($agendaItems, 'id');
        if (!empty($ids)) {
            $decisions = (new DecisionModel())->whereIn('agenda_item_id', $ids)->findAll();
        }
    }

    return view('meetings/show', [
        'meeting' => $meeting,
        'users' => $users,
        'participants' => $participants,
        'agenda' => $agendaItems,
        'decisions' => $decisions,
        'role' => $user['role'], // 👈 view içinde de bilmemiz lazım
    ]);
}

    public function modalEdit($id)
{
    $meeting = (new MeetingModel())->find($id);
    $users   = (new UserModel())
                ->where('unit_id', $meeting['unit_id'])
                ->orderBy('name')
                ->findAll();

    return view('meetings/modal_edit', compact('meeting','users'));
}



    public function update(int $id)
    {
        $data = [
            'start_at'     => $this->request->getPost('start_at'),
            'moderator_id' => (int)$this->request->getPost('moderator_id'),
        ];
        (new MeetingModel())->update($id, $data);
        return redirect()->to("/meetings/$id")->with('success', 'Güncellendi');
    }

    public function end(int $id)
    {
        (new MeetingModel())->update($id, ['status' => 'ended', 'updated_at' => Time::now()]);
        return redirect()->to("/meetings/$id");
    }

public function updateParticipantStatus()
{
    log_message('error', 'updateParticipantStatus HIT');

    $user = session('user');
    $role = $user['role'] ?? null;

    if (!in_array($role, ['manager', 'moderator', 'superadmin'], true)) {
        return $this->response->setStatusCode(403, 'Yetkisiz');
    }

    $data = $this->request->getPost();
    if (!$data || !isset($data['meeting_id'], $data['user_id'], $data['status'])) {
        return $this->response->setJSON(['success' => false, 'msg' => 'Eksik veri']);
    }

    $mp = new \App\Models\MeetingParticipantModel();

    $existing = $mp->where('meeting_id', $data['meeting_id'])
                   ->where('user_id', $data['user_id'])
                   ->first();

    $saveData = [
        'meeting_id' => $data['meeting_id'],
        'user_id'    => $data['user_id'],
        'status'     => $data['status'],
        'present'    => ($data['status'] === 'geldi') ? 1 : 0
    ];

    if ($existing) {
        $result = $mp->update($existing['id'], [
            'status'  => $saveData['status'],
            'present' => $saveData['present']
        ]);
    } else {
        $result = $mp->insert($saveData);
    }

    if ($result === false) {
        log_message('error', 'MP DB ERROR: ' . json_encode($mp->errors()));
        log_message('error', 'LAST QUERY: ' . $mp->db->getLastQuery());
    } else {
        log_message('error', 'MP DB OK: ' . json_encode($saveData));
    }

    return $this->response->setJSON(['success' => true]);
}


    public function saveParticipants(int $id)
    {
        //return redirect()->to("/meetings/$id"); bu method'a gerek yok artık onu hatta komple silebiliriz AJAX ile kaydediyoruz. 
    }

    public function delete($id)
{
    (new MeetingModel())->delete($id);
    return redirect()->to('/meetings')->with('success', 'Toplantı silindi');
}

// tüm birimler siçiliyken moderatör seçerken tüm kullancıları gelmesin
public function getUsersByUnit($unitId)
{
    $users = (new \App\Models\UserModel())
                ->where('unit_id', $unitId)
                ->orderBy('name')
                ->findAll();

    return $this->response->setJSON($users);
}


}
