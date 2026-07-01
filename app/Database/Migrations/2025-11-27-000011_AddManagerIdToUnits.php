<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManagerIdToUnits extends Migration
{
    public function up()
    {
        $fields = [
            'manager_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'name'
            ]
        ];

        $this->forge->addColumn('units', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('units', 'manager_id');
    }
}
