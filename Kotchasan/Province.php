<?php
/**
 * @filesource Kotchasan/Province.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 */

namespace Kotchasan;

/**
 * Provides Russian federal subject lookups for the booking platform.
 */
class Province
{
    /**
     * Load provinces for the specified country code.
     */
    private static function init($country)
    {
        $country = strtoupper($country);
        if (method_exists(__CLASS__, $country)) {
            return forward_static_call([__CLASS__, $country]);
        }

        return [];
    }

    /**
     * Get a list of provinces for the given country (Russia by default).
     */
    public static function all($country = 'RU')
    {
        $datas = self::init($country);
        if (empty($datas)) {
            return [];
        }
        $language = Language::name();
        $language = array_key_exists($language, reset($datas)) ? $language : 'en';
        $result = [];
        foreach ($datas as $iso => $values) {
            $result[$iso] = $values[$language];
        }
        if ($language === 'en') {
            asort($result);
        }

        return $result;
    }

    /**
     * Countries with configured province data.
     */
    public static function countries()
    {
        return ['RU'];
    }

    /**
     * Get a province name by its code.
     */
    public static function get($iso, $lang = '', $country = 'RU')
    {
        $datas = self::init($country);
        if (empty($datas)) {
            return '';
        }
        if ($lang === '') {
            $lang = Language::name();
        }
        $lang = array_key_exists($lang, reset($datas)) ? $lang : 'en';
        $key = str_pad((string) $iso, 2, '0', STR_PAD_LEFT);

        return isset($datas[$key]) ? $datas[$key][$lang] : '';
    }

    /**
     * Find the province code by name.
     */
    public static function isoFromProvince($province, $lang = '', $country = 'RU')
    {
        $datas = self::init($country);
        if (empty($datas)) {
            return '';
        }
        if ($lang === '') {
            $lang = Language::name();
        }
        $lang = array_key_exists($lang, reset($datas)) ? $lang : 'en';
        foreach ($datas as $iso => $items) {
            if ($items[$lang] === $province) {
                return $iso;
            }
        }

        return '';
    }

