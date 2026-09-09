<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameReportCardQuarterColumns extends Migration
{
    public function up()
    {
        $db = $this->db;

        if (!$db->tableExists('report_card_records')) {
            return;
        }

        $existing = [];
        foreach ($db->getFieldData('report_card_records') as $field) {
            $existing[$field->name] = true;
        }

        $renames = [
            'q1_average' => 'term1_average',
            'q2_average' => 'term2_average',
            'q3_average' => 'term3_average',
        ];

        foreach ($renames as $from => $to) {
            if (isset($existing[$from]) && !isset($existing[$to])) {
                $this->forge->modifyColumn('report_card_records', [
                    $from => [
                        'name'       => $to,
                        'type'       => 'DECIMAL',
                        'constraint' => '5,2',
                        'null'       => true,
                    ],
                ]);
            }
        }

        if (isset($existing['q4_average'])) {
            $this->forge->dropColumn('report_card_records', 'q4_average');
        }
    }

    public function down()
    {
        $db = $this->db;

        if (!$db->tableExists('report_card_records')) {
            return;
        }

        $existing = [];
        foreach ($db->getFieldData('report_card_records') as $field) {
            $existing[$field->name] = true;
        }

        $renames = [
            'term1_average' => 'q1_average',
            'term2_average' => 'q2_average',
            'term3_average' => 'q3_average',
        ];

        foreach ($renames as $from => $to) {
            if (isset($existing[$from]) && !isset($existing[$to])) {
                $this->forge->modifyColumn('report_card_records', [
                    $from => [
                        'name'       => $to,
                        'type'       => 'DECIMAL',
                        'constraint' => '5,2',
                        'null'       => true,
                    ],
                ]);
            }
        }

        if (!isset($existing['q4_average'])) {
            $this->forge->addColumn('report_card_records', [
                'q4_average' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => true,
                    'after'      => 'q3_average',
                ],
            ]);
        }
    }
}
