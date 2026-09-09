<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropTrackAndSemester extends Migration
{
    public function up()
    {
        $db = $this->db;

        if ($db->tableExists('students')) {
            foreach ($db->getFieldData('students') as $field) {
                if ($field->name === 'track') {
                    $this->forge->dropColumn('students', 'track');
                    break;
                }
            }
        }

        if ($db->tableExists('system_settings')) {
            $db->table('system_settings')
                ->whereIn('setting_key', ['current_quarter', 'current_semester'])
                ->delete();

            $existing = $db->table('system_settings')
                ->where('setting_key', 'current_term')
                ->get()
                ->getRowArray();

            if (!$existing) {
                $db->table('system_settings')->insert([
                    'setting_key'   => 'current_term',
                    'setting_value' => '1',
                    'description'   => 'Current active term for grading',
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $db = $this->db;

        if ($db->tableExists('students')) {
            $hasTrack = false;
            foreach ($db->getFieldData('students') as $field) {
                if ($field->name === 'track') {
                    $hasTrack = true;
                    break;
                }
            }
            if (!$hasTrack) {
                $this->forge->addColumn('students', [
                    'track' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 50,
                        'null'       => true,
                        'after'      => 'grade_level',
                    ],
                ]);
            }
        }

        if ($db->tableExists('system_settings')) {
            $db->table('system_settings')
                ->where('setting_key', 'current_term')
                ->delete();
        }
    }
}
