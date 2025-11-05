<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
 */
session_start();
// path
define('ROOT_PATH', str_replace(['\\', 'install/index.php'], ['/', ''], __FILE__));
// language selection
$available_languages = [
    'ru' => ['label' => ['ru' => 'Русский', 'en' => 'Russian']],
    'en' => ['label' => ['ru' => 'Английский', 'en' => 'English']]
];
if (isset($_GET['lang']) && isset($available_languages[$_GET['lang']])) {
    $_SESSION['install_lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['install_lang']) && isset($available_languages[$_SESSION['install_lang']]) ? $_SESSION['install_lang'] : 'ru';
// step
$step = isset($_REQUEST['step']) ? (int) $_REQUEST['step'] : 0;
// load current config
$new_config = include ROOT_PATH.'install/settings/config.php';
// helpers
if (!function_exists('install_text')) {
    function install_text($ru, $en = '')
    {
        global $lang;

        if ($en === '') {
            $en = $ru;
        }

        return $lang === 'en' ? $en : $ru;
    }
}

if (!function_exists('install_flag')) {
    function install_flag($language)
    {
        switch ($language) {
            case 'ru':
                return '<svg viewBox="0 0 64 48" aria-hidden="true" focusable="false"><rect width="64" height="16" fill="#ffffff"/><rect width="64" height="16" y="16" fill="#0039a6"/><rect width="64" height="16" y="32" fill="#d52b1e"/></svg>';
            case 'en':
                return '<svg viewBox="0 0 64 48" aria-hidden="true" focusable="false"><rect width="64" height="48" fill="#b22234"/><g fill="#ffffff"><rect y="4" width="64" height="4"/><rect y="12" width="64" height="4"/><rect y="20" width="64" height="4"/><rect y="28" width="64" height="4"/><rect y="36" width="64" height="4"/><rect y="44" width="64" height="4"/></g><rect width="28" height="24" fill="#3c3b6e"/><g fill="#ffffff" transform="translate(4 4)"><circle cx="3" cy="3" r="1"/><circle cx="8" cy="3" r="1"/><circle cx="13" cy="3" r="1"/><circle cx="18" cy="3" r="1"/><circle cx="23" cy="3" r="1"/><circle cx="5.5" cy="7" r="1"/><circle cx="10.5" cy="7" r="1"/><circle cx="15.5" cy="7" r="1"/><circle cx="20.5" cy="7" r="1"/><circle cx="3" cy="11" r="1"/><circle cx="8" cy="11" r="1"/><circle cx="13" cy="11" r="1"/><circle cx="18" cy="11" r="1"/><circle cx="23" cy="11" r="1"/><circle cx="5.5" cy="15" r="1"/><circle cx="10.5" cy="15" r="1"/><circle cx="15.5" cy="15" r="1"/><circle cx="20.5" cy="15" r="1"/></g></svg>';
            default:
                return '<span>'.strtoupper($language).'</span>';
        }
    }
}

// titles
$title = install_text('Установка › Настройка конфигурации', 'Installation › Setup configuration file');
$h1 = install_text('Установка версии '.$new_config['version'], 'Installing version '.$new_config['version']);
if (is_file(ROOT_PATH.'settings/config.php') && is_array(include (ROOT_PATH.'settings/config.php')) && is_file(ROOT_PATH.'settings/database.php') && is_array(include (ROOT_PATH.'settings/database.php'))) {
    // load previous configuration
    $config = include ROOT_PATH.'settings/config.php';
    if (empty($config['version']) || version_compare($config['version'], $new_config['version']) == -1) {
        // upgrade
        $title = install_text('Обновление до версии '.$new_config['version'], 'Upgrade to version '.$new_config['version']);
        $h1 = install_text('Обновление до версии '.$new_config['version'], 'Upgrade to version '.$new_config['version']);
        $file = ROOT_PATH.'install/upgrade'.$step.'.php';
    } else {
        // already installed
        $file = ROOT_PATH.'install/complete.php';
    }
} elseif (is_file(ROOT_PATH.'install/step'.$step.'.php')) {
    // fresh install
    $file = ROOT_PATH.'install/step'.$step.'.php';
}

// header
echo '<!DOCTYPE html>';
echo '<html lang='.$lang.' dir=ltr>';
echo '<head>';
echo '<meta charset=utf-8>';
echo '<title>'.$title.'</title>';
echo '<link rel=stylesheet href="../skin/gcss.css">';
echo '<link rel=stylesheet href="../skin/fonts.css">';
echo '<link rel=stylesheet href="./style.css">';
echo '<link rel="icon" type="image/svg+xml" href="../favicon.svg">';
echo '</head>';
echo '<body>';
echo '<main>';
echo '<nav class="lang-switch">';
foreach ($available_languages as $code => $meta) {
    $active = $code === $lang ? ' class="active"' : '';
    $label = htmlspecialchars(install_text($meta['label']['ru'], $meta['label']['en']), ENT_QUOTES, 'UTF-8');
    $title_attr = htmlspecialchars(install_text('Переключиться на '.$meta['label']['ru'], 'Switch to '.$meta['label']['en']), ENT_QUOTES, 'UTF-8');
    echo '<a'.$active.' href="?step='.$step.'&lang='.$code.'" title="'.$title_attr.'">'.install_flag($code).'<span>'.$label.'</span></a>';
}
echo '</nav>';
echo '<h1 id=logo>'.$h1.'</h1>';
// content
include $file;
// footer
echo '<div class=footer>'.install_text('Разработано <a href="https://www.kotchasan.com" target=_blank rel=noreferrer>Kotchasan</a>. Все права защищены.', 'Crafted by <a href="https://www.kotchasan.com" target=_blank rel=noreferrer>Kotchasan</a>. All rights reserved.').'</div>';
echo '</main>';
echo '</body>';
echo '</html>';
