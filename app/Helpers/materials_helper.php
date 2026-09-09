<?php

declare(strict_types=1);

if (! function_exists('material_category_options')) {
    /**
     * @return array<string, string>
     */
    function material_category_options(): array
    {
        return [
            'handbook'          => 'Student Handbook',
            'rules'             => 'School Rules & Policies',
            'learning_material' => 'Learning Material',
            'form'              => 'Forms & Documents',
            'policy'            => 'Memorandum / Policy',
            'other'             => 'Other',
        ];
    }
}

if (! function_exists('material_category_label')) {
    function material_category_label(?string $key): string
    {
        $key = trim((string) $key);
        if ($key === '') {
            return 'Uncategorized';
        }

        $options = material_category_options();

        return $options[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}

if (! function_exists('material_file_disk_path')) {
    function material_file_disk_path(object|array $material): ?string
    {
        $path = is_array($material) ? ($material['file_path'] ?? '') : ($material->file_path ?? '');
        if ($path === '') {
            return null;
        }

        $full = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);

        return is_file($full) ? $full : null;
    }
}

if (! function_exists('material_file_icon')) {
    function material_file_icon(?string $fileType): string
    {
        $type = strtolower((string) $fileType);

        return match ($type) {
            'pdf'        => 'file-earmark-pdf',
            'doc', 'docx' => 'file-earmark-word',
            'xls', 'xlsx' => 'file-earmark-excel',
            'ppt', 'pptx' => 'file-earmark-ppt',
            'jpg', 'jpeg', 'png', 'webp', 'gif' => 'file-earmark-image',
            'zip', 'rar'  => 'file-earmark-zip',
            default      => 'file-earmark-text',
        };
    }
}

if (! function_exists('material_can_preview_inline')) {
    function material_can_preview_inline(?string $fileType): bool
    {
        $type = strtolower((string) $fileType);

        return in_array($type, ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}

if (! function_exists('material_format_size')) {
    function material_format_size($bytes): string
    {
        $bytes = (int) $bytes;
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / 1048576, 2) . ' MB';
    }
}

if (! function_exists('public_website_materials')) {
    /**
     * School materials shown on the public website modal.
     *
     * @return list<object>
     */
    function public_website_materials(): array
    {
        try {
            $model = new \App\Models\MaterialModel();

            return $model->getWebsiteMaterials();
        } catch (\Throwable $e) {
            log_message('error', 'public_website_materials: ' . $e->getMessage());

            return [];
        }
    }
}

if (! function_exists('material_preview_url')) {
    function material_preview_url(int $id): string
    {
        return base_url('school-materials/preview/' . $id);
    }
}

if (! function_exists('material_download_url')) {
    function material_download_url(int $id): string
    {
        return base_url('school-materials/download/' . $id);
    }
}
