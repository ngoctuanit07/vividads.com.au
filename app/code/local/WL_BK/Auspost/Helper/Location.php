<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Helper_Location
  * @description    Extended from the Mage_Core_Helper_Abstract class, give methods to provide location functions which are used in AUSPOST extension models, blocks, templates ...
 */

class WL_Auspost_Helper_Location extends Mage_Core_Helper_Abstract
{

/**
 *
 * Gets AUS state code by state name
 * 
 * @param    string $code The state name
 * @return   string The state code
 */
 
    public function getStateCodeByName ($name)
    {
        $states = array(
			'Australian Capital Territory' => 'ACT',
			'New South Wales' => 'NSW',
			'Northern Territory' => 'NT',
			'Queensland' => 'QLD',
			'South Australia' => 'SA',
            'Victoria' => 'VIC',
            'Western Australia' => 'WA'
        );
        if (isset($states[$name]))
            return $states[$name];
        return $name;
    }
    
/**
 *
 * Gets AUS state name by state code
 * 
 * @param    string $code The state code
 * @return   string The state name
 */

    public function getStateNameByCode ($code)
    {
        $states = array(
			'ACT'=>'Australian Capital Territory',
			'NSW'=>'New South Wales',
			'NT'=>'Northern Territory',
			'QLD'=>'Queensland',
			'SA'=>'South Australia',
            'VIC'=>'Victoria',
            'WA'=>'Western Australia'
        );
        if (isset($states[$code]))
            return $states[$code];
        return null;
    }
    
/**
 *
 * Gets country name by country code
 * 
 * @param    string $code The country code
 * @return   string The country name
 */

