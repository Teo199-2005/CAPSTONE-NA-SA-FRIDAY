<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNameFieldsToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'email'
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'first_name'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['first_name', 'last_name']);
    }
}
