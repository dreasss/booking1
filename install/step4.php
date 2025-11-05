<?php
if (defined('ROOT_PATH')) {
    $_SESSION['db_username'] = $_POST['db_username'];
    $_SESSION['db_password'] = $_POST['db_password'];
    $_SESSION['db_server'] = $_POST['db_server'];
    $_SESSION['db_port'] = preg_replace('/[^0-9]+/', '', $_POST['db_port']);
    $_SESSION['db_name'] = preg_replace('/[^a-zA-Z0-9_]+/', '', $_POST['db_name']);
    $_SESSION['prefix'] = preg_replace('/[^a-zA-Z0-9_]+/', '', $_POST['prefix']);
    $entries = [];
    $error = false;
    $ok_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m6.5 11.2-2.3-2.3 1.06-1.06L6.5 9.08l4.24-4.24 1.06 1.06z"/></svg></span>';
    $fail_icon = '<span class="status-icon"><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="m8 6.94 3.3-3.3 1.06 1.06L9.06 8l3.3 3.3-1.06 1.06L8 9.06l-3.3 3.3-1.06-1.06L6.94 8l-3.3-3.3L4.7 3.64 8 6.94z"/></svg></span>';
    $listItem = function ($status, $message) use (&$ok_icon, &$fail_icon) {
        $class = $status ? 'correct' : 'incorrect';
        $icon = $status ? $ok_icon : $fail_icon;
        return '<li class="'.$class.'">'.$icon.'<span>'.$message.'</span></li>';
    };
    include ROOT_PATH.'install/db.php';
    try {
        $db = new Db([
            'dbname' => 'INFORMATION_SCHEMA',
            'username' => $_SESSION['db_username'],
            'password' => $_SESSION['db_password'],
            'port' => $_SESSION['db_port'],
            'hostname' => $_SESSION['db_server']
        ]);
        $db_name = $db->databaseExists($_SESSION['db_name']);
        if (!$db_name) {
            $db->query('CREATE DATABASE '.$_SESSION['db_name'].' CHARACTER SET utf8');
        }
        $db->query('USE '.$_SESSION['db_name']);
    } catch (\PDOException $e) {
        $error = true;
        echo '<h2>'.install_text('Ошибка подключения к базе данных', 'Database connection error').'</h2>';
        echo '<div class="warning">'.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</div>';
        echo '<p>'.install_text('Возможные причины:', 'Possible causes:').'</p>';
        echo '<ol>';
        echo '<li>'.install_text('Сервер базы данных временно недоступен.', 'The database server is unavailable.').'</li>';
        echo '<li>'.install_text('Указанная база данных не существует. Создайте её или выберите другую.', 'The target database does not exist. Create it or choose an existing one.').'</li>';
        echo '<li>'.install_text('Параметры подключения заполнены неверно.', 'The connection parameters are incorrect.').'</li>';
        echo '</ol>';
        echo '<div class="button-bar"><a class="button secondary" href="index.php?step=2&amp;lang='.$lang.'">'.install_text('Вернуться и исправить', 'Go back to edit settings').'</a></div>';
    }
    if (!$error) {
        $entries[] = $listItem(true, install_text('Соединение с базой данных установлено', 'Database connection established'));
        $commands = file_get_contents('database.sql');
        $lines = explode("\n", $commands);
        $commands = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && !startsWith($line, '--')) {
                if (preg_match('/CREATE TABLE `\{prefix\}_([a-z_\-]+)`/i', $line, $match)) {
                    $commands .= 'DROP TABLE IF EXISTS `'.$_SESSION['prefix'].'_'.$match[1]."`;\n";
                }
                $commands .= $line."\n";
            }
        }
        $commands = explode(";\n", $commands);
        foreach ($commands as $command) {
            if (trim($command)) {
                $command = str_replace('{prefix}', $_SESSION['prefix'], $command);
                try {
                    $db->query($command);
                    $entries[] = $listItem(true, install_text('Выполнено: ', 'Executed: ').'<code>'.htmlspecialchars($command, ENT_QUOTES, 'UTF-8').'</code>');
                } catch (\PDOException $ex) {
                    $error = true;
                    $entries[] = $listItem(false, htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8'));
                }
            }
        }
        if (!$error) {
            try {
                $db->query("DELETE FROM `".$_SESSION['prefix']."_user` WHERE `id` IN (1,2)");
                $password_key = uniqid();
                $salt = uniqid();
                $username = $_SESSION['admin_username'];
                $password = $_SESSION['admin_password'];
                $today = date('Y-m-d H:i:s');
                $sql = "INSERT INTO `".$_SESSION['prefix']."_user` (`id`, `username`, `salt`, `password`, `token`, `status`, `permission`, `name`, `create_date`) VALUES";
                $sql .= "(1, '$username', '$salt', '".sha1($password_key.$password.$salt)."', NULL, 1, '', 'Administrator', '$today'),";
                $sql .= "(2, 'approver', '$salt', '".sha1($password_key.'approver'.$salt)."', NULL, 2, '', 'Approver', '$today');";
                $db->query($sql);
                $sql = "INSERT INTO `".$_SESSION['prefix']."_user_meta` (`member_id`,`name`,`value`) VALUES";
                $sql .= "(1, 'department', '3'),";
                $sql .= "(2, 'department', '1');";
                $db->query($sql);
                $entries[] = $listItem(true, install_text('Созданы учетные записи по умолчанию', 'Default user accounts created'));
            } catch (\PDOException $ex) {
                $error = true;
                $entries[] = $listItem(false, htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8'));
            }
        }
        if (!$error) {
            $database_cfg = include 'settings/database.php';
            $database_cfg['mysql']['username'] = $_SESSION['db_username'];
            $database_cfg['mysql']['password'] = $_SESSION['db_password'];
            $database_cfg['mysql']['dbname'] = $_SESSION['db_name'];
            $database_cfg['mysql']['hostname'] = $_SESSION['db_server'];
            $database_cfg['mysql']['port'] = $_SESSION['db_port'];
            $database_cfg['mysql']['prefix'] = $_SESSION['prefix'];
            $f = save($database_cfg, ROOT_PATH.'settings/database.php');
            $entries[] = $listItem($f, install_text('Файл <strong>settings/database.php</strong> сохранён', 'settings/database.php saved'));
            $cfg = include 'settings/config.php';
            $cfg['password_key'] = $password_key;
            $cfg['reversion'] = time();
            if (!function_exists('imagewebp')) {
                $cfg['stored_img_type'] = '.jpg';
            }
            $f = save($cfg, ROOT_PATH.'settings/config.php');
            $entries[] = $listItem($f, install_text('Файл <strong>settings/config.php</strong> сохранён', 'settings/config.php saved'));
            $db_config = [
                'prefix' => $_SESSION['prefix']
            ];
            include ROOT_PATH.'install/language.php';
        }
        if (!$error) {
            $display_user = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
            $display_pass = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
            unset($_SESSION);
            echo '<h2>'.install_text('Установка завершена', 'Installation complete').'</h2>';
            echo '<p>'.install_text('Система успешно установлена. За поддержкой обращайтесь на <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a>.', 'The system is ready. Visit <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a> for support.').'</p>';
            echo '<ul>'.implode('', $entries).'</ul>';
            echo '<div class="warning">'.install_text('Удалите каталог <strong>install/</strong> с сервера и верните права каталогов <strong>datas/</strong> и <strong>settings/</strong> к 644.', 'Remove the <strong>install/</strong> directory and reset <strong>datas/</strong> and <strong>settings/</strong> permissions to 644.').'</div>';
            echo '<p>'.install_text('Войдите в систему с логином <em>'.$display_user.'</em> и паролем <em>'.$display_pass.'</em>, чтобы завершить настройку.', 'Sign in with <em>'.$display_user.'</em> and password <em>'.$display_pass.'</em> to finish configuring the system.').'</p>';
            echo '<div class="button-bar"><a class="button primary" href="../index.php">'.install_text('Перейти ко входу', 'Open the application').'</a></div>';
        } else {
            echo '<h2>'.install_text('Установка не завершена', 'Installation incomplete').'</h2>';
            echo '<p>'.install_text('Некоторые шаги завершились ошибкой. Исправьте сообщения ниже и повторите попытку. При необходимости обратитесь на <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a>.', 'Some steps failed. Resolve the issues below and try again, or contact <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a> for assistance.').'</p>';
            echo '<ul>'.implode('', $entries).'</ul>';
            echo '<div class="button-bar"><a class="button secondary" href=".">'.install_text('Попробовать снова', 'Try again').'</a></div>';
        }
    }
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function save($config, $file)
{
    $f = @fopen($file, 'wb');
    if ($f !== false) {
        if (!preg_match('/^.*\/([^\/]+)\.php?/', $file, $match)) {
            $match[1] = 'config';
        }
        fwrite($f, '<'."?php\n/* $match[1].php */\nreturn ".var_export((array) $config, true).';');
        fclose($f);
        return true;
    } else {
        return false;
    }
}
