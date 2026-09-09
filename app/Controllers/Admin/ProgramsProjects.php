<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Files\UploadedFile;

class ProgramsProjects extends BaseController
{
    private const VALID_TABS = ['programs', 'projects'];
    private const MAX_SECTIONS = 6;

    public function index(): string
    {
        helper(['programs_projects', 'asset', 'admin_access']);

        $tabs = [];
        foreach (self::VALID_TABS as $tab) {
            $hero = programs_projects_hero_get($tab);
            $tabs[$tab] = [
                'hero'     => $hero,
                'sections' => programs_projects_sections_get($tab),
                'heroUrl'  => programs_projects_media_url($hero['image']),
            ];
        }

        return view('admin/programs_projects', [
            'title' => 'Programs and Projects Management — CSCS SMS',
            'tabs'  => $tabs,
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        helper(['programs_projects', 'admin_access']);

        if (! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $tab = $this->request->getPost('tab');
        if (! in_array($tab, self::VALID_TABS, true)) {
            return redirect()->back()->with('error', 'Invalid tab specified.');
        }

        // Load existing sections to preserve media URLs when no new input provided
        $existingSections = programs_projects_sections_get($tab);

        // --- Hero ---
        $hero = programs_projects_hero_get($tab);

        $hero['title']       = trim((string) $this->request->getPost('hero_title'));
        $hero['description'] = trim((string) $this->request->getPost('hero_description'));
        $hero['position']    = trim((string) ($this->request->getPost('hero_position') ?? '50% 50%'));
        $hero['scale']       = (float) ($this->request->getPost('hero_scale') ?? 1.0);

        $heroFile = $this->request->getFile('hero_image');
        if ($heroFile !== null && $heroFile->getName() !== '') {
            $validation = $this->validateHeroUpload($heroFile);
            if ($validation !== true) {
                return redirect()->back()->with('error', 'Hero image: ' . $validation);
            }

            $newName = $heroFile->getRandomName();
            try {
                $heroFile->move(programs_projects_upload_dir(), $newName);
                $hero['image'] = 'uploads/programs-projects/' . $newName;
            } catch (\Throwable $e) {
                log_message('error', 'Programs/Projects hero upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Could not save hero image. Check upload directory permissions.');
            }
        }

        // Remove hero image
        if ($this->request->getPost('remove_hero') === '1') {
            $hero['image'] = '';
        }

        programs_projects_hero_save($tab, $hero);

        // --- Sections ---
        $sections = [];
        $sectionIds = $this->request->getPost('section_id');
        if (is_array($sectionIds)) {
            for ($i = 0; $i < count($sectionIds); $i++) {
                if ($i >= self::MAX_SECTIONS) {
                    break;
                }

                $sectionId = trim((string) ($sectionIds[$i] ?? ''));
                if ($sectionId === '') {
                    continue;
                }

                $sectionTitle       = trim((string) ($this->request->getPost('section_title')[$i] ?? ''));
                $sectionDescription = trim((string) ($this->request->getPost('section_description')[$i] ?? ''));
                $mediaType          = (string) ($this->request->getPost('section_media_type')[$i] ?? 'image');
                $mediaUrl           = trim((string) ($this->request->getPost('section_media_url')[$i] ?? ''));
                // Keep existing media URL if no new URL provided and no file uploaded
                if ($mediaUrl === '' && ! empty($existingSections[$i]['media_url'])) {
                    $mediaUrl = $existingSections[$i]['media_url'];
                }
                $order              = (int) ($this->request->getPost('section_order')[$i] ?? $i);
                $removeMedia        = (string) ($this->request->getPost('section_remove_media')[$i] ?? '0');
                $sectionPosition    = trim((string) ($this->request->getPost('section_position')[$i] ?? '50% 50%'));
                $sectionScale       = (float) ($this->request->getPost('section_scale')[$i] ?? 1.0);

                // Handle file upload for this section
                $sectionFile = $this->request->getFile('section_media_' . $sectionId);
                if ($sectionFile !== null && $sectionFile->getName() !== '') {
                    $validation = $this->validateSectionMedia($sectionFile);
                    if ($validation !== true) {
                        return redirect()->back()->with('error', 'Section "' . $sectionTitle . '": ' . $validation);
                    }

                    $newName = $sectionFile->getRandomName();
                    try {
                        $sectionFile->move(programs_projects_upload_dir(), $newName);
                        $mediaUrl = 'uploads/programs-projects/' . $newName;
                        $mediaType = 'image'; // uploaded files are images
                    } catch (\Throwable $e) {
                        log_message('error', 'Programs/Projects section upload failed: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Could not save media for section "' . $sectionTitle . '".');
                    }
                }

                if ($removeMedia === '1') {
                    $mediaUrl = '';
                }

                $sections[] = [
                    'id'          => $sectionId,
                    'title'       => $sectionTitle,
                    'description' => $sectionDescription,
                    'media_type'  => $mediaType,
                    'media_url'   => $mediaUrl,
                    'order'       => $order,
                    'position'    => $sectionPosition,
                    'scale'       => $sectionScale,
                ];
            }
        }

        programs_projects_sections_save($tab, $sections);

        return redirect()->back()->with('success', programs_projects_tab_label($tab) . ' page updated successfully.');
    }

    /**
     * @return true|string
     */
    private function validateHeroUpload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            $error = $file->getErrorString();
            return $error !== '' ? $error : 'Upload failed. File may be too large (max 50MB).';
        }

        if ($file->getSize() > 50 * 1024 * 1024) {
            return 'Image must be 50MB or less.';
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return 'Image must be JPG, PNG, or WEBP.';
        }

        $mime = strtolower((string) $file->getMimeType());
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/pjpeg', 'image/x-png'];
        if ($mime !== '' && ! in_array($mime, $allowedMime, true) && ! str_starts_with($mime, 'image/')) {
            return 'File must be an image (JPG, PNG, or WEBP).';
        }

        return true;
    }

    /**
     * @return true|string
     */
    private function validateSectionMedia(UploadedFile $file)
    {
        if (! $file->isValid()) {
            $error = $file->getErrorString();
            return $error !== '' ? $error : 'Upload failed. File may be too large (max 50MB).';
        }

        if ($file->getSize() > 50 * 1024 * 1024) {
            return 'File must be 50MB or less.';
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'mp4'], true)) {
            return 'File must be JPG, PNG, WEBP, or MP4.';
        }

        return true;
    }
}