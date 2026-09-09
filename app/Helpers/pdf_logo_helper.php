<?php

declare(strict_types=1);

if (! function_exists('school_logo_path_candidates')) {
    /**
     * @return list<string>
     */
    function school_logo_path_candidates(): array
    {
        $paths = [];

        if (defined('FCPATH')) {
            $paths[] = FCPATH . 'LPHS2.png';
            $paths[] = FCPATH . 'public' . DIRECTORY_SEPARATOR . 'LPHS2.png';
        }

        if (defined('ROOTPATH')) {
            $paths[] = rtrim(ROOTPATH, DIRECTORY_SEPARATOR . '/\\')
                . DIRECTORY_SEPARATOR . 'public'
                . DIRECTORY_SEPARATOR . 'LPHS2.png';
        }

        if (defined('APPPATH')) {
            $paths[] = dirname(rtrim(APPPATH, DIRECTORY_SEPARATOR . '/\\'))
                . DIRECTORY_SEPARATOR . 'public'
                . DIRECTORY_SEPARATOR . 'LPHS2.png';
        }

        $seen = [];
        $out  = [];
        foreach ($paths as $p) {
            if ($p === '' || isset($seen[$p])) {
                continue;
            }
            $seen[$p] = true;
            $out[]    = $p;
        }

        return $out;
    }
}

if (! function_exists('school_logo_base64')) {
    /**
     * PNG file contents as base64 for Dompdf data URIs (no GD required).
     * Supports standard CI4 (logo in public/) and flat Hostinger (public_html + public/).
     */
    function school_logo_base64(): string
    {
        foreach (school_logo_path_candidates() as $path) {
            if (! is_file($path) || ! is_readable($path)) {
                continue;
            }
            $binary = @file_get_contents($path);
            if ($binary !== false && $binary !== '') {
                return base64_encode($binary);
            }
        }

        return '';
    }
}
