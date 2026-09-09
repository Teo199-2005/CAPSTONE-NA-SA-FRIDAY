<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameQuarterToTerm extends Migration
{
    public function up()
    {
        $db = $this->db;

        if (!$db->tableExists('grades')) {
            return;
        }

        // Elementary uses three terms; discard any rows that targeted the old fourth quarter.
        $db->query("DELETE FROM grades WHERE quarter = 4");

        $fields = $db->getFieldData('grades');
        $hasQuarter = false;
        $hasTerm    = false;
        foreach ($fields as $field) {
            if ($field->name === 'quarter') {
                $hasQuarter = true;
            }
            if ($field->name === 'term') {
                $hasTerm = true;
            }
        }

        if ($hasQuarter && !$hasTerm) {
            $this->forge->modifyColumn('grades', [
                'quarter' => [
                    'name'       => 'term',
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                ],
            ]);
        }
    }

    public function down()
    {
        $db = $this->db;

        if (!$db->tableExists('grades')) {
            return;
        }

        $fields = $db->getFieldData('grades');
        foreach ($fields as $field) {
            if ($field->name === 'term') {
                $this->forge->modifyColumn('grades', [
                    'term' => [
                        'name'       => 'quarter',
                        'type'       => 'TINYINT',
                        'constraint' => 1,
                        'null'       => false,
                    ],
                ]);
                break;
            }
        }
    }
}
