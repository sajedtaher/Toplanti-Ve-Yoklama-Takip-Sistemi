<?php
namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table = 'participants';
    protected $allowedFields = ['meeting_id', 'user_id', 'status']; // 'present' yerine 'status' eklendi
}
