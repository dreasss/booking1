<?php
echo '<h2>'.install_text('Система уже установлена', 'System already installed').'</h2>';
echo '<p>'.install_text('Kotchasan уже настроен на этом сервере.', 'Kotchasan is already configured on this server.').'</p>';
echo '<div class="warning">'.install_text('Удалите каталог <strong>install/</strong> перед продолжением.', 'Remove the <strong>install/</strong> directory before continuing.').'</div>';
echo '<div class="button-bar"><a href="../index.php?module=system" class="button primary">'.install_text('Открыть систему', 'Open the console').'</a></div>';
