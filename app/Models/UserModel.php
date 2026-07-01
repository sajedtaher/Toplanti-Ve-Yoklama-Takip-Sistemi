<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'unit_id',
        'name',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType = 'array';
}


/*
class UserModel extends Model
{
    protected $table = 'users';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['unit_id','name','email','password','role','created_at','updated_at','deleted_at'];
    protected $returnType = 'array';
}
*/