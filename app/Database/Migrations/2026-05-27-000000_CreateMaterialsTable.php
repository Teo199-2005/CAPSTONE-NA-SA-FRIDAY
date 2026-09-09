<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('materials')) {
            if (! $this->db->fieldExists('show_on_website', 'materials')) {
                $this->forge->addColumn('materials', [
                    'show_on_website' => [
                        'type'       => 'TINYINT',
                        'constraint' => 1,
                        'default'    => 1,
                        'after'      => 'is_public',
                    ],
                    'sort_order' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 0,
                        'after'      => 'show_on_website',
                    ],
                ]);
            }

            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'learning_material',
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'uploaded_by_type' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'teacher'],
                'default'    => 'admin',
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'show_on_website' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addKey(['show_on_website', 'uploaded_by_type']);
        $this->forge->createTable('materials', true);
    }

    public function down()
    {
        if ($this->db->tableExists('materials') && $this->db->fieldExists('show_on_website', 'materials')) {
            $this->forge->dropColumn('materials', ['show_on_website', 'sort_order']);
        }
    }
}
