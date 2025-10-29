# Система бронирования переговорных E-Booking

Корпоративная веб‑платформа для управления бронированием переговорных комнат, построенная на фреймворке Kotchasan (PHP). Интерфейс и контент доступны на русском и английском языках. Система проверяет конфликты броней, отображает календарь и поддерживает онлайн‑управление ресурсами.

Дополнительная информация о Kotchasan доступна на сайте разработчика: https://www.kotchasan.com/index.php?module=knowledge&id=101

## Требования к окружению

- PHP 5.6 или новее
- Расширение `ext-mbstring`
- Драйвер базы данных PDO MySQL

## Установка и обновление

1. Загрузите весь исходный код проекта на сервер (или распакуйте архив в нужный каталог).
2. Откройте установщик по адресу `http://domain.tld/install/` (замените `domain.tld` на свой домен и путь к проекту) и следуйте шагам мастера установки или обновления.
3. После завершения работы мастера удалите каталог `install/` с сервера.

## Доступ по умолчанию

- Учетная запись администратора: `admin@localhost`, пароль `admin`
- Учетная запись пользователя: `demo@localhost`, пароль `demo`

Обязательно измените стандартные учетные данные сразу после установки.

## Условия использования

- Разрешено личное и корпоративное использование.
- Разрешена доработка и кастомизация.
- Вопросы можно задать на форуме проекта Kotchasan: https://www.kotchasan.com
- Для коммерческой доработки обратитесь к автору (возможна платная поддержка).
- Автор не несет ответственность за возможные ошибки при эксплуатации.
- Запрещено перепродавать решение без согласования с автором (для этого требуется пожертвование).

## Создание дистрибутива

Исходники репозитория не содержат бинарных артефактов, поэтому для публикации следует собирать архив локально. В корне проекта предусмотрены два способа подготовки актуального ZIP‑файла `dist/booking-system.zip`:

```bash
php tools/make-archive.php
```

Команда собирает архив через CLI и сохраняет его в каталоге `dist/`, не добавляя файл в Git.

Для получения архива через браузер запустите встроенный веб‑сервер и перейдите по специальной ссылке:

```bash
php -S 0.0.0.0:8000 index.php
```

После запуска откройте `http://127.0.0.1:8000/download.php` — система соберет актуальный пакет и отправит его на скачивание, не сохраняя бинарный файл в репозитории.

## Поддержка проекта

Если вы хотите поддержать автора, можно сделать пожертвование:

```
Банк: Kasikorn Bank, филиал Канчанабури
Счет: 221-2-78341-5
Получатель: Korakot Wiriyah
```

---

### English Summary

E-Booking is a corporate meeting room reservation platform built with the Kotchasan PHP framework. The system ships with Russian and English localisations, conflict detection, and calendar views.

**Environment requirements:** PHP 5.6+, `ext-mbstring`, and PDO MySQL.

**Installation:** upload the project, open `http://domain.tld/install/`, complete the wizard, then remove the `install/` directory.

**Default accounts:** administrator `admin@localhost` / `admin`, user `demo@localhost` / `demo` — make sure to change the credentials afterwards.

**Distribution build:** run `php tools/make-archive.php` or start `php -S 0.0.0.0:8000 index.php` and visit `http://127.0.0.1:8000/download.php`. The ZIP archive is generated locally and never committed to Git, which keeps the repository compatible with GitHub restrictions on binary files.

