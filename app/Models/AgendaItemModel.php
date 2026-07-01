<?php
namespace App\Models;

use CodeIgniter\Model;

class AgendaItemModel extends Model
{
    protected $table = 'agenda_items';  //gündem üyleri
    protected $useSoftDeletes = true;
    protected $allowedFields = ['meeting_id','title','author_id','created_at','updated_at','deleted_at'];
    protected $returnType = 'array';
    
}
