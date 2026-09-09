<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\MaterialModel;

/**
 * Public school materials (handbook, rules, etc.) — no login required.
 */
class SchoolMaterials extends BaseController
{
    protected MaterialModel $materialModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
    }

    public function preview(int $id)
    {
        helper('materials');

        $material = $this->materialModel->findWebsiteMaterial($id);
        if ($material === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $disk = material_file_disk_path($material);
        if ($disk === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found');
        }

        $mime = mime_content_type($disk) ?: 'application/octet-stream';
        $type = strtolower((string) $material->file_type);

        if ($type === 'pdf') {
            $mime = 'application/pdf';
        }

        $inline = material_can_preview_inline($type);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $this->safeFilename($material) . '"')
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setBody(file_get_contents($disk));
    }

    public function download(int $id)
    {
        helper('materials');

        $material = $this->materialModel->findWebsiteMaterial($id);
        if ($material === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $disk = material_file_disk_path($material);
        if ($disk === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found');
        }

        return $this->response->download($disk, null)->setFileName($this->safeFilename($material));
    }

    private function safeFilename(object $material): string
    {
        $name = (string) ($material->file_name ?? 'document');
        $name = preg_replace('/[^\w.\- ()]/u', '_', $name) ?? 'document';

        return $name !== '' ? $name : 'document';
    }
}
