<?php

/**
 * Remove leading UTF-8 BOM from PHP entrypoints (avoids "namespace must be first" fatals on PHP 8+).
 */
$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Cannot resolve project root\n");
    exit(1);
}

$stripped = [];
$scanned  = 0;

$paths = [];

// All PHP under app/ except Views (views may legally start with HTML).
$appIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . DIRECTORY_SEPARATOR . 'app', FilesystemIterator::SKIP_DOTS)
);
foreach ($appIterator as $file) {
    if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $full = $file->getPathname();
    if (str_contains(str_replace('\\', '/', $full), '/app/Views/')) {
        continue;
    }
    $paths[] = $full;
}

// Common project entrypoints at repo root.
foreach (['public/index.php', 'spark', 'preload.php'] as $rel) {
    $p = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($p)) {
        $paths[] = $p;
    }
}

// Root-level *.php one-off scripts (skip vendor / archived tree).
$rootPhp = glob($root . DIRECTORY_SEPARATOR . '*.php') ?: [];
foreach ($rootPhp as $p) {
    $paths[] = $p;
}

$paths = array_values(array_unique($paths));

foreach ($paths as $path) {
    $scanned++;
    $data = file_get_contents($path);
    if ($data === false) {
        continue;
    }
    if (strncmp($data, "\xEF\xBB\xBF", 3) !== 0) {
        continue;
    }
    $new = substr($data, 3);
    if (file_put_contents($path, $new) === false) {
        fwrite(STDERR, "Write failed: {$path}\n");
        continue;
    }
    $stripped[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
}

foreach ($stripped as $rel) {
    echo $rel, PHP_EOL;
}

echo PHP_EOL, 'Scanned: ', $scanned, PHP_EOL;
echo 'Stripped BOM: ', count($stripped), PHP_EOL;
