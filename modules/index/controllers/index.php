<?php
/**
 * @filesource modules/index/controllers/index.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Index\Index;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Http\Response;
use Kotchasan\Language;
use Kotchasan\Template;

/**
 * Controller สำหรับแสดงหน้าเว็บ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * หน้าหลักเว็บไซต์ (index.html)
     * ให้ผลลัพท์เป็น HTML
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        // ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
        define('MAIN_INIT', 'indexhtml');
        // session cookie
        $request->initSession();
        // ตรวจสอบการ login
        Login::create($request);
        // กำหนด skin ให้กับ template
        Template::init(self::$cfg->skin);
        // View
        self::$view = new \Gcms\View();
        // เข้าระบบ
        $login = Login::isMember();
        // โหลดเมนู
        self::$menus = \Index\Menu\Controller::init($login);
        // Javascript
        self::$view->addScript('var FIRST_MODULE="'.self::$menus->home().'";');
        // โหลดโมดูลที่ติดตั้งแล้ว
        self::$modules = \Gcms\Modules::create();
        foreach (self::$modules->getControllers('Init') as $className) {
            if (method_exists($className, 'execute')) {
                // โหลดค่าติดตั้งโมดูล
                $className::execute($request, $login);
            }
        }
        foreach (self::$modules->getControllers('Initmenu') as $className) {
            if (method_exists($className, 'execute')) {
                // โหลดค่าติดตั้งโมดูล
                $className::execute($request, self::$menus, $login);
            }
        }
        // Controller หลัก
        $page = \Index\Main\Controller::create()->execute($request);
        // ตัวเลือกภาษา
        $languages = '';
        $current = strtolower(Language::name());

        $labels = Language::get('LANGUAGE_NAMES');
        if (!is_array($labels)) {
            $labels = ['ru' => 'Русский', 'en' => 'English'];
        }
        $current_label = isset($labels[$current]) ? $labels[$current] : strtoupper($current);
        foreach (Language::installedLanguage() as $item) {
            $code = strtoupper($item);
            $label = isset($labels[$item]) ? $labels[$item] : $code;
            $title = Language::replace('Switch to %s', $label);
            $active = $item === $current ? ' is-active' : '';
            $languages .= '<li role="presentation"><a id=lang_'.$item.' class="lang-option'.$active.'" href="'.$page->canonical()->withParams(['lang' => $item], true).'" aria-label="'.$title.'" title="'.$title.'" data-lang="'.$code.'" tabindex=1 role="menuitemradio" aria-checked="'.($item === $current ? 'true' : 'false').'">'.self::flagIcon($item).'<span class="language-option__label">'.$label.'</span></a></li>';
=======
        foreach (Language::installedLanguage() as $item) {
            $code = strtoupper($item);
            $t = '{LNG_Language} '.$code;
            $active = $item === $current ? ' is-active' : '';
            $languages .= '<li><a id=lang_'.$item.' class="lang-option'.$active.'" href="'.$page->canonical()->withParams(['lang' => $item], true).'" aria-label="'.$t.'" data-lang="'.$code.'" tabindex=1>'.$code.'</a></li>';
 main
        }
        if (is_file(ROOT_PATH.DATA_FOLDER.'images/logo.png')) {
            $logo = '<img src="'.WEB_URL.DATA_FOLDER.'images/logo.png" alt="{WEBTITLE}">';
            if (!empty(self::$cfg->show_title_logo)) {
                $logo .= '{WEBTITLE}';
            }
            self::$view->setMetas([
                '<link rel="icon" type="image/x-icon" href="'.WEB_URL.DATA_FOLDER.'images/logo.png">'
            ]);
        } else {
            $logo = '<span class="'.self::$cfg->default_icon.'">{WEBTITLE}</span>';
        }
        // LINE Add friend
        if ($login && !empty(self::$cfg->line_official_account) && !empty(self::$cfg->line_channel_access_token)) {
            $line_add_friend = '<a href="https://line.me/R/ti/p/'.self::$cfg->line_official_account.'" class=icon-line target=_blank title="{LNG_Add friend}"></a>';
        } else {
            $line_add_friend = '';
        }
        // เนื้อหา
        self::$view->setContents([
            // main template
            '/{MAIN}/' => $page->detail(),
            // กรอบ login
            '/{LOGIN}/' => \Index\Login\Controller::init($request, $login),
            // เมนู
            '/{MENUS}/' => self::$menus->render($page->menu(), $login),
            // โลโก
            '/{LOGO}/' => $logo,
            '/{LOGO_CLASS}/' => self::logoClass(),
            // language menu
            '/{LANGUAGE_BADGE}/' => self::languageBadge($current, $current_label),
            '/{LANGUAGE_NAME}/' => $current_label,
            '/{LANGUAGES}/' => $languages,
            // dark mode
            '/{DARKMODE}/' => $request->cookie('dark')->toBoolean() ? 'icon-night' : 'icon-day',
            // title
            '/{TITLE}/' => $page->title(),
            // class สำหรับ body
            '/{BODYCLASS}/' => $page->bodyClass().' '.self::$cfg->theme_width,
            // LINE Add friend
            '/{LINE}/' => $line_add_friend,
            // เวอร์ชั่น
            '/{VERSION}/' => self::$cfg->version,
            // เลขเวอร์ชั่นของไฟล์
            '/{REV}/' => isset(self::$cfg->reversion) ? self::$cfg->reversion : ''
        ]);
        // ส่งออก เป็น HTML
        $response = new Response();
        if ($page->status() === 404) {
            $response = $response->withStatus(404)->withAddedHeader('Status', '404 Not Found');
        }
        $response->withContent(self::$view->renderHTML())->send();
    }

    /**
     * คืนค่า class ของ logo
     *
     * @param string
     */
    public static function logoClass()
    {
        if (empty(self::$cfg->show_title_logo)) {
            $logo_class = 'hide_title';
        } else {
            $logo_class = 'show_title_logo';
        }
        if (!empty(self::$cfg->new_line_title)) {
            $logo_class .= ' new_line_title';
        }
        return $logo_class;
    }

    /**
     * Render the language badge used in triggers.
     *
     * @param string $language
     * @param string $label
     *
     * @return string
     */
    private static function languageBadge($language, $label)
    {
        $label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

        return '<span class="language-badge">'.self::flagIcon($language).'<span class="language-badge__text">'.$label.'</span></span>';
    }

    /**
     * Return an inline SVG flag for supported languages.
     *
     * @param string $language
     *
     * @return string
     */
    private static function flagIcon($language)
    {
        switch ($language) {
            case 'ru':
                return '<svg class="flag-icon" viewBox="0 0 64 48" aria-hidden="true" focusable="false"><rect width="64" height="16" fill="#ffffff"/><rect width="64" height="16" y="16" fill="#0039a6"/><rect width="64" height="16" y="32" fill="#d52b1e"/></svg>';
            case 'en':
                return '<svg class="flag-icon" viewBox="0 0 64 48" aria-hidden="true" focusable="false"><rect width="64" height="48" fill="#b22234"/><g fill="#ffffff"><rect y="4" width="64" height="4"/><rect y="12" width="64" height="4"/><rect y="20" width="64" height="4"/><rect y="28" width="64" height="4"/><rect y="36" width="64" height="4"/><rect y="44" width="64" height="4"/></g><rect width="28" height="24" fill="#3c3b6e"/><g fill="#ffffff" transform="translate(4 4)"><circle cx="3" cy="3" r="1"/><circle cx="8" cy="3" r="1"/><circle cx="13" cy="3" r="1"/><circle cx="18" cy="3" r="1"/><circle cx="23" cy="3" r="1"/><circle cx="5.5" cy="7" r="1"/><circle cx="10.5" cy="7" r="1"/><circle cx="15.5" cy="7" r="1"/><circle cx="20.5" cy="7" r="1"/><circle cx="3" cy="11" r="1"/><circle cx="8" cy="11" r="1"/><circle cx="13" cy="11" r="1"/><circle cx="18" cy="11" r="1"/><circle cx="23" cy="11" r="1"/><circle cx="5.5" cy="15" r="1"/><circle cx="10.5" cy="15" r="1"/><circle cx="15.5" cy="15" r="1"/><circle cx="20.5" cy="15" r="1"/></g></svg>';
            default:
                return '<span class="language-code">'.strtoupper($language).'</span>';
        }
    }
}
