<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterMaterialsCategoryToVarchar extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('materials') || ! $this->db->fieldExists('category', 'materials')) {
            return;
        }

        $fieldData = $this->db->getFieldData('materials');
        $categoryField = null;
        foreach ($fieldData as $field) {
            if (($field->name ?? '') === 'category') {
                $categoryField = $field;
                break;
            }
        }

        $currentType = strtolower((string) ($categoryField->type ?? ''));
        if ($currentType === 'enum') {
            $this->forge->modifyColumn('materials', [
                'category' => [
                    'name'       => 'category',
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'default'    => 'learning_material',
                ],
            ]);
        }

        // Historical rows may contain '' from enum coercion; normalize to "other".
        $this->db->table('materials')
            ->groupStart()
            ->where('category', '')
            ->orWhere('category IS NULL', null, false)
            ->groupEnd()
            ->set('category', 'other')
            ->update();
    }

    public function down()
    {
        // Keep as VARCHAR to avoid losing custom categories.
    }
}

