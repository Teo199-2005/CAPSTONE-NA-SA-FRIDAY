<?php

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Setting keys
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_setting_key_hero')) {
    function programs_projects_setting_key_hero(string $tab): string
    {
        return 'programs_projects_hero_' . $tab;
    }
}

if (! function_exists('programs_projects_setting_key_sections')) {
    function programs_projects_setting_key_sections(string $tab): string
    {
        return 'programs_projects_sections_' . $tab;
    }
}

// ---------------------------------------------------------------------------
// Upload directory
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_upload_dir')) {
    function programs_projects_upload_dir(): string
    {
        // Try public/ subfolder first (Hostinger flat layout compatibility)
        $dir = FCPATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'programs-projects' . DIRECTORY_SEPARATOR;

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            // Fallback to root uploads directory
            $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'programs-projects' . DIRECTORY_SEPARATOR;
            if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
                throw new \RuntimeException('Cannot create upload directory.');
            }
        }

        return $dir;
    }
}

// ---------------------------------------------------------------------------
// Hero get/save
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_hero_get')) {
    /**
     * @return array{image: string, title: string, description: string, position: string, scale: float}
     */
    function programs_projects_hero_get(string $tab): array
    {
        $default = [
            'image'       => '',
            'title'       => '',
            'description' => '',
            'position'    => '50% 50%',
            'scale'       => 1.0,
        ];

        try {
            $model = new \App\Models\SystemSettingModel();
            $raw   = (string) ($model->getSetting(programs_projects_setting_key_hero($tab), '') ?? '');
        } catch (\Throwable $e) {
            return $default;
        }

        if ($raw === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return $default;
        }

        $position = isset($decoded['position']) ? $decoded['position'] : '50% 50%';
        $scale    = isset($decoded['scale']) ? $decoded['scale'] : 1.0;

        return [
            'image'       => is_string($decoded['image'] ?? '') ? $decoded['image'] : '',
            'title'       => is_string($decoded['title'] ?? '') ? $decoded['title'] : '',
            'description' => is_string($decoded['description'] ?? '') ? $decoded['description'] : '',
            'position'    => is_string($position) ? $position : '50% 50%',
            'scale'       => is_numeric($scale) ? (float) $scale : 1.0,
        ];
    }
}

if (! function_exists('programs_projects_hero_save')) {
    /**
     * @param array{image: string, title: string, description: string, position: string, scale: float} $data
     */
    function programs_projects_hero_save(string $tab, array $data): void
    {
        $clean = [
            'image'       => is_string($data['image'] ?? '') ? $data['image'] : '',
            'title'       => is_string($data['title'] ?? '') ? $data['title'] : '',
            'description' => is_string($data['description'] ?? '') ? $data['description'] : '',
            'position'    => is_string($data['position'] ?? '50% 50%') ? $data['position'] : '50% 50%',
            'scale'       => is_numeric($data['scale'] ?? 1.0) ? (float) $data['scale'] : 1.0,
        ];

        $model = new \App\Models\SystemSettingModel();
        $model->setSetting(
            programs_projects_setting_key_hero($tab),
            json_encode($clean, JSON_UNESCAPED_SLASHES),
            'Programs/Projects hero section for tab: ' . $tab
        );
    }
}

// ---------------------------------------------------------------------------
// Sections get/save
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_sections_get')) {
    /**
     * @return list<array{id: string, title: string, description: string, media_type: string, media_url: string, order: int, position: string, scale: float}>
     */
    function programs_projects_sections_get(string $tab): array
    {
        try {
            $model = new \App\Models\SystemSettingModel();
            $raw   = (string) ($model->getSetting(programs_projects_setting_key_sections($tab), '') ?? '');
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

            $position = isset($item['position']) ? $item['position'] : '50% 50%';
            $scale    = isset($item['scale']) ? $item['scale'] : 1.0;

            $sections[] = [
                'id'          => is_string($item['id'] ?? '') ? $item['id'] : '',
                'title'       => is_string($item['title'] ?? '') ? $item['title'] : '',
                'description' => is_string($item['description'] ?? '') ? $item['description'] : '',
                'media_type'  => in_array($item['media_type'] ?? '', ['image', 'video'], true) ? $item['media_type'] : 'image',
                'media_url'   => is_string($item['media_url'] ?? '') ? $item['media_url'] : '',
                'order'       => is_numeric($item['order'] ?? '') ? (int) $item['order'] : 0,
                'position'    => is_string($position) ? $position : '50% 50%',
                'scale'       => is_numeric($scale) ? (float) $scale : 1.0,
            ];
        }

        // Sort by order
        usort($sections, static fn (array $a, array $b): int => $a['order'] <=> $b['order']);

        return $sections;
    }
}

