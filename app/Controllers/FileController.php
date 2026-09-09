<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class FileController extends Controller
{
    public function show($filename)
    {
        // Try different upload directories in order of preference
        $paths = [
            // public/ subfolder (Hostinger flat layout - modern)
            FCPATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filename,
            // Root uploads (legacy)
            WRITEPATH . 'uploads/enrollment/' . $filename,
            WRITEPATH . 'uploads/' . $filename,
            WRITEPATH . 'uploads/enrollment_documents/' . $filename,
            FCPATH . 'uploads/enrollment_documents/' . $filename,
            FCPATH . 'uploads/' . $filename
        ];
        
        $filePath = null;
        foreach ($paths as $path) {
            if (is_file($path)) {
                $filePath = $path;
                break;
            }
        }
        
        if (!$filePath) {
            // Log the attempted paths for debugging
            log_message('error', 'File not found: ' . $filename . '. Tried paths: ' . implode(', ', $paths));
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($filePath);
        
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setBody(file_get_contents($filePath));
    }
}