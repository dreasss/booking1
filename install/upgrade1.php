<?php
if (defined('ROOT_PATH')) {
    $username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin@localhost';
    $password = isset($_SESSION['admin_password']) ? $_SESSION['admin_password'] : 'admin';
    echo '<form method="post" action="index.php" autocomplete="off" class="animate-slide-in">';
    echo '<h2>'.install_text('Учётная запись администратора', 'Administrator account').'</h2>';
    echo '<p>'.install_text('Подтвердите или обновите данные главного администратора.', 'Confirm or update the super administrator credentials.').'</p>';
    echo '<div class="form-grid">';
    echo '  <label for="username">'.install_text('Имя пользователя', 'Username').'<span class="g-input"><input type="text" size="50" maxlength="50" id="username" name="username" value="'.htmlspecialchars($username, ENT_QUOTES, 'UTF-8').'"></span></label>';
    echo '  <label for="password">'.install_text('Пароль', 'Password').'<span class="g-input"><input type="password" size="50" maxlength="20" id="password" name="password" value="'.htmlspecialchars($password, ENT_QUOTES, 'UTF-8').'"></span></label>';
    echo '</div>';
    echo '<p class="comment">'.install_text('Эти данные заменят текущую запись администратора.', 'These credentials will overwrite the existing administrator account.').'</p>';
    echo '<input type="hidden" name="step" value="2">';
    echo '<div class="button-bar">';
    echo '  <button class="button primary" type="submit">'.install_text('Продолжить', 'Continue').'</button>';
    echo '</div>';
    echo '</form>';
}
