<?php
namespace App\Models;

use CodeIgniter\Model;

class MeetingParticipantModel extends Model
{
    protected $table = 'meeting_participants';  //toplantı_katılıamcıları
    protected $primaryKey = 'id';
    //protected $useSoftDeletes = true;
    protected $allowedFields = ['meeting_id','user_id','present','status','created_at','updated_at','deleted_at'];
    protected $useTimestamps = true;
    protected $returnType = 'array';
}
 
