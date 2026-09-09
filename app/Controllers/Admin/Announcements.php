<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Announcements extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    /**
     * Display list of announcements with CRUD interface
     */
    public function index()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        
        // Get all announcements with pagination
        $announcements = $announcementModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get statistics
        $stats = [
            'total' => $announcementModel->countAllResults(false),
            'published' => $announcementModel->countAllResults(false), // All announcements are published
        ];

        return view('admin/announcements', [
            'title' => 'Announcements - CSCS SMS',
            'announcements' => $announcements,
            'stats' => $stats,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        return view('admin/announcements_create', [
            'title' => 'Create Announcement - CSCS SMS',
        ]);
    }

    /**
     * Store new announcement
     */
    public function store()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,admin,teacher,student,specific_grade,specific_section]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $announcementModel = new AnnouncementModel();
        $baseSlug = url_title($this->request->getPost('title'), '-', true);
        $slug = $this->generateUniqueSlug($baseSlug, $announcementModel);

        $targetRoles = $this->request->getPost('target_roles');
        $gradeLevel = $this->request->getPost('grade_level');
        $gradeLevels = $this->request->getPost('grade_levels') ?? [];
        $sectionId = $this->request->getPost('section_id');
        
        // Build target roles string based on selection
        $finalTargetRoles = $targetRoles;
        if ($targetRoles === 'specific_grade' && !empty($gradeLevels)) {
            // Multiple grade levels selected via checkboxes
            $finalTargetRoles = implode(',', array_map(function($g) {
                return 'grade_' . $g;
            }, $gradeLevels));
        } elseif ($targetRoles === 'specific_grade' && $gradeLevel) {
            // Single grade level selected via dropdown (fallback)
            $finalTargetRoles = 'grade_' . $gradeLevel;
        } elseif ($targetRoles === 'specific_section' && $sectionId) {
            $finalTargetRoles = 'section_' . $sectionId;
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'body' => $this->request->getPost('body'),
            'target_roles' => $finalTargetRoles,
            'created_by' => $this->auth->id(),
            'published_at' => date('Y-m-d H:i:s'), // Always publish immediately
        ];
        
        if ($announcementModel->save($data)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement published successfully!');
        } else {
            return redirect()->back()->withInput()->with('errors', $announcementModel->errors());
        }
    }

    /**
     * Show specific announcement
     */
    public function show($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        return view('admin/announcements_show', [
            'title' => 'View Announcement - CSCS SMS',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        return view('admin/announcements_edit', [
            'title' => 'Edit Announcement - CSCS SMS',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update announcement content (AJAX)
     */
    public function updateContent($id)
    {
        if (! is_any_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return $this->response->setJSON(['success' => false, 'message' => 'Announcement not found']);
        }

        $body = $this->request->getPost('body');
        
        if (empty($body)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Content cannot be empty']);
        }

        if ($announcementModel->update($id, ['body' => $body])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Content updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update']);
    }

    /**
     * Update announcement
     */
    public function update($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,admin,teacher,student,specific_grade,specific_section]',
            'grade_levels' => 'permit_empty|is_array',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $baseSlug = url_title($this->request->getPost('title'), '-', true);
        $slug = $this->generateUniqueSlug($baseSlug, $announcementModel, $id);

        $targetRoles = $this->request->getPost('target_roles');
        $gradeLevel = $this->request->getPost('grade_level');
        $gradeLevels = $this->request->getPost('grade_levels') ?? [];
        $sectionId = $this->request->getPost('section_id');

        // Build target roles string based on selection
        $finalTargetRoles = $targetRoles;
        if ($targetRoles === 'specific_grade' && !empty($gradeLevels)) {
            // Multiple grade levels selected via checkboxes
            $finalTargetRoles = implode(',', array_map(function($g) {
                return 'grade_' . $g;
            }, $gradeLevels));
        } elseif ($targetRoles === 'specific_grade' && $gradeLevel) {
            // Single grade level selected via dropdown (fallback)
            $finalTargetRoles = 'grade_' . $gradeLevel;
        } elseif ($targetRoles === 'specific_section' && $sectionId) {
            $finalTargetRoles = 'section_' . $sectionId;
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'body' => $this->request->getPost('body'),
            'target_roles' => $finalTargetRoles,
            'published_at' => $announcement['published_at'] ?: date('Y-m-d H:i:s'), // Ensure it's always published
        ];

        if ($announcementModel->update($id, $data)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('errors', $announcementModel->errors());
        }
    }

    /**
     * Delete announcement
     */
    public function delete($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        if ($announcementModel->delete($id)) {
            return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement deleted successfully!');
        } else {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Failed to delete announcement.');
        }
    }



    /**
     * Export PDF for analytics reports
     */
    public function downloadPdf($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        // Check if this is an analytics report
        if (strpos($announcement['title'], 'Analytics Report') === false) {
            return redirect()->back()->with('error', 'PDF export is only available for analytics reports.');
        }

        $slug = (string) ($announcement['slug'] ?? '');
        if (preg_match('/class-analytics-t(\d+)-/', $slug, $m)) {
            return redirect()->to(base_url('teacher/analytics/export-pdf?teacher_id=' . (int) $m[1]));
        }

        $teacherName = '';
        if (preg_match('/Class Analytics Report - (.+)/', $announcement['title'], $matches)) {
            $teacherName = trim($matches[1]);
        }

        $url = base_url('teacher/analytics/export-pdf');
        if ($teacherName !== '') {
            $url .= '?teacher=' . urlencode($teacherName);
        }

        return redirect()->to($url);
    }

    /**
     * Get announcement statistics (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX() || ! is_any_admin()) {
            return $this->response->setStatusCode(403);
        }

        $announcementModel = new AnnouncementModel();
        
        $stats = [
            'total' => $announcementModel->countAllResults(false),
            'published' => $announcementModel->countAllResults(false), // All announcements are published
        ];

        return $this->response->setJSON($stats);
    }

    /**
     * Get sections by grade level (AJAX)
     */
    public function getSections()
    {
        if (!$this->auth->loggedIn() || ! is_any_admin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $gradeLevel = $this->request->getGet('grade_level');
        
        if (!$gradeLevel) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grade level required']);
        }

        try {
            $db = \Config\Database::connect();
            $sections = $db->table('sections')
                ->select('id, section_name, grade_level')
                ->where('grade_level', $gradeLevel)
                ->orderBy('section_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'sections' => $sections,
                'count' => count($sections)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error fetching sections: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading sections: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique slug by appending counter if needed
     */
    private function generateUniqueSlug($baseSlug, $model, $excludeId = null)
    {
        $slug = $baseSlug;
        $counter = 1;
        
        while (true) {
            $query = $model->where('slug', $slug);
            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }
            
            if ($query->countAllResults() == 0) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

