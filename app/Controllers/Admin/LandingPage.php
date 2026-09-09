<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Files\UploadedFile;

class LandingPage extends BaseController
{
    public function index()
    {
        helper(['landing', 'asset', 'admin_access']);

        landing_migrate_legacy_hero_uploads();

        $records = landing_hero_slide_objects();
        $slides = [];

        foreach ($records as $i => $record) {
            $slides[$i + 1] = [
                'path'     => $record['path'],
                'url'      => featured_poster_url($record['path']),
                'position' => $record['position'],
                'scale'    => $record['scale'],
            ];
        }

        return view('admin/landing_page', [
            'title'              => 'Landing Page — CSCS SMS',
            'slides'             => $slides,
            'announcementStrip'  => landing_announcement_strip_text(),
            'previewSlides'      => landing_hero_slides_for_view(),
            'landingSections'    => landing_sections_get(),
        ]);
    }

    public function update()
    {
        helper(['landing', 'admin_access']);

        landing_migrate_legacy_hero_uploads();

        if (! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        // Early detect if the POST/request was truncated due to php.ini limits
        // (e.g. upload_max_filesize or post_max_size). If so, abort and do not
        // modify saved slides to avoid clearing existing uploads.
        $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
        $postMax = ini_get('post_max_size');
        // parse shorthand byte value (K/M/G)
        $mult = 1;
        $last = strtolower(substr($postMax, -1));
        $num = (int) $postMax;
        if ($last === 'g') $mult = 1024 * 1024 * 1024;
        elseif ($last === 'm') $mult = 1024 * 1024;
        elseif ($last === 'k') $mult = 1024;
        $postMaxBytes = $num * $mult;
        if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
            return redirect()->back()->with('error', 'Upload failed: total request size exceeds server limit (post_max_size = ' . ini_get('post_max_size') . '). No changes were made.');
        }

        $existing = landing_hero_slide_objects();
        $slots    = [
            1 => $existing[0] ?? null,
            2 => $existing[1] ?? null,
            3 => $existing[2] ?? null,
        ];
        $uploadDir = landing_hero_upload_dir();

        for ($slot = 1; $slot <= 3; $slot++) {
            if ($this->request->getPost('remove_slide_' . $slot) === '1') {
                $slots[$slot] = null;
                continue;
            }

            $position = landing_normalize_slide_position((string) $this->request->getPost('hero_slide_' . $slot . '_position') ?? '50% 50%');
            $scale = landing_normalize_slide_scale($this->request->getPost('hero_slide_' . $slot . '_scale') ?? 1);

            $file = $this->request->getFile('hero_slide_' . $slot);
            if ($file === null || $file->getName() === '') {
                if (isset($slots[$slot]['path']) && is_string($slots[$slot]['path'])) {
                    $slots[$slot] = [
                        'path'     => $slots[$slot]['path'],
                        'position' => $position,
                        'scale'    => $scale,
                    ];
                }
                continue;
            }

            $validation = $this->validateHeroUpload($file);
            if ($validation !== true) {
                return redirect()->back()->with('error', 'Slide ' . $slot . ': ' . $validation);
            }

            $newName = $file->getRandomName();
            try {
                $file->move($uploadDir, $newName);
            } catch (\Throwable $e) {
                log_message('error', 'Landing hero upload failed: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Could not save slide ' . $slot . '. Check permissions on public/uploads/landing/.');
            }

            $slots[$slot] = [
                'path'     => 'uploads/landing/' . $newName,
                'position' => $position,
                'scale'    => $scale,
            ];
        }

        $cleanSlides = [];
        foreach ([1, 2, 3] as $slot) {
            if (isset($slots[$slot]['path']) && is_string($slots[$slot]['path']) && $slots[$slot]['path'] !== '') {
                $cleanSlides[] = $slots[$slot];
            }
        }

        landing_save_hero_slides($cleanSlides);

        $stripText = trim((string) $this->request->getPost('announcement_strip'));
        $stripText = preg_replace('/\s+/u', ' ', $stripText) ?? '';
        if (strlen($stripText) > 500) {
            $stripText = mb_substr($stripText, 0, 500);
        }

        $model = new \App\Models\SystemSettingModel();
        $model->setSetting(
            landing_strip_setting_key(),
            $stripText,
            'Landing page announcement ticker (principal message)'
        );

        // Lifelines: optional fields set by admin
        $lifelines = [
            'water' => strtoupper(trim((string) $this->request->getPost('lifeline_water') ?? 'FUNCTIONAL')),
            'communication' => strtoupper(trim((string) $this->request->getPost('lifeline_communication') ?? 'FUNCTIONAL')),
            'electricity' => strtoupper(trim((string) $this->request->getPost('lifeline_electricity') ?? 'FUNCTIONAL')),
        ];
        // Use helper to save
        try {
            helper('landing');
            landing_save_lifelines($lifelines);
        } catch (\Throwable $e) {
            // non-fatal; proceed
            log_message('warning', 'Could not save landing lifelines: ' . $e->getMessage());
        }

        // Footer QR Code: optional upload
        try {
            helper('landing');
            $removeQr = $this->request->getPost('remove_qr_code') === '1';
            $qrFile = $this->request->getFile('footer_qr_code');
            if ($removeQr) {
                landing_save_qr_code_path('');
            } elseif ($qrFile !== null && $qrFile->isValid() && $qrFile->getSize() > 0) {
                $validation = $this->validateQrUpload($qrFile);
                if ($validation !== true) {
                    return redirect()->back()->with('error', 'QR Code: ' . $validation);
                }

                $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
                if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0755, true) && ! is_dir($uploadDir)) {
                    throw new \RuntimeException('Cannot create upload directory: ' . $uploadDir);
                }

                $newName = 'qr-code-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $qrFile->getClientExtension();
                $qrFile->move($uploadDir, $newName);
                landing_save_qr_code_path('uploads/' . $newName);
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Could not save landing QR code: ' . $e->getMessage());
        }

