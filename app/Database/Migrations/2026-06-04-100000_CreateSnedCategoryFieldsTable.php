<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSnedCategoryFieldsTable extends Migration
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
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('category_id');
        $this->forge->addKey(['category_id', 'display_order']);
        $this->forge->createTable('sned_category_fields');
    }

    public function down()
    {
        $this->forge->dropTable('sned_category_fields', true);
    }
}