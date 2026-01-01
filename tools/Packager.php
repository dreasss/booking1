<?php
/**
 * Utility for building distributable archives of the project.
 */
class Packager
{
    /**
     * Create a ZIP archive of the project.
     *
     * @param string      $root        Absolute path to the project root.
     * @param string|null $distDir     Optional directory for generated archives.
     * @param string      $archiveName Name of the archive to create.
     * @param array|null  $exclude     Optional list of relative paths to exclude.
     *
     * @return string Full path to the generated archive.
     *
     * @throws InvalidArgumentException If the root path is invalid.
     * @throws RuntimeException         If the archive cannot be created.
     */
    public static function createArchive($root, $distDir = null, $archiveName = 'booking-system.zip', ?array $exclude = null)
    {
        if (!is_string($root) || $root === '' || !is_dir($root)) {
            throw new InvalidArgumentException('A valid project root is required.');
        }

        $root = realpath($root);
        if ($root === false) {
            throw new InvalidArgumentException('Unable to resolve project root.');
        }

        if ($distDir === null) {
            $distDir = $root . DIRECTORY_SEPARATOR . 'dist';
        }

        if ($exclude === null) {
            $exclude = [
                '.git',
                '.github',
                '.idea',
                '.vscode',
                'dist',
                'node_modules',
                'vendor',
            ];
        }

        if (!is_dir($distDir) && !mkdir($distDir, 0775, true) && !is_dir($distDir)) {
            throw new RuntimeException('Unable to create dist directory: ' . $distDir);
        }

        $zipPath = $distDir . DIRECTORY_SEPARATOR . $archiveName;

        if (file_exists($zipPath) && !unlink($zipPath)) {
            throw new RuntimeException('Unable to remove existing archive: ' . $zipPath);
        }

        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('The ZipArchive extension is required.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Unable to open archive for writing: ' . $zipPath);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            $relativePath = substr($fileInfo->getPathname(), strlen($root) + 1);

            foreach ($exclude as $ignored) {
                if ($relativePath === $ignored) {
                    continue 2;
                }

                $prefix = $ignored . DIRECTORY_SEPARATOR;
                if (strpos($relativePath, $prefix) === 0) {
                    continue 2;
                }
            }

            $zipPathName = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            if ($fileInfo->isDir()) {
                if (!$fileInfo->isReadable()) {
                    throw new RuntimeException('Unreadable directory: ' . $fileInfo->getPathname());
                }
                if (!$zip->addEmptyDir($zipPathName)) {
                    throw new RuntimeException('Unable to add directory to archive: ' . $zipPathName);
                }
            } else {
                if (!$fileInfo->isReadable()) {
                    throw new RuntimeException('Unreadable file: ' . $fileInfo->getPathname());
                }
                if (!$zip->addFile($fileInfo->getPathname(), $zipPathName)) {
                    throw new RuntimeException('Unable to add file to archive: ' . $zipPathName);
                }
            }
        }

        if (!$zip->close()) {
            throw new RuntimeException('Failed to write archive contents.');
        }

        return $zipPath;
    }
}
