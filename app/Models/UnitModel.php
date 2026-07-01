<?php
namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'units';
    protected $primaryKey       = 'id';

    // Sadece düzenleyebileceğin alanlar
    protected $allowedFields    = [
        'name',
        'manager_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Otomatik zaman damgaları
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Soft delete
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    protected $returnType       = 'array';
}
