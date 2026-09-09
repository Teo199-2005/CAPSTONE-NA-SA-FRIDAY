<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeSnedTablesSectionAware extends Migration
{
    public function up()
    {
        // Add section_id to sned_categories (allow NULL for backward compat)
        $this->forge->addColumn('sned_categories', [
            'section_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // Link existing sned categories to their sections via sned_grades
        // First, find which sections have sned grades
        $this->db->query("
            UPDATE sned_categories c
            SET c.section_id = (
                SELECT s.id FROM sections s
                WHERE s.grade_level = 7
                LIMIT 1
            )
            WHERE c.section_id IS NULL
        ");

        // If there are multiple grade 7 sections, we need to duplicate categories per section
        // Get all grade 7 sections
        $snedSections = $this->db->query("SELECT id FROM sections WHERE grading_type = 'non_numerical'")->getResultArray();
        
        if (count($snedSections) > 1) {
            // Get the first section's categories
            $firstSectionId = $snedSections[0]['id'];
            $categories = $this->db->query("SELECT * FROM sned_categories WHERE section_id = ?", [$firstSectionId])->getResultArray();
            
            // For each additional section, duplicate the categories
            for ($i = 1; $i < count($snedSections); $i++) {
                $sectionId = $snedSections[$i]['id'];
                $existingCount = $this->db->query("SELECT COUNT(*) as cnt FROM sned_categories WHERE section_id = ?", [$sectionId])->getRow()->cnt;
                
                if ($existingCount == 0) {
                    foreach ($categories as $cat) {
                        // Insert category for this section
                        $now = date('Y-m-d H:i:s');
                        $this->db->query("
                            INSERT INTO sned_categories (section_id, name, description, display_order, is_active, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ", [$sectionId, $cat['name'], $cat['description'], $cat['display_order'], $cat['is_active'], $now, $now]);
                        
                        $newCategoryId = $this->db->insertID();
                        
                        // Duplicate fields for this new category
                        $fields = $this->db->query("SELECT * FROM sned_category_fields WHERE category_id = ?", [$cat['id']])->getResultArray();
                        foreach ($fields as $field) {
                            $this->db->query("
                                INSERT INTO sned_category_fields (category_id, field_name, display_order, is_active, created_at, updated_at)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ", [$newCategoryId, $field['field_name'], $field['display_order'], $field['is_active'], $now, $now]);
                        }
                    }
                }
            }
        }

        // Add section_id to sned_grades
        $this->forge->addColumn('sned_grades', [
            'section_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // Populate section_id in sned_grades based on student's section
        $this->db->query("
            UPDATE sned_grades g
            JOIN students st ON st.id = g.student_id
            SET g.section_id = st.section_id
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('sned_grades', 'section_id');
        $this->forge->dropColumn('sned_categories', 'section_id');
    }
}