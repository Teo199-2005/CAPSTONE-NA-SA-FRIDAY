<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSectionGradingSymbols extends Migration
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
            'section_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'symbol' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'display_order' => [
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('section_id');
        $this->forge->addKey(['section_id', 'display_order']);
        $this->forge->createTable('section_grading_symbols');

        // Seed default symbols for existing non_numerical sections
        $sections = $this->db->query("SELECT id FROM sections WHERE grading_type = 'non_numerical'")->getResultArray();
        
        $defaultSymbols = [
            ['symbol' => 'P',     'label' => 'Proficient',                 'description' => 'The student consistently demonstrates the skill independently.', 'display_order' => 1],
            ['symbol' => 'AP',    'label' => 'Approaching Proficiency',    'description' => 'The student is developing the skill with minimal assistance.',      'display_order' => 2],
            ['symbol' => 'D',     'label' => 'Developing',                 'description' => 'The student is beginning to develop the skill with guidance.',     'display_order' => 3],
            ['symbol' => 'B',     'label' => 'Beginning',                  'description' => 'The student needs significant support to develop the skill.',   'display_order' => 4],
            ['symbol' => 'NO/NA', 'label' => 'Not Observed / Not Applicable', 'description' => 'The skill has not been observed or is not applicable at this time.', 'display_order' => 5],
        ];

        foreach ($sections as $section) {
            foreach ($defaultSymbols as $sym) {
                $sym['section_id'] = $section['id'];
                $sym['created_at'] = date('Y-m-d H:i:s');
                $sym['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('section_grading_symbols')->insert($sym);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('section_grading_symbols', true);
    }
}