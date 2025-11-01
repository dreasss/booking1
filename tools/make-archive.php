<?php
/**
 * CLI utility to create a distributable ZIP archive of the application.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

require_once __DIR__ . '/Packager.php';

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Unable to determine project root.\n");
    exit(1);
}

try {
    $zipPath = Packager::createArchive($root);
    echo "Archive created at {$zipPath}\n";
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