    /**
     * Russian federal subjects with Russian and English captions.
     */
    private static function RU()
    {
        return [
            '01' => ['ru' => 'Республика Адыгея', 'en' => 'Republic of Adygea'],
            '02' => ['ru' => 'Республика Башкортостан', 'en' => 'Republic of Bashkortostan'],
            '03' => ['ru' => 'Республика Бурятия', 'en' => 'Republic of Buryatia'],
            '04' => ['ru' => 'Республика Алтай', 'en' => 'Altai Republic'],
            '05' => ['ru' => 'Республика Дагестан', 'en' => 'Republic of Dagestan'],
            '06' => ['ru' => 'Республика Ингушетия', 'en' => 'Republic of Ingushetia'],
            '07' => ['ru' => 'Кабардино-Балкарская Республика', 'en' => 'Kabardino-Balkaria'],
            '08' => ['ru' => 'Карачаево-Черкесская Республика', 'en' => 'Karachay-Cherkess Republic'],
            '09' => ['ru' => 'Республика Карелия', 'en' => 'Republic of Karelia'],
            '10' => ['ru' => 'Республика Коми', 'en' => 'Komi Republic'],
            '11' => ['ru' => 'Республика Марий Эл', 'en' => 'Mari El Republic'],
            '12' => ['ru' => 'Республика Мордовия', 'en' => 'Republic of Mordovia'],
            '13' => ['ru' => 'Республика Саха (Якутия)', 'en' => 'Sakha Republic (Yakutia)'],
            '14' => ['ru' => 'Республика Северная Осетия — Алания', 'en' => 'Republic of North Ossetia-Alania'],
            '15' => ['ru' => 'Республика Татарстан', 'en' => 'Republic of Tatarstan'],
            '16' => ['ru' => 'Республика Тыва', 'en' => 'Tuva Republic'],
            '17' => ['ru' => 'Удмуртская Республика', 'en' => 'Udmurt Republic'],
            '18' => ['ru' => 'Республика Хакасия', 'en' => 'Republic of Khakassia'],
            '19' => ['ru' => 'Чеченская Республика', 'en' => 'Chechen Republic'],
            '20' => ['ru' => 'Чувашская Республика', 'en' => 'Chuvash Republic'],
            '21' => ['ru' => 'Алтайский край', 'en' => 'Altai Krai'],
            '22' => ['ru' => 'Забайкальский край', 'en' => 'Zabaykalsky Krai'],
            '23' => ['ru' => 'Камчатский край', 'en' => 'Kamchatka Krai'],
            '24' => ['ru' => 'Краснодарский край', 'en' => 'Krasnodar Krai'],
            '25' => ['ru' => 'Красноярский край', 'en' => 'Krasnoyarsk Krai'],
            '26' => ['ru' => 'Пермский край', 'en' => 'Perm Krai'],
            '27' => ['ru' => 'Приморский край', 'en' => 'Primorsky Krai'],
            '28' => ['ru' => 'Ставропольский край', 'en' => 'Stavropol Krai'],
            '29' => ['ru' => 'Хабаровский край', 'en' => 'Khabarovsk Krai'],
            '30' => ['ru' => 'Амурская область', 'en' => 'Amur Oblast'],
            '31' => ['ru' => 'Архангельская область', 'en' => 'Arkhangelsk Oblast'],
            '32' => ['ru' => 'Астраханская область', 'en' => 'Astrakhan Oblast'],
            '33' => ['ru' => 'Белгородская область', 'en' => 'Belgorod Oblast'],
            '34' => ['ru' => 'Брянская область', 'en' => 'Bryansk Oblast'],
            '35' => ['ru' => 'Владимирская область', 'en' => 'Vladimir Oblast'],
            '36' => ['ru' => 'Волгоградская область', 'en' => 'Volgograd Oblast'],
            '37' => ['ru' => 'Вологодская область', 'en' => 'Vologda Oblast'],
            '38' => ['ru' => 'Воронежская область', 'en' => 'Voronezh Oblast'],
            '39' => ['ru' => 'Ивановская область', 'en' => 'Ivanovo Oblast'],
            '40' => ['ru' => 'Иркутская область', 'en' => 'Irkutsk Oblast'],
            '41' => ['ru' => 'Калининградская область', 'en' => 'Kaliningrad Oblast'],
            '42' => ['ru' => 'Калужская область', 'en' => 'Kaluga Oblast'],
            '43' => ['ru' => 'Кемеровская область', 'en' => 'Kemerovo Oblast'],
            '44' => ['ru' => 'Кировская область', 'en' => 'Kirov Oblast'],
            '45' => ['ru' => 'Костромская область', 'en' => 'Kostroma Oblast'],
            '46' => ['ru' => 'Курганская область', 'en' => 'Kurgan Oblast'],
            '47' => ['ru' => 'Курская область', 'en' => 'Kursk Oblast'],
            '48' => ['ru' => 'Ленинградская область', 'en' => 'Leningrad Oblast'],
            '49' => ['ru' => 'Липецкая область', 'en' => 'Lipetsk Oblast'],
            '50' => ['ru' => 'Магаданская область', 'en' => 'Magadan Oblast'],
            '51' => ['ru' => 'Московская область', 'en' => 'Moscow Oblast'],
            '52' => ['ru' => 'Мурманская область', 'en' => 'Murmansk Oblast'],
            '53' => ['ru' => 'Нижегородская область', 'en' => 'Nizhny Novgorod Oblast'],
            '54' => ['ru' => 'Новгородская область', 'en' => 'Novgorod Oblast'],
            '55' => ['ru' => 'Новосибирская область', 'en' => 'Novosibirsk Oblast'],
            '56' => ['ru' => 'Омская область', 'en' => 'Omsk Oblast'],
            '57' => ['ru' => 'Оренбургская область', 'en' => 'Orenburg Oblast'],
            '58' => ['ru' => 'Орловская область', 'en' => 'Oryol Oblast'],
            '59' => ['ru' => 'Пензенская область', 'en' => 'Penza Oblast'],
            '60' => ['ru' => 'Псковская область', 'en' => 'Pskov Oblast'],
            '61' => ['ru' => 'Ростовская область', 'en' => 'Rostov Oblast'],
            '62' => ['ru' => 'Рязанская область', 'en' => 'Ryazan Oblast'],
            '63' => ['ru' => 'Самарская область', 'en' => 'Samara Oblast'],
            '64' => ['ru' => 'Саратовская область', 'en' => 'Saratov Oblast'],
            '65' => ['ru' => 'Сахалинская область', 'en' => 'Sakhalin Oblast'],
            '66' => ['ru' => 'Свердловская область', 'en' => 'Sverdlovsk Oblast'],
            '67' => ['ru' => 'Смоленская область', 'en' => 'Smolensk Oblast'],
            '68' => ['ru' => 'Тамбовская область', 'en' => 'Tambov Oblast'],
            '69' => ['ru' => 'Тверская область', 'en' => 'Tver Oblast'],
            '70' => ['ru' => 'Томская область', 'en' => 'Tomsk Oblast'],
            '71' => ['ru' => 'Тульская область', 'en' => 'Tula Oblast'],
            '72' => ['ru' => 'Тюменская область', 'en' => 'Tyumen Oblast'],
            '73' => ['ru' => 'Ульяновская область', 'en' => 'Ulyanovsk Oblast'],
            '74' => ['ru' => 'Челябинская область', 'en' => 'Chelyabinsk Oblast'],
            '75' => ['ru' => 'Ярославская область', 'en' => 'Yaroslavl Oblast'],
            '76' => ['ru' => 'Город Москва', 'en' => 'Moscow'],
            '77' => ['ru' => 'Город Санкт-Петербург', 'en' => 'Saint Petersburg'],
            '78' => ['ru' => 'Город Севастополь', 'en' => 'Sevastopol'],
            '79' => ['ru' => 'Еврейская автономная область', 'en' => 'Jewish Autonomous Oblast'],
            '80' => ['ru' => 'Ненецкий автономный округ', 'en' => 'Nenets Autonomous Okrug'],
            '81' => ['ru' => 'Ханты-Мансийский автономный округ — Югра', 'en' => 'Khanty-Mansi Autonomous Okrug'],
            '82' => ['ru' => 'Чукотский автономный округ', 'en' => 'Chukotka Autonomous Okrug'],
            '83' => ['ru' => 'Ямало-Ненецкий автономный округ', 'en' => 'Yamalo-Nenets Autonomous Okrug'],
            '84' => ['ru' => 'Республика Крым', 'en' => 'Republic of Crimea'],
            '85' => ['ru' => 'Республика Калмыкия', 'en' => 'Republic of Kalmykia'],
        ];
    }
}
