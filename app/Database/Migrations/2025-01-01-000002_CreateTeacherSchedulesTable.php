<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeacherSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'subject_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'day_of_week' => [
                'type' => 'ENUM',
                'constraint' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'room' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => '2024-2025',
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
        $this->forge->addForeignKey('teacher_id', 'teachers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('section_id', 'sections', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('teacher_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('teacher_schedules');
    }
}