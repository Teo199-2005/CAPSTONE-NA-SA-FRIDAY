<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherPersonnelFields extends Migration
{
    public function up()
    {
        $fields = [
            'tin' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
                'after'      => 'license_number',
            ],
            'personnel_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'tin',
            ],
            'fund_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nature_of_appointment' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'baccalaureate_degree' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'prc_specialization' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'prc_major_units_percent' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'minor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'masters_degree' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'government_employee_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'hiring_arrangement' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'religion' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'ethnic_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'item_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'civil_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'philsys_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 12,
                'null'       => true,
            ],
            'eligibility' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'date_first_service' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'date_first_service_new_station' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('teachers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('teachers', [
            'tin',
            'personnel_category',
            'fund_source',
            'designation',
            'nature_of_appointment',
            'baccalaureate_degree',
            'prc_specialization',
            'prc_major_units_percent',
            'minor',
            'masters_degree',
            'government_employee_no',
            'hiring_arrangement',
            'religion',
            'ethnic_group',
            'item_status',
            'civil_status',
            'philsys_number',
            'eligibility',
            'date_first_service',
            'date_first_service_new_station',
        ]);
    }
}
