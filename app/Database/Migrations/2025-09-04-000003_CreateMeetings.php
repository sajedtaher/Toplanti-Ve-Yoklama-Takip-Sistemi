<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMeetings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'unit_id'      => ['type' => 'INT', 'unsigned' => true],
            'start_at'     => ['type' => 'DATETIME'],
            'moderator_id' => ['type' => 'INT', 'unsigned' => true],
            'scribe_id'    => ['type' => 'INT', 'unsigned' => true],
            'status'       => ['type' => 'ENUM("draft","active","ended")', 'default' => 'draft'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('moderator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('scribe_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('meetings');
    }
    public function down() { $this->forge->dropTable('meetings'); }
}

