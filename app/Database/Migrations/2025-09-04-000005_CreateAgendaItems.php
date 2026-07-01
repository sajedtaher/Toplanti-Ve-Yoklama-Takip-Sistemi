<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgendaItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'meeting_id' => ['type' => 'INT', 'unsigned' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('meeting_id', 'meetings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('agenda_items');
    }
    public function down() { $this->forge->dropTable('agenda_items'); }
}

