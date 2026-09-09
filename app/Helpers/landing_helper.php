<?php

declare(strict_types=1);

if (! function_exists('landing_hero_setting_key')) {
    function landing_hero_setting_key(): string
    {
        return 'landing_hero_slides';
    }
}

if (! function_exists('landing_strip_setting_key')) {
    function landing_strip_setting_key(): string
    {
        return 'landing_announcement_strip';
    }
}

if (! function_exists('landing_hero_upload_dir')) {
    /**
     * Writable directory for hero uploads (FCPATH is already the public web root).
     */
    function landing_hero_upload_dir(): string
    {
        $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR;

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Cannot create upload directory: ' . $dir);
        }

        return $dir;
    }
}

if (! function_exists('landing_migrate_legacy_hero_uploads')) {
    /**
     * Move files from mistaken public/public/uploads/landing to public/uploads/landing.
     */
    function landing_migrate_legacy_hero_uploads(): void
    {
        if (! defined('FCPATH')) {
            return;
        }

        $legacyDir = FCPATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'landing';
        $targetDir = landing_hero_upload_dir();

        if (! is_dir($legacyDir)) {
            return;
        }

        foreach (glob($legacyDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (! is_file($file)) {
                continue;
            }
            $name = basename($file);
            $dest = $targetDir . $name;
            if (is_file($dest)) {
                continue;
            }
            @rename($file, $dest);
        }
    }
}

if (! function_exists('landing_hero_slide_paths')) {
    /**
     * Stored relative paths (max 3), e.g. uploads/landing/abc.jpg
     *
     * @return list<string>
     */
    function landing_hero_slide_paths(): array
    {
        return array_map(static fn (array $slide): string => $slide['path'], landing_hero_slide_objects());
    }
}

if (! function_exists('landing_hero_slide_objects')) {
    /**
     * @return list<array{path: string, position: string, scale: float}>
     */
    function landing_hero_slide_objects(): array
    {
        helper('asset');

        try {
            $model = new \App\Models\SystemSettingModel();
            $raw   = (string) ($model->getSetting(landing_hero_setting_key(), '') ?? '');
        } catch (\Throwable $e) {
            return [];
        }

        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        $slides = [];
        foreach ($decoded as $item) {
            $position = '50% 50%';
            $scale = 1.0;
            $path = null;

            if (is_string($item)) {
                $path = $item;
            } elseif (is_array($item) && isset($item['path']) && is_string($item['path'])) {
                $path = $item['path'];
                if (isset($item['position']) && is_string($item['position'])) {
                    $position = landing_normalize_slide_position($item['position']);
                }
                if (isset($item['objectPosition']) && is_string($item['objectPosition'])) {
                    $position = landing_normalize_slide_position($item['objectPosition']);
                }

                if (isset($item['scale']) || isset($item['zoom'])) {
                    $scale = landing_normalize_slide_scale($item['scale'] ?? $item['zoom']);
                }
            }

            if (! is_string($path) || trim($path) === '') {
                continue;
            }

            $relative = ltrim(str_replace('\\', '/', $path), '/');
            if (preg_match('#^uploads/landing/[a-zA-Z0-9._-]+$#', $relative) !== 1) {
                continue;
            }

            if (featured_poster_disk_path($relative) === null) {
                continue;
            }

            $slides[] = [
                'path'     => $relative,
                'position' => landing_normalize_slide_position($position),
                'scale'    => landing_normalize_slide_scale($scale),
            ];

            if (count($slides) >= 3) {
                break;
            }
        }

        return $slides;
    }
}

if (! function_exists('landing_save_hero_slides')) {
    /**
     * @param list<array<string,mixed>|string> $slides
     */
    function landing_save_hero_slides(array $slides): void
    {
        $clean = [];
        foreach ($slides as $slide) {
            $position = '50% 50%';
            $scale = 1.0;
            $path = null;

            if (is_string($slide)) {
                $path = $slide;
            } elseif (is_array($slide) && isset($slide['path']) && is_string($slide['path'])) {
                $path = $slide['path'];
                if (isset($slide['position']) && is_string($slide['position'])) {
                    $position = landing_normalize_slide_position($slide['position']);
                }
                if (isset($slide['objectPosition']) && is_string($slide['objectPosition'])) {
                    $position = landing_normalize_slide_position($slide['objectPosition']);
                }
                if (isset($slide['scale']) || isset($slide['zoom'])) {
                    $scale = landing_normalize_slide_scale($slide['scale'] ?? $slide['zoom']);
                }
            }

            if (! is_string($path) || trim($path) === '') {
                continue;
            }

            $relative = ltrim(str_replace('\\', '/', $path), '/');
            if (preg_match('#^uploads/landing/[a-zA-Z0-9._-]+$#', $relative) !== 1) {
                continue;
            }

            $clean[] = [
                'path'     => $relative,
                'position' => landing_normalize_slide_position($position),
                'scale'    => landing_normalize_slide_scale($scale),
            ];

            if (count($clean) >= 3) {
                break;
            }
        }

        $model = new \App\Models\SystemSettingModel();
        $model->setSetting(
            landing_hero_setting_key(),
            json_encode(array_values($clean), JSON_UNESCAPED_SLASHES),
            'Landing page hero slideshow images (max 3)'
        );
    }
}

if (! function_exists('landing_save_hero_slide_paths')) {
    /**
     * @param list<string> $paths
     */
    function landing_save_hero_slide_paths(array $paths): void
    {
        $slides = [];
        foreach ($paths as $path) {
            if (! is_string($path) || trim($path) === '') {
                continue;
            }
            $slides[] = ['path' => $path];
            if (count($slides) >= 3) {
                break;
            }
        }

        landing_save_hero_slides($slides);
    }
}

if (! function_exists('landing_hero_slides_for_view')) {
    /**
     * @return list<array{url: string, alt: string, position: string, scale: float}>
     */
    function landing_hero_slides_for_view(): array
    {
        $records = landing_hero_slide_objects();
        helper('asset');

        $slides = [];
        $primary = hero_banner_url();
        if ($primary !== '') {
            $slides[] = [
                'url' => $primary,
                'alt' => 'Cauayan South Central School — primary hero banner',
                'position' => '50% 50%',
                'scale' => 1.0,
            ];
        }

        foreach ($records as $record) {
            if (count($slides) >= 4) {
                break;
            }

            $url = featured_poster_url($record['path']);
            if ($url === '' || $url === $primary) {
                continue;
            }

            $slides[] = [
                'url' => $url,
                'alt' => 'Cauayan South Central School — hero slide ' . (count($slides) + 1),
                'position' => $record['position'],
                'scale' => $record['scale'],
            ];
        }

        if ($slides === []) {
            $slides[] = [
                'url' => $primary,
                'alt' => 'Cauayan South Central School — CSCS Tap n Track',
                'position' => '50% 50%',
                'scale' => 1.0,
            ];
        }

        return $slides;
    }
}

if (! function_exists('landing_normalize_slide_position')) {
    function landing_normalize_slide_position(string $position): string
    {
        $position = trim(preg_replace('/\s+/u', ' ', $position));
        if (preg_match('/^\s*(\d{1,3})%\s+(\d{1,3})%\s*$/', $position, $matches)) {
            $x = min(100, max(0, (int) $matches[1]));
            $y = min(100, max(0, (int) $matches[2]));
            return $x . '% ' . $y . '%';
        }

        return '50% 50%';
    }
}

if (! function_exists('landing_normalize_slide_scale')) {
    function landing_normalize_slide_scale($scale): float
    {
        if (! is_numeric($scale)) {
            return 1.0;
        }

        $scale = (float) $scale;
        if ($scale < 1.0) {
            return 1.0;
        }
        if ($scale > 2.5) {
            return 2.5;
        }

        return $scale;
    }
}

if (! function_exists('landing_announcement_strip_text')) {
    function landing_announcement_strip_text(): string
    {
        try {
            $model = new \App\Models\SystemSettingModel();
            $text  = (string) ($model->getSetting(landing_strip_setting_key(), '') ?? '');
        } catch (\Throwable $e) {
            return '';
        }

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }
}

if (! function_exists('landing_announcement_strip_html')) {
    /**
     * Return announcement strip text with URLs converted to clickable links.
     * Other HTML is escaped for safety. Links open in a new tab.
     */
    function landing_announcement_strip_html(): string
    {
        $text = landing_announcement_strip_text();
        if ($text === '') {
            return '';
        }

        // plain-text text: URLs become links, everything else stays as-is
        $pattern = '/(https?:\/\/[^\s<>"\']+)/i';
        $html = preg_replace_callback($pattern, function ($m) {
            $url = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" style="color: #fbbf24; text-decoration: underline; text-underline-offset: 2px;">' . $url . '</a>';
        }, $text);

        // IMPORTANT: we already built safe HTML (only <br> if user typed it, and our own <a> tags)
        // so we must NOT pass it through esc() again, otherwise angle brackets get encoded.
        return (string) $html;
    }
}

if (! function_exists('landing_strip_enabled')) {
    function landing_strip_enabled(): bool
    {
        return landing_announcement_strip_text() !== '';
    }
}

if (! function_exists('landing_lifelines')) {
    /**
     * Return associative lifeline statuses.
     * Keys: water, communication, electricity
     */
    function landing_lifelines(): array
    {
        try {
            $model = new \App\Models\SystemSettingModel();
            $water = (string) ($model->getSetting('landing_lifeline_water', 'FUNCTIONAL') ?? 'FUNCTIONAL');
            $comm  = (string) ($model->getSetting('landing_lifeline_communication', 'FUNCTIONAL') ?? 'FUNCTIONAL');
            $elec  = (string) ($model->getSetting('landing_lifeline_electricity', 'FUNCTIONAL') ?? 'FUNCTIONAL');
        } catch (\Throwable $e) {
            return [
                'water' => 'FUNCTIONAL',
                'communication' => 'FUNCTIONAL',
                'electricity' => 'FUNCTIONAL',
            ];
        }

        return [
            'water' => trim($water) === '' ? 'FUNCTIONAL' : strtoupper(trim($water)),
            'communication' => trim($comm) === '' ? 'FUNCTIONAL' : strtoupper(trim($comm)),
            'electricity' => trim($elec) === '' ? 'FUNCTIONAL' : strtoupper(trim($elec)),
        ];
    }
}

if (! function_exists('landing_save_lifelines')) {
    /**
     * Persist lifeline statuses (expects associative array with keys water, communication, electricity)
     * Values should be stored as uppercase strings like 'FUNCTIONAL' or 'NOT FUNCTIONAL'.
     */
    function landing_save_lifelines(array $lifelines): void
    {
        $model = new \App\Models\SystemSettingModel();
        $water = isset($lifelines['water']) ? (string) $lifelines['water'] : 'FUNCTIONAL';
        $comm  = isset($lifelines['communication']) ? (string) $lifelines['communication'] : 'FUNCTIONAL';
        $elec  = isset($lifelines['electricity']) ? (string) $lifelines['electricity'] : 'FUNCTIONAL';

        $model->setSetting('landing_lifeline_water', strtoupper(trim($water)), 'Landing page lifeline: water');
        $model->setSetting('landing_lifeline_communication', strtoupper(trim($comm)), 'Landing page lifeline: communication');
        $model->setSetting('landing_lifeline_electricity', strtoupper(trim($elec)), 'Landing page lifeline: electricity');
    }
}

if (! function_exists('landing_qr_code_path')) {
    function landing_qr_code_path(): string
    {
        try {
            $model = new \App\Models\SystemSettingModel();
            $path = (string) ($model->getSetting('landing_qr_code', '') ?? '');
        } catch (\Throwable $e) {
            return '';
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '' || preg_match('#^uploads/[a-zA-Z0-9._/-]+$#', $path) !== 1) {
            return '';
        }

        $full = FCPATH . $path;
        if (! is_file($full)) {
            return '';
        }

        return $path;
    }
}

if (! function_exists('landing_qr_code_url')) {
    function landing_qr_code_url(): string
    {
        $path = landing_qr_code_path();
        if ($path === '') {
            return '';
        }

        return asset_url($path);
    }
}

if (! function_exists('landing_save_qr_code_path')) {
    function landing_save_qr_code_path(?string $relativePath): void
    {
        $model = new \App\Models\SystemSettingModel();
        if ($relativePath === null || trim($relativePath) === '') {
            $model->setSetting('landing_qr_code', '', 'Landing page footer QR code');
            return;
        }

        $relative = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (preg_match('#^uploads/[a-zA-Z0-9._/-]+$#', $relative) !== 1) {
            return;
        }

        $full = FCPATH . $relative;
        if (! is_file($full)) {
            return;
        }

        $model->setSetting('landing_qr_code', $relative, 'Landing page footer QR code');
    }
}

// ---------------------------------------------------------------------------
// Landing page content sections
// ---------------------------------------------------------------------------

if (! function_exists('landing_sections_get')) {
    function landing_sections_get(): array
    {
        try {
            $model = new \App\Models\SystemSettingModel();
            $raw   = (string) ($model->getSetting('landing_content_sections', '') ?? '');
        } catch (\Throwable $e) {
            return [];
        }

        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        $sections = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }

            $sections[] = [
                'id'          => is_string($item['id'] ?? '') ? $item['id'] : '',
                'title'       => is_string($item['title'] ?? '') ? $item['title'] : '',
                'description' => is_string($item['description'] ?? '') ? $item['description'] : '',
                'media_type'  => in_array($item['media_type'] ?? '', ['image', 'video'], true) ? $item['media_type'] : 'image',
                'media_url'   => is_string($item['media_url'] ?? '') ? $item['media_url'] : '',
                'order'       => is_numeric($item['order'] ?? '') ? (int) $item['order'] : 0,
            ];
        }

        usort($sections, static fn (array $a, array $b): int => $a['order'] <=> $b['order']);

        return $sections;
    }
}

