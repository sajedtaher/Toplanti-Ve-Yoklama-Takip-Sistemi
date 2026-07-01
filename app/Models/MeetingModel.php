<?php
namespace App\Models;

use CodeIgniter\Model;

class MeetingModel extends Model
{
    protected $table = 'meetings';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['unit_id','start_at','moderator_id','scribe_id','status','created_at','updated_at','deleted_at'];
    protected $returnType = 'array';
}
