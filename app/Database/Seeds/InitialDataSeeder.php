<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $this->seedSections();
        $this->seedSubjects();
        $this->seedFaq();
        $this->seedAnnouncements();
    }

    private function seedSections()
    {
        $currentYear = date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $sections = [];
        foreach (['K-A', 'K-B'] as $kSection) {
            $sections[] = [
                'section_name' => $kSection,
                'grade_level'  => 0,
                'school_year'  => $schoolYear,
                'max_capacity' => 40,
            ];
        }
        foreach (range(1, 6) as $grade) {
            foreach (['A', 'B'] as $suffix) {
                $sections[] = [
                    'section_name' => $grade . '-' . $suffix,
                    'grade_level'  => $grade,
                    'school_year'  => $schoolYear,
                    'max_capacity' => 40,
                ];
            }
        }

        $this->db->table('sections')->insertBatch($sections);
    }

    private function seedSubjects()
    {
        $subjects = [];

        $subjects[] = ['subject_code' => 'LANGK', 'subject_name' => 'Language (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];
        $subjects[] = ['subject_code' => 'MATHK', 'subject_name' => 'Mathematics (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];
        $subjects[] = ['subject_code' => 'SCIK', 'subject_name' => 'Science (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];
        $subjects[] = ['subject_code' => 'APK', 'subject_name' => 'Araling Panlipunan (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];
        $subjects[] = ['subject_code' => 'MAPEHK', 'subject_name' => 'MAPEH (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];
        $subjects[] = ['subject_code' => 'ESPK', 'subject_name' => 'Edukasyon sa Pagpapakatao (Kindergarten)', 'grade_level' => 0, 'units' => 1.0, 'is_core' => true];

        foreach ([1, 2, 3] as $grade) {
            $subjects[] = ['subject_code' => "MTB{$grade}", 'subject_name' => "Mother Tongue {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "FIL{$grade}", 'subject_name' => "Filipino {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "ENG{$grade}", 'subject_name' => "English {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "MATH{$grade}", 'subject_name' => "Mathematics {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "AP{$grade}", 'subject_name' => "Araling Panlipunan {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "MAPEH{$grade}", 'subject_name' => "MAPEH {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "ESP{$grade}", 'subject_name' => "Edukasyon sa Pagpapakatao {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            if ($grade === 3) {
                $subjects[] = ['subject_code' => "SCI{$grade}", 'subject_name' => "Science {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            }
        }

        foreach ([4, 5, 6] as $grade) {
            $subjects[] = ['subject_code' => "FIL{$grade}", 'subject_name' => "Filipino {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "ENG{$grade}", 'subject_name' => "English {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "MATH{$grade}", 'subject_name' => "Mathematics {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "SCI{$grade}", 'subject_name' => "Science {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "AP{$grade}", 'subject_name' => "Araling Panlipunan {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "MAPEH{$grade}", 'subject_name' => "MAPEH {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "ESP{$grade}", 'subject_name' => "Edukasyon sa Pagpapakatao {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
            $subjects[] = ['subject_code' => "EPP{$grade}", 'subject_name' => "Edukasyong Pantahanan at Pangkabuhayan {$grade}", 'grade_level' => $grade, 'units' => 1.0, 'is_core' => true];
        }

        $this->db->table('subjects')->insertBatch($subjects);
    }

    private function seedFaq()
    {
        $faqs = [
            [
                'question' => 'What are the enrollment requirements?',
                'answer'   => 'Required documents include: Birth Certificate (PSA), Report Card/Form 138, Certificate of Good Moral Character, Medical Certificate, and 2x2 ID photos.',
                'keywords' => 'enrollment,requirements,documents,birth certificate,report card,good moral,medical certificate,photos',
                'category' => 'enrollment',
            ],
            [
                'question' => 'When is the enrollment period?',
                'answer'   => 'Enrollment for the upcoming school year typically starts in March and ends in June. Please check our announcements for specific dates.',
                'keywords' => 'enrollment,period,when,dates,march,june,school year',
                'category' => 'enrollment',
            ],
            [
                'question' => 'How can I check my grades?',
                'answer'   => 'Students and parents can log in to the portal using their credentials to view grades and academic performance.',
                'keywords' => 'grades,check,view,academic,performance,login,portal',
                'category' => 'academics',
            ],
            [
                'question' => 'What is the school contact information?',
                'answer'   => 'You can reach Cauayan South Central School via email at 302002@deped.gov.ph. Office hours are Monday to Friday, 8:00 AM to 5:00 PM.',
                'keywords' => 'contact,email,office hours,information',
                'category' => 'general',
            ],
            [
                'question' => 'How do I reset my password?',
                'answer'   => 'Click on "Forgot Password" on the login page and enter your email address. You will receive instructions to reset your password.',
                'keywords' => 'password,reset,forgot,login,email',
                'category' => 'technical',
            ],
        ];

        $this->db->table('faq')->insertBatch($faqs);
    }

    private function seedAnnouncements()
    {
        $currentYear = date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $announcements = [
            [
                'title'        => "Welcome to School Year {$schoolYear}",
                'slug'         => 'welcome-sy-' . $currentYear . '-' . ($currentYear + 1),
                'body'         => 'We welcome all pupils, parents, and faculty to the new school year. Let us work together for academic excellence.',
                'target_roles' => 'all',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Enrollment Period Open',
                'slug'         => 'enrollment-period-open-' . $currentYear,
                'body'         => 'Enrollment for the new school year is now open. Please prepare the required documents before visiting the registrar.',
                'target_roles' => 'all',
                'published_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('announcements')->insertBatch($announcements);
    }
}
