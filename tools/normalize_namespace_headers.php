<?php

/**
 * Ensure <?php is immediately followed by namespace (no blank line) for pure PHP files.
 * Skips files where declare(strict_types) appears before namespace.
 */
$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Cannot resolve project root\n");
    exit(1);
}

$changed = [];
$scanned = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . DIRECTORY_SEPARATOR . 'app', FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    if (str_contains(str_replace('\\', '/', $path), '/app/Views/')) {
        continue;
    }

    $scanned++;
    $data = file_get_contents($path);
    if ($data === false) {
        continue;
    }

    $original = $data;

    if (strncmp($data, "\xEF\xBB\xBF", 3) === 0) {
        $data = substr($data, 3);
    }

    // Skip if declare comes before namespace (PSR-12 / strict headers).
    if (preg_match('/^<\?php\s*[\r\n]+\s*declare\s*\(/s', $data)) {
        if ($data !== $original) {
            file_put_contents($path, $data);
            $changed[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path) . ' (BOM only)';
        }
        continue;
    }

    // Collapse: <?php [line breaks / whitespace] namespace -> <?php\nnamespace
    $new = preg_replace(
        '/^<\?php(\s*)[\r\n]+(?:\s*[\r\n]+)+(\s*namespace\s+)/s',
        "<?php$1\n$2",
        $data,
        1,
        $count
    );

    if ($count > 0) {
        $data = $new;
    }

    if ($data !== $original) {
        file_put_contents($path, $data);
        $changed[] = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
    }
}

foreach ($changed as $rel) {
    echo $rel, PHP_EOL;
}

echo PHP_EOL, 'Scanned: ', $scanned, PHP_EOL;
echo 'Updated: ', count($changed), PHP_EOL;
