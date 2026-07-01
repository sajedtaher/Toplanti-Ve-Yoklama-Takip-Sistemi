<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDecisions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'agenda_item_id' => ['type' => 'INT', 'unsigned' => true],
            'decision_text'  => ['type' => 'TEXT'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('agenda_item_id', 'agenda_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('decisions');
    }
    public function down() { $this->forge->dropTable('decisions'); }
}

