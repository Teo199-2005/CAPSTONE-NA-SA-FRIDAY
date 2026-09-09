<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlatformRatingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'responder_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addKey('rating');
        $this->forge->addKey('responder_role');
        $this->forge->addKey('created_at');
        $this->forge->createTable('platform_ratings');
    }

    public function down(): void
    {
        $this->forge->dropTable('platform_ratings');
    }
}
