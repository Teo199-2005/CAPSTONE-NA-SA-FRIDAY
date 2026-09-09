<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTermToPlatformRatings extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('platform_ratings')) {
            return;
        }

        if (! $this->db->fieldExists('school_year', 'platform_ratings')) {
            $this->forge->addColumn('platform_ratings', [
                'school_year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 9,
                    'null'       => true,
                    'after'      => 'user_id',
                ],
                'term' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'school_year',
                ],
            ]);
        }

        $syRow = $this->db->table('system_settings')
            ->where('setting_key', 'current_school_year')
            ->get()
            ->getRowArray();
        $termRow = $this->db->table('system_settings')
            ->where('setting_key', 'current_term')
            ->get()
            ->getRowArray();

        $year = (int) date('Y');
        $schoolYear = $syRow['setting_value'] ?? ($year . '-' . ($year + 1));
        $term = (int) ($termRow['setting_value'] ?? 1);
        if ($term < 1 || $term > 3) {
            $term = 1;
        }

        $this->db->query(
            'UPDATE platform_ratings SET school_year = ?, term = ? WHERE school_year IS NULL OR school_year = ""',
            [$schoolYear, $term]
        );

        $this->forge->modifyColumn('platform_ratings', [
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 9,
                'null'       => false,
            ],
            'term' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        $indexes = $this->db->getIndexData('platform_ratings');
        foreach ($indexes as $index) {
            if (($index->name ?? '') === 'user_id' && ($index->type ?? '') === 'UNIQUE') {
                $this->forge->dropKey('platform_ratings', 'user_id', true);
                break;
            }
        }

        $hasComposite = false;
        foreach ($indexes as $index) {
            if (($index->name ?? '') === 'platform_ratings_user_term') {
                $hasComposite = true;
                break;
            }
        }

        if (! $hasComposite) {
            $this->forge->addUniqueKey(['user_id', 'school_year', 'term'], 'platform_ratings_user_term');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('platform_ratings')) {
            return;
        }

        if ($this->db->fieldExists('school_year', 'platform_ratings')) {
            $this->forge->dropKey('platform_ratings', 'platform_ratings_user_term', true);
            $this->forge->dropColumn('platform_ratings', ['school_year', 'term']);
            $this->forge->addUniqueKey('user_id');
        }
    }
}
