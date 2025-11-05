<?php
if (defined('ROOT_PATH')) {
    $ok_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m6.5 11.2-2.3-2.3 1.06-1.06L6.5 9.08l4.24-4.24 1.06 1.06z"/></svg></span>';
    $fail_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m8 6.94 3.3-3.3 1.06 1.06L9.06 8l3.3 3.3-1.06 1.06L8 9.06l-3.3 3.3-1.06-1.06L6.94 8l-3.3-3.3L4.7 3.64 8 6.94z"/></svg></span>';
    echo '<h2>'.install_text('Проверка прав перед обновлением', 'Verify permissions before upgrade').'</h2>';
    echo '<p>'.install_text('Каталоги и файлы ниже должны существовать и быть доступными для записи.', 'The following folders and files must exist and be writable.').'</p>';
    echo '<ul>';
    $error = false;
    $folders = [
        ROOT_PATH.'datas/',
        ROOT_PATH.'settings/',
        ROOT_PATH.'datas/cache/',
        ROOT_PATH.'datas/logs/',
        ROOT_PATH.'datas/images/'
    ];
    foreach ($folders as $folder) {
        makeDirectory($folder, 0755);
        $isWritable = is_writable($folder);
        $statusClass = $isWritable ? 'correct' : 'incorrect';
        $icon = $isWritable ? $ok_icon : $fail_icon;
        $label = $isWritable
            ? install_text('Каталог <strong>'.str_replace(ROOT_PATH, '', $folder).'</strong> доступен', 'Folder <strong>'.str_replace(ROOT_PATH, '', $folder).'</strong> is writable')
            : install_text('Каталог <strong>'.str_replace(ROOT_PATH, '', $folder).'</strong> недоступен. Назначьте права 755.', 'Folder <strong>'.str_replace(ROOT_PATH, '', $folder).'</strong> is not writable. Apply chmod 755.');
        echo '<li class="'.$statusClass.'">'.$icon.'<span>'.$label.'</span></li>';
        $error = !$isWritable || $error;
    }
    $files = [
        ROOT_PATH.'settings/config.php',
        ROOT_PATH.'settings/database.php'
    ];
    foreach ($files as $file) {
        if (!is_file($file)) {
            $f = @fopen($file, 'wb');
            if ($f) {
                fclose($f);
            }
        }
        $isWritable = is_writable($file);
        $statusClass = $isWritable ? 'correct' : 'incorrect';
        $icon = $isWritable ? $ok_icon : $fail_icon;
        $label = $isWritable
            ? install_text('Файл <strong>'.str_replace(ROOT_PATH, '', $file).'</strong> доступен', 'File <strong>'.str_replace(ROOT_PATH, '', $file).'</strong> is writable')
            : install_text('Файл <strong>'.str_replace(ROOT_PATH, '', $file).'</strong> нельзя изменить. Установите права 755.', 'File <strong>'.str_replace(ROOT_PATH, '', $file).'</strong> is read-only. Set chmod 755.');
        echo '<li class="'.$statusClass.'">'.$icon.'<span>'.$label.'</span></li>';
        $error = !$isWritable || $error;
    }
    echo '</ul>';
    echo '<div class="button-bar">';
    echo '<a href="index.php?lang='.$lang.'" class="button secondary">'.install_text('Проверить снова', 'Check again').'</a>';
    if (!$error) {
        echo '<a href="index.php?step=1&amp;lang='.$lang.'" class="button primary">'.install_text('Продолжить обновление', 'Continue upgrade').'</a>';
    }
    echo '</div>';
}

function makeDirectory($dir, $mode = 0755)
{
    if (!is_dir($dir)) {
        $old = umask(0);
        @mkdir($dir, $mode, true);
        umask($old);
    }
    $old = umask(0);
    $f = @chmod($dir, $mode);
    umask($old);
    return $f;
}
