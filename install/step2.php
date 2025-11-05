<?php
if (defined('ROOT_PATH')) {
    $username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin@localhost';
    $password = isset($_SESSION['admin_password']) ? $_SESSION['admin_password'] : 'admin';
    echo '<form method="post" action="index.php" autocomplete="off" class="animate-slide-in">';
    echo '<h2>'.install_text('Администратор системы', 'System administrator account').'</h2>';
    echo '<p>'.install_text('Укажите данные учётной записи с максимальными правами. Сохраните эти данные в безопасном месте.', 'Provide the credentials for the super administrator and store them safely.').'</p>';
    echo '<div class="form-grid">';
    echo '  <label for="username">'.install_text('Имя пользователя', 'Username').'<span class="g-input"><input type="text" size="50" maxlength="50" id="username" name="username" value="'.htmlspecialchars($username, ENT_QUOTES, 'UTF-8').'"></span></label>';
    echo '  <label for="password">'.install_text('Пароль', 'Password').'<span class="g-input"><input type="password" size="50" maxlength="20" id="password" name="password" value="'.htmlspecialchars($password, ENT_QUOTES, 'UTF-8').'"></span></label>';
    echo '</div>';
    echo '<p class="comment">'.install_text('Используйте надёжную комбинацию. Учётная запись понадобится для первой авторизации.', 'Use a strong combination. You will sign in with this account after installation.').'</p>';
    echo '<input type="hidden" name="step" value="3">';
    echo '<div class="button-bar">';
    echo '  <button class="button primary" type="submit">'.install_text('Продолжить', 'Continue').'</button>';
    echo '</div>';
    echo '</form>';
}
