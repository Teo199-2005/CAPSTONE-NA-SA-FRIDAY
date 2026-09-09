<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTempPasswordToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'temp_password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'school_year'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'temp_password');
    }
}