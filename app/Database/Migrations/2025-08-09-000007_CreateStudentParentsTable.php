<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentParentsTable extends Migration
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
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'relationship' => [
                'type'       => 'ENUM',
                'constraint' => ['Father', 'Mother', 'Guardian', 'Stepfather', 'Stepmother', 'Grandfather', 'Grandmother', 'Uncle', 'Aunt', 'Other'],
                'default'    => 'Guardian',
            ],
            'is_primary' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'is_emergency_contact' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
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
        $this->forge->addKey('parent_id');
        $this->forge->addUniqueKey(['student_id', 'parent_id']);
        $this->forge->createTable('student_parents');
    }

    public function down()
    {
        $this->forge->dropTable('student_parents', true);
    }
}