if (! function_exists('programs_projects_sections_save')) {
    /**
     * @param list<array{id: string, title: string, description: string, media_type: string, media_url: string, order: int, position: string, scale: float}> $sections
     */
    function programs_projects_sections_save(string $tab, array $sections): void
    {
        $clean = [];
        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $position = isset($section['position']) ? $section['position'] : '50% 50%';
            $scale    = isset($section['scale']) ? $section['scale'] : 1.0;

            $clean[] = [
                'id'          => is_string($section['id'] ?? '') ? $section['id'] : '',
                'title'       => is_string($section['title'] ?? '') ? $section['title'] : '',
                'description' => is_string($section['description'] ?? '') ? $section['description'] : '',
                'media_type'  => in_array($section['media_type'] ?? '', ['image', 'video'], true) ? $section['media_type'] : 'image',
                'media_url'   => is_string($section['media_url'] ?? '') ? $section['media_url'] : '',
                'order'       => is_numeric($section['order'] ?? '') ? (int) $section['order'] : 0,
                'position'    => is_string($position) ? $position : '50% 50%',
                'scale'       => is_numeric($scale) ? (float) $scale : 1.0,
            ];
        }

        $model = new \App\Models\SystemSettingModel();
        $model->setSetting(
            programs_projects_setting_key_sections($tab),
            json_encode(array_values($clean), JSON_UNESCAPED_SLASHES),
            'Programs/Projects content sections for tab: ' . $tab
        );
    }
}

// ---------------------------------------------------------------------------
// Helper: media URL helpers
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_media_url')) {
    function programs_projects_media_url(string $path): string
    {
        if ($path === '') {
            return '';
        }

        // If it's already a full URL
        if (preg_match('#^https?://#', $path) === 1) {
            return $path;
        }

        $relative = ltrim(str_replace('\\', '/', $path), '/');
        if ($relative === '') {
            return '';
        }

        helper('asset');

        // Check if file exists on disk (supports flat and public/ layouts)
        $diskPath = programs_projects_disk_path($relative);
        if ($diskPath !== null) {
            return asset_url($relative);
        }

        // Try anyway if it matches the expected pattern
        if (preg_match('#^uploads/programs-projects/[a-zA-Z0-9._-]+$#', $relative) === 1) {
            return asset_url($relative);
        }

        return '';
    }
}

if (! function_exists('programs_projects_disk_path')) {
    /**
     * Resolve a Programs/Projects upload path to an absolute disk path.
     * Supports flat layout (FCPATH/uploads/...) and public/ subfolder layout.
     */
    function programs_projects_disk_path(string $relativePath): ?string
    {
        $relative = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relative === '') {
            return null;
        }

        $candidates = [];
        if (defined('FCPATH')) {
            $candidates[] = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $candidates[] = FCPATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        }
        if (defined('ROOTPATH')) {
            $candidates[] = rtrim(ROOTPATH, DIRECTORY_SEPARATOR . '/\\')
                . DIRECTORY_SEPARATOR . 'public'
                . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        }

        foreach ($candidates as $file) {
            if (is_file($file)) {
                return $file;
            }
        }

        return null;
    }
}

if (! function_exists('programs_projects_is_youtube_url')) {
    function programs_projects_is_youtube_url(string $url): bool
    {
        return preg_match('#(?:youtube\.com|youtu\.be)#i', $url) === 1;
    }
}

if (! function_exists('programs_projects_youtube_embed_url')) {
    function programs_projects_youtube_embed_url(string $url): string
    {
        // Extract video ID
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $url;
    }
}

// ---------------------------------------------------------------------------
// Supported tabs
// ---------------------------------------------------------------------------

if (! function_exists('programs_projects_tabs')) {
    /**
     * @return list<string>
     */
    function programs_projects_tabs(): array
    {
        return ['programs', 'projects'];
    }
}

if (! function_exists('programs_projects_tab_label')) {
    function programs_projects_tab_label(string $tab): string
    {
        return match ($tab) {
            'programs' => 'Programs',
            'projects' => 'Projects',
            default    => strtoupper($tab),
        };
    }
}