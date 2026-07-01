<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

//Migrate: sadece tabloları oluşturur / günceller.
//Seed: tabloları örnek verilerle doldurur.

//Seeds: örnek veri (INSERT) ekler — uygulamayı test etmen için faydalı. seed-tohum
// Seeder tabloların içine demo kayıtlar ekler.
class DemoSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $time = Time::now();

        // Units
        $db->table('units')->insertBatch([
            ['name' => 'Bilgi İşlem', 'created_at' => $time],
            ['name' => 'İdari Mali İşler', 'created_at' => $time],
        ]);

        // Users
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $users = [
            ['unit_id' => 1, 'name' => 'Ahmet Yönetici', 'email' => 'ahmet@ex.com', 'password' => $password, 'role' => 'manager', 'created_at' => $time],
            ['unit_id' => 1, 'name' => 'Ayşe Üye',       'email' => 'ayse@ex.com',  'password' => $password, 'role' => 'member',  'created_at' => $time],
            ['unit_id' => 2, 'name' => 'Mehmet Yönetici','email' => 'mehmet@ex.com','password' => $password, 'role' => 'manager', 'created_at' => $time],
            ['unit_id' => 2, 'name' => 'Fatma Üye',      'email' => 'fatma@ex.com', 'password' => $password, 'role' => 'member',  'created_at' => $time],
        ];
        $db->table('users')->insertBatch($users);

        // Example meeting
        $db->table('meetings')->insert([
            'unit_id' => 1,
            'start_at' => $time,
            'moderator_id' => 1,
            'scribe_id' => 2,
            'status' => 'active',
            'created_at' => $time,
        ]);
        $meetingId = $db->insertID();

        // Meeting participants
        $db->table('meeting_participants')->insertBatch([
            ['meeting_id' => $meetingId, 'user_id' => 1, 'present' => 1, 'created_at' => $time],
            ['meeting_id' => $meetingId, 'user_id' => 2, 'present' => 1, 'created_at' => $time],
        ]);

        // agenda items
        $db->table('agenda_items')->insertBatch([
            ['meeting_id' => $meetingId, 'title' => 'Sunucu güncellemeleri', 'sort_order' => 1, 'created_at' => $time],
            ['meeting_id' => $meetingId, 'title' => 'Yedekleme stratejisi',  'sort_order' => 2, 'created_at' => $time],
        ]);
    }
}

