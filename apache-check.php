<?php
/**
 * Temporary Apache diagnostic — delete after fixing local URLs.
 * Open: http://localhost/public_html/apache-check.php
 */
header('Content-Type: text/plain; charset=utf-8');
echo "Apache is serving THIS folder:\n";
echo realpath(__DIR__) . "\n\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '(none)') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? '(none)') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '(none)') . "\n";
echo "\nIf this file 404s, XAMPP is pointed at a different folder than your project.\n";
echo "Expected folder name may include: public_html (4)\n";
