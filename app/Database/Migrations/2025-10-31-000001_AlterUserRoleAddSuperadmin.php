<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUserRoleAddSuperadmin extends Migration
{
    public function up()
    {
        // users.role alanını genişlet: superadmin + manager + member
        $this->db->query("ALTER TABLE `users` 
            MODIFY `role` ENUM('superadmin','manager','member') NOT NULL DEFAULT 'member'");
    }

    public function down()
    {
        // Eski haline geri al (superadmin olmadan)
        $this->db->query("ALTER TABLE `users` 
            MODIFY `role` ENUM('manager','member') NOT NULL DEFAULT 'member'");
    }
}


