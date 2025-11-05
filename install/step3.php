<?php
if (defined('ROOT_PATH')) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        include ROOT_PATH.'install/step2.php';
    } else {
        include ROOT_PATH.'Kotchasan/Text.php';
        $_SESSION['admin_username'] = \Kotchasan\Text::username($_POST['username']);
        $_SESSION['admin_password'] = \Kotchasan\Text::password($_POST['password']);
        $database = include 'settings/database.php';
        $db_username = isset($_SESSION['db_username']) ? $_SESSION['db_username'] : $database['mysql']['username'];
        $db_password = isset($_SESSION['db_password']) ? $_SESSION['db_password'] : $database['mysql']['password'];
        $db_server = isset($_SESSION['db_server']) ? $_SESSION['db_server'] : 'localhost';
        $db_port = isset($_SESSION['db_port']) ? $_SESSION['db_port'] : 3306;
        $db_name = isset($_SESSION['db_name']) ? $_SESSION['db_name'] : $database['mysql']['dbname'];
        $prefix = isset($_SESSION['prefix']) ? $_SESSION['prefix'] : $database['mysql']['prefix'];
        echo '<form method="post" action="index.php" autocomplete="off" class="animate-slide-in">';
        echo '<h2>'.install_text('Параметры базы данных', 'Database configuration').'</h2>';
        echo '<p>'.install_text('Введите параметры подключения MySQL. Они нужны для создания таблиц и подключения приложения.', 'Provide your MySQL credentials so the installer can create the required tables.').'</p>';
        echo '<div class="form-grid">';
        echo '  <label for="db_username">'.install_text('Имя пользователя', 'Username').'<span class="g-input"><input type="text" size="50" id="db_username" name="db_username" value="'.htmlspecialchars($db_username, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Учётная запись MySQL с правами на создание таблиц.', 'MySQL account with permission to create tables.').'</span>';
        echo '  <label for="db_password">'.install_text('Пароль', 'Password').'<span class="g-input"><input type="password" size="50" id="db_password" name="db_password" value="'.htmlspecialchars($db_password, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Пароль для указанного пользователя.', 'Password for the MySQL user.').'</span>';
        echo '  <label for="db_server">'.install_text('Хост базы данных', 'Database host').'<span class="g-input"><input type="text" size="50" id="db_server" name="db_server" value="'.htmlspecialchars($db_server, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Обычно это localhost.', 'Usually localhost.').'</span>';
        echo '  <label for="db_port">'.install_text('Порт', 'Port').'<span class="g-input"><input type="text" size="50" id="db_port" name="db_port" value="'.htmlspecialchars($db_port, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Стандартный порт MySQL — 3306.', 'Default MySQL port is 3306.').'</span>';
        echo '  <label for="db_name">'.install_text('Имя базы данных', 'Database name').'<span class="g-input"><input type="text" size="50" id="db_name" name="db_name" value="'.htmlspecialchars($db_name, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Используйте существующую базу или создайте новую заранее.', 'Use an existing database or create one beforehand.').'</span>';
        echo '  <label for="prefix">'.install_text('Префикс таблиц', 'Table prefix').'<span class="g-input"><input type="text" size="50" id="prefix" name="prefix" value="'.htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8').'"></span></label>';
        echo '  <span class="comment">'.install_text('Помогает разделить таблицы, если база общая.', 'Helps separate tables when sharing a database.').'</span>';
        echo '</div>';
        echo '<input type="hidden" name="step" value="4">';
        echo '<div class="button-bar">';
        echo '  <button class="button primary" type="submit">'.install_text('Установить', 'Install').'</button>';
        echo '</div>';
        echo '</form>';
    }
}
