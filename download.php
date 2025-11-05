<?php
require_once __DIR__ . '/tools/Packager.php';

$root = __DIR__;
$distDir = $root . DIRECTORY_SEPARATOR . 'dist';

try {
    $zipPath = Packager::createArchive($root, $distDir);
} catch (Throwable $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Не удалось подготовить архив: ' . $exception->getMessage();
    exit;
}

if (!is_file($zipPath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Архив не найден.';
    exit;
}

$filename = basename($zipPath);

header('Content-Type: application/zip');
header('Content-Length: ' . filesize($zipPath));
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($zipPath);
exit;
