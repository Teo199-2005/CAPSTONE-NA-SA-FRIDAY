<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGradesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 9, // e.g., "2024-2025"
            ],
            'quarter' => [
                'type'       => 'INT',
                'constraint' => 1, // 1, 2, 3, 4
            ],
            'grade' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'date_recorded' => [
                'type' => 'DATETIME',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('student_id');
        $this->forge->addKey('subject_id');
        $this->forge->addKey('teacher_id');
        $this->forge->addKey(['student_id', 'subject_id', 'school_year', 'quarter']);
        $this->forge->createTable('grades');
    }

    public function down()
    {
        $this->forge->dropTable('grades', true);
    }
}