    public function getCountryNameByCode ($code)
    {
        $countries = array(
			'DZ'=>'Algeria',
			'AO'=>'Angola',
			'BJ'=>'Benin',
			'BW'=>'Botswana',
			'BF'=>'Burkina Faso',
			'BI'=>'Burundi',
			'CM'=>'Cameroon',
			'CV'=>'Cape Verde',
			'CF'=>'Central African Republic',
			'TD'=>'Chad',
			'KM'=>'Comoros',
			'CD'=>'Congo [DRC]',
			'CG'=>'Congo [Republic]',
			'DJ'=>'Djibouti',
			'EG'=>'Egypt',
			'GQ'=>'Equatorial Guinea',
			'ER'=>'Eritrea',
			'ET'=>'Ethiopia',
			'GA'=>'Gabon',
			'GM'=>'Gambia',
			'GH'=>'Ghana',
			'GN'=>'Guinea',
			'GW'=>'Guinea-Bissau',
			'CI'=>'Ivory Coast',
			'KE'=>'Kenya',
			'LS'=>'Lesotho',
			'LR'=>'Liberia',
			'LY'=>'Libya',
			'MG'=>'Madagascar',
			'MW'=>'Malawi',
			'ML'=>'Mali',
			'MR'=>'Mauritania',
			'MU'=>'Mauritius',
			'YT'=>'Mayotte',
			'MA'=>'Morocco',
			'MZ'=>'Mozambique',
			'NA'=>'Namibia',
			'NE'=>'Niger',
			'NG'=>'Nigeria',
			'RW'=>'Rwanda',
			'RE'=>'Réunion',
			'SH'=>'Saint Helena',
			'SN'=>'Senegal',
			'SC'=>'Seychelles',
			'SL'=>'Sierra Leone',
			'SO'=>'Somalia',
			'ZA'=>'South Africa',
			'SD'=>'Sudan',
			'SZ'=>'Swaziland',
			'ST'=>'São Tomé and Príncipe',
			'TZ'=>'Tanzania',
			'TG'=>'Togo',
			'TN'=>'Tunisia',
			'UG'=>'Uganda',
			'EH'=>'Western Sahara',
			'ZM'=>'Zambia',
			'ZW'=>'Zimbabwe',
			'AQ'=>'Antarctica',
			'BV'=>'Bouvet Island',
			'TF'=>'French Southern Territories',
			'HM'=>'Heard Island and McDonald Island',
			'GS'=>'South Georgia and the South Sandwich Islands',
			'AF'=>'Afghanistan',
			'AM'=>'Armenia',
			'AZ'=>'Azerbaijan',
			'BH'=>'Bahrain',
			'BD'=>'Bangladesh',
			'BT'=>'Bhutan',
			'IO'=>'British Indian Ocean Territory',
			'BN'=>'Brunei',
			'KH'=>'Cambodia',
			'CN'=>'China',
			'CX'=>'Christmas Island',
			'CC'=>'Cocos [Keeling] Islands',
			'GE'=>'Georgia',
			'HK'=>'Hong Kong',
			'IN'=>'India',
			'ID'=>'Indonesia',
			'IR'=>'Iran',
			'IQ'=>'Iraq',
			'IL'=>'Israel',
			'JP'=>'Japan',
			'JO'=>'Jordan',
			'KZ'=>'Kazakhstan',
			'KW'=>'Kuwait',
			'KG'=>'Kyrgyzstan',
			'LA'=>'Laos',
			'LB'=>'Lebanon',
			'MO'=>'Macau',
			'MY'=>'Malaysia',
			'MV'=>'Maldives',
			'MN'=>'Mongolia',
			'MM'=>'Myanmar [Burma]',
			'NP'=>'Nepal',
			'KP'=>'North Korea',
			'OM'=>'Oman',
			'PK'=>'Pakistan',
			'PS'=>'Palestinian Territories',
			'PH'=>'Philippines',
			'QA'=>'Qatar',
			'SA'=>'Saudi Arabia',
			'SG'=>'Singapore',
			'KR'=>'South Korea',
			'LK'=>'Sri Lanka',
			'SY'=>'Syria',
			'TW'=>'Taiwan',
			'TJ'=>'Tajikistan',
			'TH'=>'Thailand',
			'TR'=>'Turkey',
			'TM'=>'Turkmenistan',
			'AE'=>'United Arab Emirates',
			'UZ'=>'Uzbekistan',
			'VN'=>'Vietnam',
			'YE'=>'Yemen',
			'AL'=>'Albania',
			'AD'=>'Andorra',
			'AT'=>'Austria',
			'BY'=>'Belarus',
			'BE'=>'Belgium',
			'BA'=>'Bosnia and Herzegovina',
			'BG'=>'Bulgaria',
			'HR'=>'Croatia',
			'CY'=>'Cyprus',
			'CZ'=>'Czech Republic',
			'DK'=>'Denmark',
			'EE'=>'Estonia',
			'FO'=>'Faroe Islands',
			'FI'=>'Finland',
			'FR'=>'France',
			'DE'=>'Germany',
			'GI'=>'Gibraltar',
			'GR'=>'Greece',
			'GG'=>'Guernsey',
			'HU'=>'Hungary',
			'IS'=>'Iceland',
			'IE'=>'Ireland',
			'IM'=>'Isle of Man',
			'IT'=>'Italy',
			'JE'=>'Jersey',
			'XK'=>'Kosovo',
			'LV'=>'Latvia',
			'LI'=>'Liechtenstein',
			'LT'=>'Lithuania',
			'LU'=>'Luxembourg',
			'MK'=>'Macedonia',
			'MT'=>'Malta',
			'MD'=>'Moldova',
			'MC'=>'Monaco',
			'ME'=>'Montenegro',
			'NL'=>'Netherlands',
			'NO'=>'Norway',
			'PL'=>'Poland',
			'PT'=>'Portugal',
			'RO'=>'Romania',
			'RU'=>'Russia',
			'SM'=>'San Marino',
			'RS'=>'Serbia',
			'CS'=>'Serbia and Montenegro',
			'SK'=>'Slovakia',
			'SI'=>'Slovenia',
			'ES'=>'Spain',
			'SJ'=>'Svalbard and Jan Mayen',
			'SE'=>'Sweden',
			'CH'=>'Switzerland',
			'UA'=>'Ukraine',
			'GB'=>'United Kingdom',
			'VA'=>'Vatican City',
			'AX'=>'Åland Islands',
			'AI'=>'Anguilla',
			'AG'=>'Antigua and Barbuda',
			'AW'=>'Aruba',
			'BS'=>'Bahamas',
			'BB'=>'Barbados',
			'BZ'=>'Belize',
			'BM'=>'Bermuda',
			'BQ'=>'Bonaire, Saint Eustatius and Saba',
			'VG'=>'British Virgin Islands',
			'CA'=>'Canada',
			'KY'=>'Cayman Islands',
			'CR'=>'Costa Rica',
			'CU'=>'Cuba',
			'CW'=>'Curacao',
			'DM'=>'Dominica',
			'DO'=>'Dominican Republic',
			'SV'=>'El Salvador',
			'GL'=>'Greenland',
			'GD'=>'Grenada',
			'GP'=>'Guadeloupe',
			'GT'=>'Guatemala',
			'HT'=>'Haiti',
			'HN'=>'Honduras',
			'JM'=>'Jamaica',
			'MQ'=>'Martinique',
			'MX'=>'Mexico',
			'MS'=>'Montserrat',
			'AN'=>'Netherlands Antilles',
			'NI'=>'Nicaragua',
			'PA'=>'Panama',
			'PR'=>'Puerto Rico',
			'BL'=>'Saint Barthélemy',
			'KN'=>'Saint Kitts and Nevis',
			'LC'=>'Saint Lucia',
			'MF'=>'Saint Martin',
			'PM'=>'Saint Pierre and Miquelon',
			'VC'=>'Saint Vincent and the Grenadines',
			'SX'=>'Sint Maarten',
			'TT'=>'Trinidad and Tobago',
			'TC'=>'Turks and Caicos Islands',
			'VI'=>'U.S. Virgin Islands',
			'US'=>'United States',
			'AR'=>'Argentina',
			'BO'=>'Bolivia',
			'BR'=>'Brazil',
			'CL'=>'Chile',
			'CO'=>'Colombia',
			'EC'=>'Ecuador',
			'FK'=>'Falkland Islands',
			'GF'=>'French Guiana',
			'GY'=>'Guyana',
			'PY'=>'Paraguay',
			'PE'=>'Peru',
			'SR'=>'Suriname',
			'UY'=>'Uruguay',
			'VE'=>'Venezuela',
			'AS'=>'American Samoa',
			'AU'=>'Australia',
			'CK'=>'Cook Islands',
			'TL'=>'East Timor',
			'FJ'=>'Fiji',
			'PF'=>'French Polynesia',
			'GU'=>'Guam',
			'KI'=>'Kiribati',
			'MH'=>'Marshall Islands',
			'FM'=>'Micronesia',
			'NR'=>'Nauru',
			'NC'=>'New Caledonia',
			'NZ'=>'New Zealand',
			'NU'=>'Niue',
			'NF'=>'Norfolk Island',
			'MP'=>'Northern Mariana Islands',
			'PW'=>'Palau',
			'PG'=>'Papua New Guinea',
			'PN'=>'Pitcairn Islands',
			'WS'=>'Samoa',
			'SB'=>'Solomon Islands',
			'TK'=>'Tokelau',
			'TO'=>'Tonga',
			'TV'=>'Tuvalu',
			'UM'=>'U.S. Minor Outlying Islands',
			'VU'=>'Vanuatu',
			'WF'=>'Wallis and Futuna'
		);
        
        if (isset($countries[$code]))
            return $countries[$code];
        return null;
    }
    

}