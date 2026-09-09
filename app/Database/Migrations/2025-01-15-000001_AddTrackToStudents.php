<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrackToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'track' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'grade_level'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'track');
    }
}
