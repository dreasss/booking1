<?php
/**
 * @filesource Kotchasan/Country.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 */

namespace Kotchasan;

/**
 * Utility helpers for working with ISO country names in the supported locales.
 */
class Country
{
    /**
     * @var array<string, string> Base list of country names in English.
     */
    private static $countries = [
        'AC' => 'Ascension Island',
        'AD' => 'Andorra',
        'AE' => 'United Arab Emirates',
        'AF' => 'Afghanistan',
        'AG' => 'Antigua & Barbuda',
        'AI' => 'Anguilla',
        'AL' => 'Albania',
        'AM' => 'Armenia',
        'AO' => 'Angola',
        'AQ' => 'Antarctica',
        'AR' => 'Argentina',
        'AS' => 'American Samoa',
        'AT' => 'Austria',
        'AU' => 'Australia',
        'AW' => 'Aruba',
        'AX' => 'Aland Islands',
        'AZ' => 'Azerbaijan',
        'BA' => 'Bosnia & Herzegovina',
        'BB' => 'Barbados',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BF' => 'Burkina Faso',
        'BG' => 'Bulgaria',
        'BH' => 'Bahrain',
        'BI' => 'Burundi',
        'BJ' => 'Benin',
        'BL' => 'St. Barthélemy',
        'BM' => 'Bermuda',
        'BN' => 'Brunei',
        'BO' => 'Bolivia',
        'BQ' => 'Caribbean Netherlands',
        'BR' => 'Brazil',
        'BS' => 'Bahamas',
        'BT' => 'Bhutan',
        'BV' => 'Bouvet Island',
        'BW' => 'Botswana',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'CA' => 'Canada',
        'CC' => 'Cocos (Keeling) Islands (Kepulauan Cocos)',
        'CD' => 'Congo (DRC)',
        'CF' => 'Central African Republic',
        'CG' => 'Congo (Republic)',
        'CH' => 'Switzerland',
        'CI' => 'Côte d’Ivoire',
        'CK' => 'Cook Islands',
        'CL' => 'Chile',
        'CM' => 'Cameroon',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CP' => 'Clipperton Island',
        'CR' => 'Costa Rica',
        'CU' => 'Cuba',
        'CV' => 'Cape Verde',
        'CW' => 'Curaçao',
        'CX' => 'Christmas Island',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DE' => 'Germany',
        'DG' => 'Diego Garcia',
        'DJ' => 'Djibouti',
        'DK' => 'Denmark',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'DZ' => 'Algeria',
        'EA' => 'Ceuta & Melilla',
        'EC' => 'Ecuador',
        'EE' => 'Estonia',
        'EG' => 'Egypt',
        'EH' => 'Western Sahara',
        'ER' => 'Eritrea',
        'ES' => 'Spain',
        'ET' => 'Ethiopia',
        'FI' => 'Finland',
        'FJ' => 'Fiji',
        'FK' => 'Falkland Islands',
        'FM' => 'Micronesia',
        'FO' => 'Faroe Islands',
        'FR' => 'France',
        'GA' => 'Gabon',
        'GB' => 'United Kingdom',
        'GD' => 'Grenada',
        'GE' => 'Georgia',
        'GF' => 'French Guiana',
        'GG' => 'Guernsey',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GL' => 'Greenland',
        'GM' => 'Gambia',
        'GN' => 'Guinea',
        'GP' => 'Guadeloupe',
        'GQ' => 'Equatorial Guinea',
        'GR' => 'Greece',
        'GS' => 'South Georgia & South Sandwich Islands',
        'GT' => 'Guatemala',
        'GU' => 'Guam',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HK' => 'Hong Kong',
        'HM' => 'Heard & McDonald Islands',
        'HN' => 'Honduras',
        'HR' => 'Croatia',
        'HU' => 'Hungary',
        'IC' => 'Canary Islands',
        'ID' => 'Indonesia',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IM' => 'Isle of Man',
        'IN' => 'India',
        'IO' => 'British Indian Ocean Territory',
        'IQ' => 'Iraq',
        'IR' => 'Iran',
        'IS' => 'Iceland',
        'IT' => 'Italy',
        'JE' => 'Jersey',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'KE' => 'Kenya',
        'KG' => 'Kyrgyzstan',
        'KH' => 'Cambodia',
        'KI' => 'Kiribati',
        'KM' => 'Comoros',
        'KN' => 'St. Kitts & Nevis',
        'KP' => 'North Korea',
        'KR' => 'South Korea',
        'KW' => 'Kuwait',
        'KY' => 'Cayman Islands',
        'KZ' => 'Kazakhstan',
        'LA' => 'Laos',
        'LB' => 'Lebanon',
        'LC' => 'St. Lucia',
        'LI' => 'Liechtenstein',
        'LK' => 'Sri Lanka',
        'LR' => 'Liberia',
        'LS' => 'Lesotho',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'LY' => 'Libya',
        'MA' => 'Morocco',
        'MC' => 'Monaco',
        'MD' => 'Moldova',
        'ME' => 'Montenegro',
        'MF' => 'St. Martin',
        'MG' => 'Madagascar',
        'MH' => 'Marshall Islands',
        'MK' => 'Macedonia (FYROM)',
        'ML' => 'Mali',
        'MM' => 'Myanmar (Burma)',
        'MN' => 'Mongolia',
        'MO' => 'Macau',
        'MP' => 'Northern Mariana Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MS' => 'Montserrat',
        'MT' => 'Malta',
        'MU' => 'Mauritius',
        'MV' => 'Maldives',
        'MW' => 'Malawi',
        'MX' => 'Mexico',
        'MY' => 'Malaysia',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NC' => 'New Caledonia',
        'NE' => 'Niger',
        'NF' => 'Norfolk Island',
        'NG' => 'Nigeria',
        'NI' => 'Nicaragua',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'NP' => 'Nepal',
        'NR' => 'Nauru',
        'NU' => 'Niue',
        'NZ' => 'New Zealand',
        'OM' => 'Oman',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PF' => 'French Polynesia',
        'PG' => 'Papua New Guinea',
        'PH' => 'Philippines',
        'PK' => 'Pakistan',
        'PL' => 'Poland',
        'PM' => 'St. Pierre & Miquelon',
        'PN' => 'Pitcairn Islands',
        'PR' => 'Puerto Rico',
        'PS' => 'Palestine',
        'PT' => 'Portugal',
        'PW' => 'Palau',
        'PY' => 'Paraguay',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RS' => 'Serbia',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        'SA' => 'Saudi Arabia',
        'SB' => 'Solomon Islands',
        'SC' => 'Seychelles',
        'SD' => 'Sudan',
        'SE' => 'Sweden',
        'SG' => 'Singapore',
        'SH' => 'St. Helena',
        'SI' => 'Slovenia',
        'SJ' => 'Svalbard & Jan Mayen',
        'SK' => 'Slovakia',
        'SL' => 'Sierra Leone',
        'SM' => 'San Marino',
        'SN' => 'Senegal',
        'SO' => 'Somalia',
        'SR' => 'Suriname',
        'SS' => 'South Sudan',
        'ST' => 'São Tomé & Príncipe',
        'SV' => 'El Salvador',
        'SX' => 'Sint Maarten',
        'SY' => 'Syria',
        'SZ' => 'Swaziland',
        'TA' => 'Tristan da Cunha',
        'TC' => 'Turks & Caicos Islands',
        'TD' => 'Chad',
        'TF' => 'French Southern Territories',
        'TG' => 'Togo',
        'TH' => 'Thailand',
        'TJ' => 'Tajikistan',
        'TK' => 'Tokelau',
        'TL' => 'Timor-Leste',
        'TM' => 'Turkmenistan',
        'TN' => 'Tunisia',
        'TO' => 'Tonga',
        'TR' => 'Turkey',
        'TT' => 'Trinidad & Tobago',
        'TV' => 'Tuvalu',
        'TW' => 'Taiwan',
        'TZ' => 'Tanzania',
        'UA' => 'Ukraine',
        'UG' => 'Uganda',
        'UM' => 'U.S. Outlying Islands',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VA' => 'Vatican City',
        'VC' => 'St. Vincent & Grenadines',
        'VE' => 'Venezuela',
        'VG' => 'British Virgin Islands',
        'VI' => 'U.S. Virgin Islands',
        'VN' => 'Vietnam',
        'VU' => 'Vanuatu',
        'WF' => 'Wallis & Futuna',
        'WS' => 'Samoa',
        'XK' => 'Kosovo',
        'YE' => 'Yemen',
        'YT' => 'Mayotte',
        'ZA' => 'South Africa',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',    ];

    /**
     * Returns the country name for the given ISO code.
     *
     * @param string      $iso  ISO 3166-1 alpha-2 code
     * @param string|null $lang Optional language override
     */
    public static function get($iso, $lang = null)
    {
        $iso = strtoupper($iso);
        if (!isset(self::$countries[$iso])) {
            return '';
        }
        $language = $lang ?: Language::name();

        return self::resolveLabel($iso, $language, self::$countries[$iso]);
    }

    /**
     * Returns the complete list of country names in the active language.
     *
     * @return array<string, string>
     */
    public static function all()
    {
        $language = Language::name();
        $result = [];
        foreach (self::$countries as $iso => $name) {
            $result[$iso] = self::resolveLabel($iso, $language, $name);
        }
        asort($result, SORT_LOCALE_STRING);

        return $result;
    }

    /**
     * Resolve a localized label using ICU if available, falling back to English names.
     */
    private static function resolveLabel($iso, $language, $fallback)
    {
        $iso = strtoupper($iso);
        $fallback = $fallback ?: (self::$countries[$iso] ?? '');
        $locales = [];
        if ($language === 'ru') {
            $locales[] = 'ru_RU';
            $locales[] = 'ru';
        } elseif ($language === 'en') {
            $locales[] = 'en_US';
            $locales[] = 'en';
        } elseif ($language) {
            $locales[] = $language;
        }
        $locales[] = 'en';
        foreach (array_unique($locales) as $locale) {
            $label = self::intlRegionName($iso, $locale);
            if (!empty($label)) {
                return $label;
            }
        }

        return $fallback;
    }

    /**
     * Use the intl extension to obtain a localized country name.
     */
    private static function intlRegionName($iso, $locale)
    {
        if (!class_exists('\\Locale')) {
            return '';
        }
        $name = \Locale::getDisplayRegion('-'.strtoupper($iso), $locale);
        if (empty($name) || $name === '-'.strtoupper($iso)) {
            return '';
        }

        return $name;
    }
}
