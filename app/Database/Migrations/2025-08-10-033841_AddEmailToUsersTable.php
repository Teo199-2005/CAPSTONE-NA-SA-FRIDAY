<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'username'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'email');
    }
}
