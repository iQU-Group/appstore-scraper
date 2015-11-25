<?php

/**
 * Countries.php
 */

namespace Iqu\AppStore;

/**
 * Class that contains the official collection of country codes and names for
 *
 * @package Iqu\AppStore\Config
 * @author Evangelos Karvounis
 */
abstract class Countries
{
    private static $countries = array(
    'AE' => 'United Arab Emirates',
    'AG' => 'Antigua and Barbuda',
    'AI' => 'Anguilla',
    'AL' => 'Albania',
    'AM' => 'Armenia',
    'AO' => 'Angola',
    'AR' => 'Argentina',
    'AT' => 'Austria',
    'AU' => 'Australia',
    'AZ' => 'Azerbaijan',
    'BB' => 'Barbados',
    'BE' => 'Belgium',
    'BF' => 'Burkina Faso',
    'BG' => 'Bulgaria',
    'BH' => 'Bahrain',
    'BJ' => 'Benin',
    'BM' => 'Bermuda',
    'BN' => 'Brunei',
    'BO' => 'Bolivia',
    'BR' => 'Brazil',
    'BS' => 'Bahamas',
    'BT' => 'Bhutan',
    'BW' => 'Botswana',
    'BY' => 'Belarus',
    'BZ' => 'Belize',
    'CA' => 'Canada',
    'CG' => 'Republic Of Congo',
    'CH' => 'Switzerland',
    'CL' => 'Chile',
    'CN' => 'China',
    'CO' => 'Colombia',
    'CR' => 'Costa Rica',
    'CV' => 'Cape Verde',
    'CY' => 'Cyprus',
    'CZ' => 'Czech Republic',
    'DE' => 'Germany',
    'DK' => 'Denmark',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'DZ' => 'Algeria',
    'EC' => 'Ecuador',
    'EE' => 'Estonia',
    'EG' => 'Egypt',
    'ES' => 'Spain',
    'FI' => 'Finland',
    'FJ' => 'Fiji',
    'FM' => 'Federated States Of Micronesia',
    'FR' => 'France',
    'GB' => 'United Kingdom',
    'GD' => 'Grenada',
    'GH' => 'Ghana',
    'GM' => 'Gambia',
    'GR' => 'Greece',
    'GT' => 'Guatemala',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HK' => 'Hong Kong',
    'HN' => 'Honduras',
    'HR' => 'Croatia',
    'HU' => 'Hungary',
    'ID' => 'Indonesia',
    'IE' => 'Ireland',
    'IL' => 'Israel',
    'IN' => 'India',
    'IS' => 'Iceland',
    'IT' => 'Italy',
    'JM' => 'Jamaica',
    'JO' => 'Jordan',
    'JP' => 'Japan',
    'KE' => 'Kenya',
    'KG' => 'Kyrgyzstan',
    'KH' => 'Cambodia',
    'KN' => 'St Kitts and Nevis',
    'KR' => 'Republic Of Korea',
    'KW' => 'Kuwait',
    'KY' => 'Cayman Islands',
    'KZ' => 'Kazakstan',
    'LA' => 'Lao People’s Democratic Republic',
    'LB' => 'Lebanon',
    'LC' => 'St. Lucia',
    'LK' => 'Sri Lanka',
    'LR' => 'Liberia',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'LV' => 'Latvia',
    'MD' => 'Republic Of Moldova',
    'MG' => 'Madagascar',
    'MK' => 'Macedonia',
    'ML' => 'Mali',
    'MN' => 'Mongolia',
    'MO' => 'Macau',
    'MR' => 'Mauritania',
    'MS' => 'Montserrat',
    'MT' => 'Malta',
    'MU' => 'Mauritius',
    'MW' => 'Malawi',
    'MX' => 'Mexico',
    'MY' => 'Malaysia',
    'MZ' => 'Mozambique',
    'NA' => 'Namibia',
    'NE' => 'Niger',
    'NG' => 'Nigeria',
    'NI' => 'Nicaragua',
    'NL' => 'Netherlands',
    'NO' => 'Norway',
    'NP' => 'Nepal',
    'NZ' => 'New Zealand',
    'OM' => 'Oman',
    'PA' => 'Panama',
    'PE' => 'Peru',
    'PG' => 'Papua New Guinea',
    'PH' => 'Philippines',
    'PK' => 'Pakistan',
    'PL' => 'Poland',
    'PT' => 'Portugal',
    'PW' => 'Palau',
    'PY' => 'Paraguay',
    'QA' => 'Qatar',
    'RO' => 'Romania',
    'RU' => 'Russia',
    'SA' => 'Saudi Arabia',
    'SB' => 'Solomon Islands',
    'SC' => 'Seychelles',
    'SE' => 'Sweden',
    'SG' => 'Singapore',
    'SI' => 'Slovenia',
    'SK' => 'Slovakia',
    'SL' => 'Sierra Leone',
    'SN' => 'Senegal',
    'SR' => 'Suriname',
    'ST' => 'Sao Tome and Principe',
    'SV' => 'El Salvador',
    'SZ' => 'Swaziland',
    'TC' => 'Turks and Caicos',
    'TD' => 'Chad',
    'TH' => 'Thailand',
    'TJ' => 'Tajikistan',
    'TM' => 'Turkmenistan',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TT' => 'Trinidad and Tobago',
    'TW' => 'Taiwan',
    'TZ' => 'Tanzania',
    'UA' => 'Ukraine',
    'UG' => 'Uganda',
    'US' => 'United States',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VC' => 'St. Vincent and The Grenadines',
    'VE' => 'Venezuela',
    'VG' => 'British Virgin Islands',
    'VN' => 'Vietnam',
    'YE' => 'Yemen',
    'ZA' => 'South Africa',
    'ZW' => 'Zimbabwe'
    );

    /**
     * Returns the complete collection of countries and their codes.
     *
     * @return array Country code as key, Country name as value
     */
    public static function getCountries()
    {
        return self::$countries;
    }

    /**
     * Checks if a given country code exists in the collection (case-insensitive).
     *
     * @param string $countryCode
     * @return bool
     */
    public static function checkIfValidCountryCode($countryCode)
    {
        return array_key_exists(strtoupper($countryCode), self::$countries);
    }

    /**
     *  Checks if a given country name exists in the collection of countries (case-insensitive).
     *  If it exists, returns the country's code.
     *  If not, returns an empty string.
     *
     * @param string $countryName
     * @return string
     */
    public static function getCountryCode($countryName)
    {
        foreach (self::$countries as $key => $value) {
            if (!strcasecmp($value, $countryName)) {
                return $key;
            }
        }
        return '';
    }
}
