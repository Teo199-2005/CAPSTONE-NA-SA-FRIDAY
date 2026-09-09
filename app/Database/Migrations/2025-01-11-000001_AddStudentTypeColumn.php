<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentTypeColumn extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'student_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'lrn'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'student_type');
    }
}