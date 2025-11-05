<?php
/**
 * @filesource modules/index/controllers/privacy.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Index\Privacy;

use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=privacy
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * @param Request $request
     */
    public function render(Request $request)
    {
        // template ตามภาษาที่เลือก พร้อมสำรองเป็นภาษารัสเซียหรืออังกฤษ
        $template = null;
        foreach ([Language::name(), 'ru', 'en'] as $lng) {
            $candidate = ROOT_PATH.self::$cfg->skin.'/privacy_'.$lng.'.html';
            if (file_exists($candidate)) {
                $template = $candidate;
                break;
            }
        }
        if ($template === null) {
            $template = ROOT_PATH.self::$cfg->skin.'/privacy_en.html';
        }
        // content
        $content = file_get_contents($template);
        // title, menu
        if (preg_match('/<h1[^>]{0,}>(.*)<\/h1>/', $content, $match)) {
            $this->title = strip_tags($match[1]);
        }
        $this->menu = 'privacy';
        // คืนค่า HTML
        $data_controller = empty(self::$cfg->data_controller) ? '' : self::$cfg->data_controller;
        return str_replace('{DATACONTROLLER}', $data_controller, $content);
    }
}
