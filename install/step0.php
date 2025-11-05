<?php
if (defined('ROOT_PATH')) {
    $ok_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m6.5 11.2-2.3-2.3 1.06-1.06L6.5 9.08l4.24-4.24 1.06 1.06z"/></svg></span>';
    $fail_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m8 6.94 3.3-3.3 1.06 1.06L9.06 8l3.3 3.3-1.06 1.06L8 9.06l-3.3 3.3-1.06-1.06L6.94 8l-3.3-3.3L4.7 3.64 8 6.94z"/></svg></span>';
    echo '<form method="post" action="index.php" autocomplete="off">';
    echo '<h2>'.install_text('Предварительная проверка сервера', 'Pre-installation check').'</h2>';
    echo '<p>'.install_text('Ответьте на несколько вопросов и убедитесь, что сервер готов к установке. Все элементы должны подсветиться зелёным цветом.', 'Answer a few questions to confirm the server is ready. All items below should turn green.').'</p>';
    $v = version_compare(PHP_VERSION, '5.6.0', '>=');
    $checks = [
        [
            'status' => $v,
            'ok' => install_text('Версия PHP: <strong>'.PHP_VERSION.'</strong>', 'PHP version: <strong>'.PHP_VERSION.'</strong>'),
            'fail' => install_text('Требуется PHP версии <strong>5.6.0</strong> или выше', 'PHP <strong>5.6.0</strong> or newer is required'),
            'help' => ''
        ],
        [
            'status' => defined('PDO::ATTR_DRIVER_NAME') && in_array('mysql', \PDO::getAvailableDrivers()),
            'ok' => install_text('Поддержка PDO MySQL включена', 'PDO MySQL support is available'),
            'fail' => install_text('Необходима поддержка PDO MySQL', 'PDO MySQL support must be enabled'),
            'help' => ''
        ],
        [
            'status' => extension_loaded('mbstring'),
            'ok' => install_text('Расширение MBString доступно', 'MBString extension is available'),
            'fail' => install_text('Включите расширение MBString', 'Enable the MBString extension'),
            'help' => ''
        ],
        [
            'status' => ini_get('register_globals') == false,
            'ok' => install_text('Register Globals отключён', 'Register Globals is disabled'),
            'fail' => install_text('Отключите Register Globals', 'Disable Register Globals'),
            'help' => ''
        ],
        [
            'status' => extension_loaded('zlib'),
            'ok' => install_text('Поддержка Zlib активна', 'Zlib compression support is active'),
            'fail' => install_text('Требуется поддержка Zlib', 'Zlib compression support is required'),
            'help' => 'https://www.goragod.com/knowledge/native_zip_support.html'
        ],
        [
            'status' => function_exists('json_encode') && function_exists('json_decode'),
            'ok' => install_text('JSON доступен', 'JSON support is available'),
            'fail' => install_text('Функции JSON должны быть включены', 'JSON functions must be enabled'),
            'help' => ''
        ],
        [
            'status' => extension_loaded('xml'),
            'ok' => install_text('Расширение XML доступно', 'XML extension is available'),
            'fail' => install_text('Требуется расширение XML', 'XML extension is required'),
            'help' => ''
        ],
        [
            'status' => function_exists('openssl_random_pseudo_bytes'),
            'ok' => install_text('OpenSSL доступен', 'OpenSSL support is available'),
            'fail' => install_text('Включите OpenSSL', 'Enable OpenSSL support'),
            'help' => ''
        ],
        [
            'status' => extension_loaded('gd'),
            'ok' => install_text('GD Library установлена', 'GD library is installed'),
            'fail' => install_text('Необходима библиотека GD', 'GD library is required'),
            'help' => 'https://www.kotchasan.com/knowledge/gd_support.html'
        ],
        [
            'status' => function_exists('curl_version'),
            'ok' => install_text('cURL доступен', 'cURL support is available'),
            'fail' => install_text('Активируйте cURL', 'Enable cURL support'),
            'help' => 'https://www.kotchasan.com/knowledge/curl_support.html'
        ]
    ];
    echo '<ul>';
    $error = false;
    foreach ($checks as $item) {
        $statusClass = $item['status'] ? 'correct' : 'incorrect';
        $label = $item['status'] ? $item['ok'] : $item['fail'];
        $icon = $item['status'] ? $ok_icon : $fail_icon;
        $error = !$item['status'] || $error;
        echo '<li class="'.$statusClass.'">'.$icon.'<span>'.$label;
        if (!$item['status'] && $item['help'] !== '') {
            echo ' <a href="'.$item['help'].'" target="_blank" class="icon-help" rel="noreferrer">'.install_text('Подробнее', 'Learn more').'</a>';
        }
        echo '</span></li>';
    }
    echo '</ul>';
    echo '<p>'.install_text('Рекомендуется также проверить параметры ниже. Они не обязательны, но улучшат стабильность системы.', 'We also recommend aligning the following PHP directives; they are optional but improve stability.').'</p>';
    $recommend = [
        [install_text('Safe Mode — выключен', 'Safe Mode — OFF'), (bool) ini_get('safe_mode') === false],
        [install_text('File Uploads — включены', 'File Uploads — ON'), (bool) ini_get('file_uploads') === true],
        [install_text('Magic Quotes GPC — выключен', 'Magic Quotes GPC — OFF'), (bool) ini_get('magic_quotes_gpc') === false],
        [install_text('Magic Quotes Runtime — выключен', 'Magic Quotes Runtime — OFF'), (bool) ini_get('magic_quotes_runtime') === false],
        [install_text('Session Auto Start — выключен', 'Session Auto Start — OFF'), (bool) ini_get('session.auto_start') === false],
        [install_text('Native ZIP support — включена', 'Native ZIP support — ON'), function_exists('zip_open') && function_exists('zip_read')],
        [install_text('Intl extension — включено', 'Intl extension — ON'), function_exists('idn_to_ascii')],
        [install_text('OPcache — включён', 'OPcache support — ON'), function_exists('opcache_invalidate')]
    ];
    echo '<ul>';
    foreach ($recommend as $item) {
        $statusClass = $item[1] ? 'correct' : 'incorrect';
        $icon = $item[1] ? $ok_icon : $fail_icon;
        echo '<li class="'.$statusClass.'">'.$icon.'<span>'.$item[0].'</span></li>';
    }
    echo '</ul>';
    if ($error) {
        echo '<div class="warning">'.install_text('Сервер не готов к установке. Исправьте параметры, отмеченные красным, и повторите проверку.', 'The server is not ready yet. Fix the red items above and run the check again.').'</div>';
    }
    echo '<div class="button-bar">';
    echo '<a class="button secondary" href="?step=0&amp;lang='.$lang.'">'.install_text('Проверить снова', 'Run check again').'</a>';
    if (!$error) {
        echo '<input type="hidden" name="step" value="1">';
        echo '<button class="button primary" type="submit">'.install_text('Начать установку', 'Start installation').'</button>';
    }
    echo '</div>';
    echo '</form>';
}
