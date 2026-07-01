<?php
namespace App\Models;

use CodeIgniter\Model;

class DecisionModel extends Model
{
    protected $table = 'decisions';     //kararlar
    protected $useSoftDeletes = true;
    protected $allowedFields = ['agenda_item_id','decision_text','created_at','updated_at','deleted_at'];
    protected $returnType = 'array';
}
