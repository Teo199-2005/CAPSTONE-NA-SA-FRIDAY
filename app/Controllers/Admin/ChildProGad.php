<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Files\UploadedFile;

class ChildProGad extends BaseController
{
    private const VALID_TABS = ['childpro', 'gad'];
    private const MAX_SECTIONS = 6;

    public function index(): string
    {
        helper(['childpro_gad', 'asset', 'admin_access']);

        $tabs = [];
        foreach (self::VALID_TABS as $tab) {
            $hero = childpro_gad_hero_get($tab);
            $tabs[$tab] = [
                'hero'     => $hero,
                'sections' => childpro_gad_sections_get($tab),
                'heroUrl'  => childpro_gad_media_url($hero['image']),
            ];
        }

        return view('admin/childpro_gad', [
            'title' => 'CHILDPRO / GAD Management — CSCS SMS',
            'tabs'  => $tabs,
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        helper(['childpro_gad', 'admin_access']);

        if (! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $tab = trim((string) ($this->request->getPost('tab') ?? ''));
        if (! in_array($tab, self::VALID_TABS, true)) {
            return redirect()->back()->with('error', 'Invalid tab specified.');
        }

        // --- Hero ---
        $hero = childpro_gad_hero_get($tab);

        $hero['title']       = trim((string) $this->request->getPost('hero_title'));
        $hero['description'] = trim((string) $this->request->getPost('hero_description'));
        $hero['position']    = trim((string) ($this->request->getPost('hero_position') ?? '50% 50%'));
        $hero['scale']       = (float) ($this->request->getPost('hero_scale') ?? 1.0);

        $heroFile = $this->request->getFile('hero_image');
        if ($heroFile !== null && $heroFile->getName() !== '') {
            $validation = $this->validateHeroUpload($heroFile);
            if ($validation !== true) {
                return redirect()->back()->with('error', 'Hero upload failed: ' . $validation);
            }

            $newName = $heroFile->getRandomName();
            try {
                $uploadDir = childpro_gad_upload_dir();
                log_message('info', 'Uploading hero to: ' . $uploadDir . ' filename: ' . $newName);
                $heroFile->move($uploadDir, $newName);
                $hero['image'] = 'uploads/childpro-gad/' . $newName;
                log_message('info', 'Hero uploaded successfully: ' . $hero['image']);
            } catch (\Throwable $e) {
                log_message('error', 'CHILDPRO/GAD hero upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Could not save hero media. Error: ' . $e->getMessage() . ' - Check upload directory permissions.');
            }
        }

        // Remove hero image
        if ($this->request->getPost('remove_hero') === '1') {
            $hero['image'] = '';
        }

        childpro_gad_hero_save($tab, $hero);

        // --- Sections ---
        $sections = [];
        $sectionIds = $this->request->getPost('section_id');
        log_message('info', 'CHILDPRO/GAD Update - Tab: ' . $tab . ', Section IDs received: ' . print_r($sectionIds, true));
        
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
                $order              = (int) ($this->request->getPost('section_order')[$i] ?? $i);
                $removeMedia        = (string) ($this->request->getPost('section_remove_media')[$i] ?? '0');
                $sectionPosition    = trim((string) ($this->request->getPost('section_position')[$i] ?? '50% 50%'));
                $sectionScale       = (float) ($this->request->getPost('section_scale')[$i] ?? 1.0);
                
                log_message('info', "CHILDPRO/GAD Section $i - ID: $sectionId, Title: $sectionTitle, MediaURL: $mediaUrl");

                // Handle file upload for this section
                $sectionFile = $this->request->getFile('section_media_' . $sectionId);
                if ($sectionFile !== null && $sectionFile->getName() !== '') {
                    $validation = $this->validateSectionMedia($sectionFile);
                    if ($validation !== true) {
                        return redirect()->back()->with('error', 'Section "' . $sectionTitle . '": ' . $validation);
                    }

                    $newName = $sectionFile->getRandomName();
                    try {
                        $sectionFile->move(childpro_gad_upload_dir(), $newName);
                        $mediaUrl = 'uploads/childpro-gad/' . $newName;
                        $mediaType = 'image'; // uploaded files are images
                    } catch (\Throwable $e) {
                        log_message('error', 'CHILDPRO/GAD section upload failed: ' . $e->getMessage());
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

        childpro_gad_sections_save($tab, $sections);

        return redirect()->back()->with('success', childpro_gad_tab_label($tab) . ' page updated successfully.');
    }

    /**
     * @return true|string
     */
    private function validateHeroUpload(UploadedFile $file)
    {
        $fileName = $file->getName();
        $fileSize = $file->getSize();
        $fileExt = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        $fileMime = strtolower((string) $file->getMimeType());
        
        log_message('info', 'Validating hero upload: ' . $fileName . ' - Size: ' . $fileSize . ' - Extension: ' . $fileExt . ' - MIME: ' . $fileMime);

        if (! $file->isValid()) {
            $error = $file->getErrorString();
            log_message('error', 'Upload validation failed: ' . $error);
            return $error !== '' ? $error : 'Upload failed. File may be too large.';
        }

        $sizeMB = $fileSize / 1024 / 1024;

        // Max 300MB
        $maxSize = 300 * 1024 * 1024;
        $maxSizeMB = '300MB';

        if ($fileSize > $maxSize) {
            $msg = 'File size (' . number_format($sizeMB, 2) . 'MB) exceeds ' . $maxSizeMB . ' limit. Please compress your file or choose a smaller one.';
            log_message('error', $msg);
            return $msg;
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm', 'ogg', 'mov'];
        if (! in_array($fileExt, $allowedExt, true)) {
            $msg = 'Invalid file extension: ' . $fileExt . '. Allowed: JPG, PNG, WEBP, MP4, WebM, OGG, MOV';
            log_message('error', $msg);
            return $msg;
        }

        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/pjpeg',
            'image/x-png',
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime',
        ];
        if ($fileMime !== '' && ! in_array($fileMime, $allowedMime, true)) {
            $msg = 'Invalid MIME type: ' . $fileMime;
            log_message('error', $msg);
            return $msg;
        }

        log_message('info', 'Validation passed for: ' . $fileName . ' (' . number_format($sizeMB, 2) . 'MB)');
        return true;
    }

    /**
     * @return true|string
     */
    private function validateSectionMedia(UploadedFile $file)
    {
        $fileName = $file->getName();
        $fileSize = $file->getSize();
        $fileExt = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        
        log_message('info', 'Validating section media: ' . $fileName . ' - Size: ' . $fileSize . ' - Extension: ' . $fileExt);

        if (! $file->isValid()) {
            $error = $file->getErrorString();
            return $error !== '' ? $error : 'Upload failed. File may be too large.';
        }

        $sizeMB = $fileSize / 1024 / 1024;

        // Max 300MB for all media
        $maxSize = 300 * 1024 * 1024;
        $maxSizeMB = '300MB';

        if ($fileSize > $maxSize) {
            $msg = 'File size (' . number_format($sizeMB, 2) . 'MB) exceeds ' . $maxSizeMB . ' limit.';
            log_message('error', $msg);
            return $msg;
        }

        if (! in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'mp4'], true)) {
            $msg = 'Invalid file extension: ' . $fileExt . '. Allowed: JPG, PNG, WEBP, MP4';
            log_message('error', $msg);
            return $msg;
        }

        log_message('info', 'Section media validation passed for: ' . $fileName);
        return true;
    }
}