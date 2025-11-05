<?php
if (defined('ROOT_PATH')) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        include ROOT_PATH.'install/upgrade1.php';
    } else {
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
        $db_config = include ROOT_PATH.'settings/database.php';
        try {
            $db_config = $db_config['mysql'];
            $db = new Db($db_config);
        } catch (\Exception $exc) {
            $error = true;
            echo '<h2>'.install_text('Ошибка подключения к базе данных', 'Database connection error').'</h2>';
            echo '<div class="warning">'.install_text('Не удаётся подключиться к текущей базе данных. Проверьте настройки в файле <strong>settings/database.php</strong>.', 'Unable to connect to the configured database. Please verify <strong>settings/database.php</strong>.').'</div>';
            echo '<p>'.install_text('Сообщение сервера:', 'Server message:').'</p>';
            echo '<div class="warning">'.htmlspecialchars($exc->getMessage(), ENT_QUOTES, 'UTF-8').'</div>';
            echo '<div class="button-bar"><a href="index.php?step=1&amp;lang='.$lang.'" class="button secondary">'.install_text('Вернуться', 'Back').'</a></div>';
        }
        if (!$error) {
            $entries[] = $listItem(true, install_text('Соединение с базой данных установлено', 'Database connection established'));
            try {
                $table_user = $db_config['prefix'].'_user';
                if (empty($config['password_key'])) {
                    $config['password_key'] = uniqid();
                }
                updateAdmin($db, $table_user, $_POST['username'], $_POST['password'], $config['password_key']);
                if (!$db->fieldExists($table_user, 'social')) {
                    $db->query("ALTER TABLE `$table_user` CHANGE `fb` `social` TINYINT(1) NOT NULL DEFAULT 0");
                }
                if (!$db->fieldExists($table_user, 'country')) {
                    $db->query("ALTER TABLE `$table_user` ADD `country` VARCHAR(2)");
                }
                if (!$db->fieldExists($table_user, 'province')) {
                    $db->query("ALTER TABLE `$table_user` ADD `province` VARCHAR(50)");
                }
                if (!$db->fieldExists($table_user, 'token')) {
                    $db->query("ALTER TABLE `$table_user` ADD `token` VARCHAR(50) NULL AFTER `password`");
                }
                $db->query("ALTER TABLE `$table_user` CHANGE `address` `address` VARCHAR(150) DEFAULT NULL");
                $db->query("ALTER TABLE `$table_user` CHANGE `password` `password` VARCHAR(50) NOT NULL");
                $db->query("ALTER TABLE `$table_user` CHANGE `username` `username` VARCHAR(50) DEFAULT NULL");
                foreach (['visited', 'lastvisited', 'session_id', 'ip'] as $column) {
                    if ($db->fieldExists($table_user, $column)) {
                        $db->query("ALTER TABLE `$table_user` DROP `$column`");
                    }
                }
                if (!$db->indexExists($table_user, 'phone')) {
                    $db->query("ALTER TABLE `$table_user` ADD INDEX (`phone`)");
                }
                if (!$db->indexExists($table_user, 'id_card')) {
                    $db->query("ALTER TABLE `$table_user` ADD INDEX (`id_card`)");
                }
                if (!$db->indexExists($table_user, 'token')) {
                    $db->query("ALTER TABLE `$table_user` ADD INDEX (`token`)");
                }
                if (!$db->fieldExists($table_user, 'line_uid')) {
                    $db->query("ALTER TABLE `$table_user` ADD `line_uid` VARCHAR(33) DEFAULT NULL");
                }
                if (!$db->indexExists($table_user, 'line_uid')) {
                    $db->query("ALTER TABLE `$table_user` ADD INDEX (`line_uid`)");
                }
                if (!$db->fieldExists($table_user, 'telegram_id')) {
                    $db->query("ALTER TABLE `$table_user` ADD `telegram_id` VARCHAR(13) DEFAULT NULL");
                }
                if (!$db->fieldExists($table_user, 'activatecode')) {
                    $db->query("ALTER TABLE `$table_user` ADD `activatecode` VARCHAR(32) NOT NULL DEFAULT '', ADD INDEX (`activatecode`)");
                }
                $db->query("ALTER TABLE `$table_user` CHANGE `salt` `salt` VARCHAR(32) CHARACTER SET utf8 DEFAULT ''");
                $db->query("ALTER TABLE `$table_user` CHANGE `permission` `permission` TEXT CHARACTER SET utf8 DEFAULT ''");
                $entries[] = $listItem(true, install_text('Таблица <code>'.$table_user.'</code> обновлена', 'Table <code>'.$table_user.'</code> updated'));
                $table_logs = $db_config['prefix'].'_logs';
                if (!$db->tableExists($table_logs)) {
                    $sql = 'CREATE TABLE `'.$table_logs.'` (';
                    $sql .= ' `id` int(11) NOT NULL,';
                    $sql .= ' `src_id` int(11) NOT NULL,';
                    $sql .= ' `module` varchar(20) NOT NULL,';
                    $sql .= ' `action` varchar(20) NOT NULL,';
                    $sql .= ' `create_date` datetime NOT NULL,';
                    $sql .= ' `reason` text DEFAULT NULL,';
                    $sql .= ' `member_id` int(11) DEFAULT NULL,';
                    $sql .= ' `topic` text NOT NULL,';
                    $sql .= ' `datas` text DEFAULT NULL';
                    $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
                    $db->query($sql);
                    $sql = 'ALTER TABLE `'.$table_logs.'`';
                    $sql .= ' ADD PRIMARY KEY (`id`),';
                    $sql .= ' ADD KEY `src_id` (`src_id`),';
                    $sql .= ' ADD KEY `module` (`module`),';
                    $sql .= ' ADD KEY `action` (`action`);';
                    $db->query($sql);
                    $db->query('ALTER TABLE `'.$table_logs.'` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
                    $entries[] = $listItem(true, install_text('Создан журнал событий <code>'.$table_logs.'</code>', 'Created audit log table <code>'.$table_logs.'</code>'));
                }
                $table_category = $db_config['prefix'].'_category';
                if (!$db->tableExists($table_category)) {
                    $sql = 'CREATE TABLE `'.$table_category.'` (';
                    $sql .= ' `type` varchar(20) NOT NULL,';
                    $sql .= ' `category_id` varchar(10) DEFAULT "0",';
                    $sql .= ' `language` varchar(2) DEFAULT "",';
                    $sql .= ' `topic` varchar(150) NOT NULL,';
                    $sql .= ' `color` varchar(16) DEFAULT NULL,';
                    $sql .= ' `published` tinyint(1) NOT NULL DEFAULT 1';
                    $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
                    $db->query($sql);
                    $db->query('ALTER TABLE `'.$table_category.'` ADD KEY `type` (`type`), ADD KEY `category_id` (`category_id`), ADD KEY `language` (`language`);');
                } else {
                    if (!$db->fieldExists($table_category, 'language')) {
                        $db->query("ALTER TABLE `$table_category` ADD `language` VARCHAR(2) NOT NULL DEFAULT '' AFTER `category_id`, ADD INDEX (`language`);");
                    }
                    $db->query("ALTER TABLE `$table_category` CHANGE `category_id` `category_id` VARCHAR(10) NOT NULL DEFAULT '0'");
                }
                $entries[] = $listItem(true, install_text('Категории обновлены', 'Category table updated'));
                $table_user_meta = $db_config['prefix'].'_user_meta';
                if (!$db->tableExists($table_user_meta)) {
                    $sql = 'CREATE TABLE `'.$table_user_meta.'` (';
                    $sql .= ' `value` varchar(10) NOT NULL,';
                    $sql .= ' `name` varchar(20) NOT NULL,';
                    $sql .= ' `member_id` int(11) NOT NULL';
                    $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
                    $db->query($sql);
                    $db->query('ALTER TABLE `'.$table_user_meta.'` ADD KEY `member_id` (`member_id`,`name`);');
                } else {
                    $db->query("ALTER TABLE `$table_user_meta` CHANGE `name` `name` VARCHAR(20) CHARACTER SET utf8 NOT NULL");
                }
                $entries[] = $listItem(true, install_text('Дополнительные атрибуты пользователей обновлены', 'User metadata table updated'));
                $table_reservation = $db_config['prefix'].'_reservation';
                if (!$db->fieldExists($table_reservation, 'approve')) {
                    $db->query("ALTER TABLE `$table_reservation` ADD `approve` tinyint(1) NOT NULL, ADD `closed` tinyint(1) NOT NULL, ADD `department` varchar(10) DEFAULT NULL");
                    $db->query("UPDATE `$table_reservation` SET `closed`=1");
                    $db->query("UPDATE `$table_reservation` SET `approve`=1");
                    $db->query("ALTER TABLE `$table_reservation` ADD INDEX (`status`)");
                    $db->query("DELETE FROM `$table_user_meta` WHERE `name`='department'");
                    $db->query("INSERT INTO `$table_user_meta` (`value`,`name`,`member_id`) SELECT '1','department', id FROM `$table_user` WHERE `id`=1 OR `permission` LIKE '%can_approve_room%'");
                    $db->query("UPDATE `$table_user` SET `status`=0 WHERE `status`>1 AND `permission` LIKE '%can_approve_room%'");
                    $db->query("INSERT INTO `$table_user_meta` (`value`,`name`,`member_id`) SELECT '2','department', U.`id` FROM `$table_user` AS U WHERE NOT EXISTS (SELECT 1 FROM `$table_user_meta` WHERE `member_id`=U.`id`)");
                }
                if ($db->fieldExists($table_reservation, 'approver')) {
                    $db->query("ALTER TABLE `$table_reservation` DROP `approver`");
                    $db->query("ALTER TABLE `$table_reservation` DROP `approved_date`");
                }
                $entries[] = $listItem(true, install_text('Таблица бронирований обновлена', 'Reservation table updated'));
                $config['version'] = $new_config['version'];
                $config['reversion'] = time();
                if (function_exists('imagewebp')) {
                    $config['stored_img_type'] = isset($config['stored_img_type']) ? $config['stored_img_type'] : '.jpg';
                } else {
                    $config['stored_img_type'] = '.jpg';
                }
                if (isset($new_config['default_icon'])) {
                    $config['default_icon'] = $new_config['default_icon'];
                }
                if (isset($config['booking_delete']) && !is_array($config['booking_delete'])) {
                    $config['booking_delete'] = [$config['booking_delete']];
                }
                $f = save($config, ROOT_PATH.'settings/config.php');
                $entries[] = $listItem($f, install_text('Файл <strong>settings/config.php</strong> сохранён', 'settings/config.php saved'));
                include ROOT_PATH.'install/language.php';
            } catch (\PDOException $exc) {
                $entries[] = $listItem(false, htmlspecialchars($exc->getMessage(), ENT_QUOTES, 'UTF-8'));
                $error = true;
            } catch (\Exception $exc) {
                $entries[] = $listItem(false, htmlspecialchars($exc->getMessage(), ENT_QUOTES, 'UTF-8'));
                $error = true;
            }
            if (!$error) {
                echo '<h2>'.install_text('Обновление завершено', 'Upgrade complete').'</h2>';
                echo '<p>'.install_text('Система успешно обновлена. За поддержкой обращайтесь на <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a>.', 'The upgrade finished successfully. Contact <a href="https://www.kotchasan.com" target="_blank" rel="noreferrer">kotchasan.com</a> for support.').'</p>';
                echo '<ul>'.implode('', $entries).'</ul>';
                echo '<div class="warning">'.install_text('Удалите каталог <strong>install/</strong> и верните права каталогов <strong>datas/</strong> и <strong>settings/</strong> к 644.', 'Remove the <strong>install/</strong> directory and reset <strong>datas/</strong> and <strong>settings/</strong> permissions to 644.').'</div>';
                echo '<div class="button-bar"><a href="../index.php" class="button primary">'.install_text('Открыть систему', 'Open the system').'</a></div>';
            } else {
                echo '<h2>'.install_text('Обновление не завершено', 'Upgrade incomplete').'</h2>';
                echo '<p>'.install_text('Некоторые шаги завершились ошибкой. Проверьте сообщения ниже и повторите попытку.', 'Some upgrade steps failed. Review the messages below and try again.').'</p>';
                echo '<ul>'.implode('', $entries).'</ul>';
                echo '<div class="button-bar"><a href="." class="button secondary">'.install_text('Попробовать снова', 'Try again').'</a></div>';
            }
        }
    }
}

function updateAdmin($db, $table_name, $username, $password, $password_key)
{
    include ROOT_PATH.'Kotchasan/Text.php';
    $username = \Kotchasan\Text::username($username);
    $password = \Kotchasan\Text::password($password);
    $result = $db->first($table_name, [
        'username' => $username,
        'status' => 1
    ]);
    if (!$result || $result->id > 1) {
        throw new \Exception(install_text('Имя пользователя не принадлежит супер администратору.', 'Username does not belong to the super administrator.'));
    } elseif ($result->password === sha1($password.$result->salt)) {
        $password = sha1($password_key.$password.$result->salt);
        $db->update($table_name, ['id' => $result->id], ['password' => $password]);
    } elseif ($result->password != sha1($password_key.$password.$result->salt)) {
        throw new \Exception(install_text('Неверный пароль администратора.', 'Invalid administrator password.'));
    }
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
