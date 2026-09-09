<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentDocumentsTable extends Migration
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
            'document_type' => [
                'type'       => 'ENUM',
                'constraint' => ['birth_certificate', 'report_card', 'good_moral', 'medical_certificate', 'photo', 'other'],
            ],
            'document_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'is_verified' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('document_type');
        $this->forge->addKey('is_verified');
        $this->forge->createTable('enrollment_documents');
    }

    public function down()
    {
        $this->forge->dropTable('enrollment_documents', true);
    }
}