if (! function_exists('landing_sections_save')) {
    function landing_sections_save(array $sections): void
    {
        $clean = [];
        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $clean[] = [
                'id'          => is_string($section['id'] ?? '') ? $section['id'] : '',
                'title'       => is_string($section['title'] ?? '') ? $section['title'] : '',
                'description' => is_string($section['description'] ?? '') ? $section['description'] : '',
                'media_type'  => in_array($section['media_type'] ?? '', ['image', 'video'], true) ? $section['media_type'] : 'image',
                'media_url'   => is_string($section['media_url'] ?? '') ? $section['media_url'] : '',
                'order'       => is_numeric($section['order'] ?? '') ? (int) $section['order'] : 0,
            ];
        }

        $model = new \App\Models\SystemSettingModel();
        $model->setSetting(
            'landing_content_sections',
            json_encode(array_values($clean), JSON_UNESCAPED_SLASHES),
            'Landing page content sections'
        );
    }
}

if (! function_exists('landing_section_media_url')) {
    function landing_section_media_url(string $path): string
    {
        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#', $path) === 1) {
            return $path;
        }

        $relative = ltrim(str_replace('\\', '/', $path), '/');
        if ($relative === '') {
            return '';
        }

        helper('asset');

        $candidates = [];
        if (defined('FCPATH')) {
            $candidates[] = FCPATH . $relative;
            $candidates[] = FCPATH . 'public' . DIRECTORY_SEPARATOR . $relative;
        }
        if (defined('ROOTPATH')) {
            $candidates[] = rtrim(ROOTPATH, DIRECTORY_SEPARATOR . '/\\') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relative;
        }

        foreach ($candidates as $file) {
            if (is_file($file)) {
                return asset_url($relative);
            }
        }

        if (preg_match('#^uploads/landing/[a-zA-Z0-9._-]+$#', $relative) === 1) {
            return asset_url($relative);
        }

        return '';
    }
}