        // Save landing content sections
        try {
            helper('landing');
            $sections = [];
            $sectionIds = $this->request->getPost('section_id');
            if (is_array($sectionIds)) {
                for ($i = 0; $i < count($sectionIds); $i++) {
                    if ($i >= 6) break;
                    $sectionId = trim((string) ($sectionIds[$i] ?? ''));
                    if ($sectionId === '') continue;
                    $sectionTitle = trim((string) ($this->request->getPost('section_title')[$i] ?? ''));
                    $sectionDesc = trim((string) ($this->request->getPost('section_description')[$i] ?? ''));
                    $mediaType = (string) ($this->request->getPost('section_media_type')[$i] ?? 'image');
                    $mediaUrl = trim((string) ($this->request->getPost('section_media_url')[$i] ?? ''));
                    $order = (int) ($this->request->getPost('section_order')[$i] ?? $i);
                    $sections[] = [
                        'id' => $sectionId,
                        'title' => $sectionTitle,
                        'description' => $sectionDesc,
                        'media_type' => $mediaType,
                        'media_url' => $mediaUrl,
                        'order' => $order,
                    ];
                }
            }
            landing_sections_save($sections);
        } catch (\Throwable $e) {
            log_message('warning', 'Could not save landing content sections: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Landing page updated successfully.');
    }

    /**
     * @return true|string
     */
    private function validateHeroUpload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            $error = $file->getErrorString();

            return $error !== '' ? $error : 'Upload failed. The file may be too large (max 50MB).';
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
    private function validateQrUpload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            $error = $file->getErrorString();

            return $error !== '' ? $error : 'Upload failed.';
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return 'QR code image must be 10MB or less.';
        }

        $ext = strtolower($file->getClientExtension() ?: $file->getExtension() ?: '');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return 'QR code must be JPG, PNG, or WEBP.';
        }

        $mime = strtolower((string) $file->getMimeType());
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/pjpeg', 'image/x-png'];
        if ($mime !== '' && ! in_array($mime, $allowedMime, true) && ! str_starts_with($mime, 'image/')) {
            return 'File must be an image (JPG, PNG, or WEBP).';
        }

        return true;
    }
}
