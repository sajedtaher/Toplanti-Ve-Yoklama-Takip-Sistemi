<?php
namespace App\Controllers;

use App\Models\{AgendaItemModel, DecisionModel};
use CodeIgniter\I18n\Time;

class Agenda extends BaseController
{
    public function add(int $meetingId)
    {
        $title = $this->request->getPost('title');
        (new AgendaItemModel())->insert([
        'meeting_id' => $meetingId,
        'title' => $title,
        'author_id' => (int)($this->request->getPost('author_id') ?? session('user.id')),//yada => session('user.id'), // oturum açan kullanıcıyı kaydet
        'created_at' => Time::now(),
        ]);
        return redirect()->to("/meetings/$meetingId")->with('success', 'Gündem eklendi');
    }

    public function saveDecision(int $agendaItemId)
    {
        $text = $this->request->getPost('decision_text');
        $decisions = new \App\Models\DecisionModel();
        $exists = $decisions->where('agenda_item_id', $agendaItemId)->first();

        if ($exists) {
            $decisions->update($exists['id'], ['decision_text' => $text, 'updated_at' => \CodeIgniter\I18n\Time::now()]);
            $savedId = $exists['id'];
        } else {
            $decisions->insert(['agenda_item_id' => $agendaItemId, 'decision_text' => $text, 'created_at' => \CodeIgniter\I18n\Time::now()]);
            $savedId = $decisions->getInsertID();
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'id' => $savedId,
                'decision_text' => $text
            ]);
        }

    return redirect()->back()->with('success', 'Karar kaydedildi');


        /* kondun eski hali karar satırlı gönmesi ve düzelmesi için
        $text = $this->request->getPost('decision_text');
        $decisions = new DecisionModel();
        $exists = $decisions->where('agenda_item_id', $agendaItemId)->first();
        if ($exists) {
            $decisions->update($exists['id'], ['decision_text' => $text, 'updated_at' => Time::now()]);
        } 
        else {
            $decisions->insert(['agenda_item_id' => $agendaItemId, 'decision_text' => $text, 'created_at' => Time::now()]);
        }
        return redirect()->back()->with('success', 'Karar kaydedildi');
        */
    }


    public function update(int $id)
    {
        $title = $this->request->getPost('title');
        if ($title === null) {
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Başlık boş'
            ]);
        }

        $model = new \App\Models\AgendaItemModel();
        $item = $model->find($id);

        if (!$item) {
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Gündem bulunamadı'
            ]);
        }

        // Burada meeting durumunu kontrol et
        $meetingModel = new \App\Models\MeetingModel();
        $meeting = $meetingModel->find($item['meeting_id']);

        if (!$meeting || $meeting['status'] !== 'active') {
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Bu toplantı sonlandırılmış, gündem maddeleri değiştirilemez'
            ]);
        }

        // Güncelleme
        $model->update($id, [
            'title' => $title,
            'updated_at' => \CodeIgniter\I18n\Time::now()
        ]);

        return $this->response->setJSON([
            'success'=>true,
            'title'=>strip_tags($title)
        ]);
    }

}

