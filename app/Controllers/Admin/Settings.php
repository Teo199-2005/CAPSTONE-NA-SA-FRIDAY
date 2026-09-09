<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Files\UploadedFile;

class Settings extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $yearSetting = $db->table('system_settings')
            ->where('setting_key', 'current_school_year')
            ->get()
            ->getRowArray();

        $termSetting = $db->table('system_settings')
            ->where('setting_key', 'current_term')
            ->get()
            ->getRowArray();

        $teacherPosterSetting = $db->table('system_settings')
            ->where('setting_key', 'featured_poster_teacher')
            ->get()
            ->getRowArray();

        $studentPosterSetting = $db->table('system_settings')
            ->where('setting_key', 'featured_poster_student')
            ->get()
            ->getRowArray();

        helper('asset');

        $currentSchoolYear = $yearSetting['setting_value'] ?? get_current_school_year();
        $currentTerm       = $termSetting['setting_value'] ?? 1;

        return view('admin/settings', [
            'title'                 => 'System Settings - CSCS SMS',
            'currentSchoolYear'     => $currentSchoolYear,
            'currentTerm'           => $currentTerm,
            'featuredPosterTeacher' => $teacherPosterSetting['setting_value'] ?? '',
            'featuredPosterStudent' => $studentPosterSetting['setting_value'] ?? '',
            'featuredPosterTeacherUrl' => featured_poster_url($teacherPosterSetting['setting_value'] ?? ''),
            'featuredPosterStudentUrl' => featured_poster_url($studentPosterSetting['setting_value'] ?? ''),
        ]);
    }

    public function updateSchoolYear()
    {
        $schoolYear = $this->request->getPost('school_year');
        $term       = $this->request->getPost('term');

        if (! $schoolYear) {
            return redirect()->back()->with('error', 'School year is required');
        }

        $db = \Config\Database::connect();

        $exists = $db->table('system_settings')
            ->where('setting_key', 'current_school_year')
            ->get()
            ->getRowArray();

        if ($exists) {
            $db->table('system_settings')
                ->where('setting_key', 'current_school_year')
                ->update(['setting_value' => $schoolYear]);
        } else {
            $db->table('system_settings')->insert([
                'setting_key'   => 'current_school_year',
                'setting_value' => $schoolYear,
            ]);
        }

        if ($term) {
            $termExists = $db->table('system_settings')
                ->where('setting_key', 'current_term')
                ->get()
                ->getRowArray();

            if ($termExists) {
                $db->table('system_settings')
                    ->where('setting_key', 'current_term')
                    ->update(['setting_value' => $term]);
            } else {
                $db->table('system_settings')->insert([
                    'setting_key'   => 'current_term',
                    'setting_value' => $term,
                ]);
            }
        }

        $db->table('sections')
            ->where('deleted_at IS NULL')
            ->update(['school_year' => $schoolYear]);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function updateFeaturedPosters()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $db        = \Config\Database::connect();
        $uploadDir = $this->featuredPosterUploadDir();

        $fields = [
            'featured_poster_teacher' => 'featured_poster_teacher',
            'featured_poster_student' => 'featured_poster_student',
        ];

        $updates = [];

        foreach ($fields as $settingKey => $fileField) {
            $file = $this->request->getFile($fileField);
            if ($file === null || $file->getName() === '') {
                continue;
            }

            $validation = $this->validatePosterUpload($file);
            if ($validation !== true) {
                return redirect()->back()->with('error', $validation);
            }

            $newName = $file->getRandomName();
            try {
                $file->move($uploadDir, $newName);
            } catch (\Throwable $e) {
                log_message('error', 'Featured poster move failed: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Could not save the poster file. Check folder permissions on public/uploads/featured/.');
            }

            $updates[$settingKey] = 'uploads/featured/' . $newName;
        }

        if ($updates === []) {
            return redirect()->back()->with('error', 'No poster image selected. Choose a JPG, PNG, or WEBP file first.');
        }

        foreach ($updates as $key => $value) {
            $exists = $db->table('system_settings')->where('setting_key', $key)->get()->getRowArray();
            if ($exists) {
                $db->table('system_settings')->where('setting_key', $key)->update(['setting_value' => $value]);
            } else {
                $db->table('system_settings')->insert(['setting_key' => $key, 'setting_value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Featured posters updated successfully.');
    }

    public function getGradeSubjects($gradeLevel)
    {
        if (! is_any_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $db = \Config\Database::connect();
        $subjects = $db->table('subjects')
            ->where('grade_level', $gradeLevel)
            ->orderBy('subject_name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success'  => true,
            'subjects' => $subjects,
        ]);
    }

    private function featuredPosterUploadDir(): string
    {
        // The web docroot differs between deployments:
        //  - Standard CI4 docroot (public/): FCPATH itself is public/ -> uploads live at FCPATH.'uploads/featured/'
        //  - Flat Hostinger layout (index.php next to public/): uploads live at FCPATH.'public/uploads/featured/'
        // Prefer an existing directory so we never write somewhere the web cannot serve.
        $candidates = [
            FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'featured' . DIRECTORY_SEPARATOR,
            FCPATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'featured' . DIRECTORY_SEPARATOR,
        ];

        $dir = null;
        foreach ($candidates as $cand) {
            if (is_dir($cand)) {
                $dir = $cand;
                break;
            }
        }

        $dir ??= $candidates[0];

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Cannot create upload directory: ' . $dir);
        }

        return $dir;
    }

    /**
     * @return true|string Error message
     */
    private function validatePosterUpload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            $error = $file->getErrorString();

            return $error !== '' ? $error : 'Upload failed. The file may be too large (max 50MB).';
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return 'Poster image must be 5MB or less.';
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (! in_array($ext, $allowedExt, true)) {
            return 'Poster must be JPG, PNG, or WEBP.';
        }

        $mime = strtolower((string) $file->getMimeType());
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/pjpeg', 'image/x-png'];
        if ($mime !== '' && ! in_array($mime, $allowedMime, true) && ! str_starts_with($mime, 'image/')) {
            return 'Poster must be an image (JPG, PNG, or WEBP).';
        }

        return true;
    }
}
