<?php
// Импорт языковых файлов
$dir = ROOT_PATH.'language/';
if (is_dir(ROOT_PATH.'language/')) {
    // таблица language
    $table = $db_config['prefix'].'_language';
    // сканируем доступные языковые файлы
    $f = opendir($dir);
    if ($f) {
        while (false !== ($text = readdir($f))) {
            if (preg_match('/^([a-z]{2,2})\.(php|js)$/', $text, $match)) {
                if ($db->fieldExists($table, $match[1]) == false) {
                    // добавляем колонку языка при необходимости
                    $db->query("ALTER TABLE `$table` ADD `$match[1]` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci AFTER `en`");
                }
                if ($match[2] == 'php') {
                    importPHP($db, $table, $match[1], $dir.$text);
                } else {
                    importJS($db, $table, $match[1], $dir.$text);
                }
            }
        }
        closedir($f);
    }
    $content[] = '<li class="correct">Импорт таблицы `'.$table.'` выполнен успешно</li>';
}

/**
 * Импорт строк из PHP-файла перевода
 *
 * @param Db     $db        Database Class
 * @param string $table     Имя таблицы language
 * @param string $lang      Код языка
 * @param string $file_name Путь к файлу языка
 */
function importPHP($db, $table, $lang, $file_name)
{
    foreach (include ($file_name) as $key => $value) {
        if (is_array($value)) {
            $type = 'array';
        } elseif (is_int($value)) {
            $type = 'int';
        } else {
            $type = 'text';
        }
        $search = $db->first($table, ['key' => $key, 'js' => 0]);
        if ($type == 'array') {
            $value = serialize($value);
        }
        if ($search) {
            $db->update($table, [
                'id' => $search->id
            ], [
                $lang => $value
            ]);
        } else {
            $db->insert($table, [
                'key' => $key,
                'js' => 0,
                'type' => $type,
                'owner' => 'index',
                $lang => $value
            ]);
        }
    }
}

/**
 * Импорт строк из JS-файла перевода
 *
 * @param Database $db        Database Object
 * @param string   $table     Имя таблицы language
 * @param string   $lang      Код языка
 * @param string   $file_name Путь к файлу языка
 */
function importJS($db, $table, $lang, $file_name)
{
    $patt = '/^var[\s]+([A-Z0-9_]+)[\s]{0,}=[\s]{0,}[\'"](.*)[\'"];$/';
    foreach (file($file_name) as $item) {
        $item = trim($item);
        if ($item != '') {
            if (preg_match($patt, $item, $match)) {
                $search = $db->first($table, ['key' => $match[1], 'js' => 1]);
                if ($search) {
                    $db->update($table, [
                        'id' => $search->id
                    ], [
                        $lang => $match[2]
                    ]);
                } else {
                    $db->insert($table, [
                        'key' => $match[1],
                        'js' => 1,
                        'type' => 'text',
                        'owner' => 'index',
                        $lang => $match[2]
                    ]);
                }
            }
        }
    }
}
