<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSectionsTable extends Migration
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
            'section_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'grade_level' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 9, // e.g., "2024-2025"
            ],
            'adviser_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'max_capacity' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 40,
            ],
            'current_enrollment' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
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
        $this->forge->addKey('grade_level');
        $this->forge->addKey('school_year');
        $this->forge->addKey('adviser_id');
        $this->forge->createTable('sections');
    }

    public function down()
    {
        $this->forge->dropTable('sections', true);
    }
}
