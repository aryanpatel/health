<?php

/**
 * Get a log file path.
 *
 * @since 1.0.0
 * @param string $handle name.
 * @return string the log file path.
 */
function bmps_get_log_file_path($handle) {
    return trailingslashit(BMPS_LOG_DIR) . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
}

/**
 * Get an image size.
 *
 * Variable is filtered by woocommerce_get_image_size_{image_size}.
 *
 * @param mixed $image_size
 * @return array
 */
function bmps_get_image_size($image_size) {
    if (is_array($image_size)) {
        $width = isset($image_size[0]) ? $image_size[0] : '300';
        $height = isset($image_size[1]) ? $image_size[1] : '300';
        $crop = isset($image_size[2]) ? $image_size[2] : 1;

        $size = array(
            'width' => $width,
            'height' => $height,
            'crop' => $crop,
        );

        $image_size = $width . '_' . $height;
    } elseif (in_array($image_size, array('parking_thumbnail', 'parking_catalog', 'parking_single'))) {
        $size = get_option($image_size . '_image_size', array());
        $size['width'] = isset($size['width']) ? $size['width'] : '300';
        $size['height'] = isset($size['height']) ? $size['height'] : '300';
        $size['crop'] = isset($size['crop']) ? $size['crop'] : 0;
    } else {
        $size = array(
            'width' => '300',
            'height' => '300',
            'crop' => 1,
        );
    }

    return apply_filters('bmps_get_image_size_' . $image_size, $size);
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since 2.3.10 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param string $var
 * @return string
 */
function bmps_sanitize_tooltip($var) {
    return htmlspecialchars(wp_kses(html_entity_decode($var), array(
        'br' => array(),
        'em' => array(),
        'strong' => array(),
        'small' => array(),
        'span' => array(),
        'ul' => array(),
        'li' => array(),
        'ol' => array(),
        'p' => array(),
    )));
}

/**
 * Display a BMPS tool tip.
 *
 * @since  2.5.0
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function bmps_tool_tip($tip, $allow_html = false) {
    if ($allow_html) {
        $tip = bmps_sanitize_tooltip($tip);
    } else {
        $tip = esc_attr($tip);
    }

    return '<span class="bmps-tool-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Get Base Currency Code.
 *
 * @return string
 */
function get_bmps_currency() {
    return apply_filters('bmps_default_currency', get_option('bmps_default_currency'));
}

/**
 * Get full list of currency codes.
 *
 * @return array
 */
function get_bmps_currencies() {
    return array_unique(
            apply_filters('bmps_global_currencies', array(
        'AED' => __('United Arab Emirates dirham', BMPS_PLUGIN_TEXTDOMAIN),
        'AFN' => __('Afghan afghani', BMPS_PLUGIN_TEXTDOMAIN),
        'ALL' => __('Albanian lek', BMPS_PLUGIN_TEXTDOMAIN),
        'AMD' => __('Armenian dram', BMPS_PLUGIN_TEXTDOMAIN),
        'ANG' => __('Netherlands Antillean guilder', BMPS_PLUGIN_TEXTDOMAIN),
        'AOA' => __('Angolan kwanza', BMPS_PLUGIN_TEXTDOMAIN),
        'ARS' => __('Argentine peso', BMPS_PLUGIN_TEXTDOMAIN),
        'AUD' => __('Australian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'AWG' => __('Aruban florin', BMPS_PLUGIN_TEXTDOMAIN),
        'AZN' => __('Azerbaijani manat', BMPS_PLUGIN_TEXTDOMAIN),
        'BAM' => __('Bosnia and Herzegovina convertible mark', BMPS_PLUGIN_TEXTDOMAIN),
        'BBD' => __('Barbadian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'BDT' => __('Bangladeshi taka', BMPS_PLUGIN_TEXTDOMAIN),
        'BGN' => __('Bulgarian lev', BMPS_PLUGIN_TEXTDOMAIN),
        'BHD' => __('Bahraini dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'BIF' => __('Burundian franc', BMPS_PLUGIN_TEXTDOMAIN),
        'BMD' => __('Bermudian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'BND' => __('Brunei dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'BOB' => __('Bolivian boliviano', BMPS_PLUGIN_TEXTDOMAIN),
        'BRL' => __('Brazilian real', BMPS_PLUGIN_TEXTDOMAIN),
        'BSD' => __('Bahamian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'BTC' => __('Bitcoin', BMPS_PLUGIN_TEXTDOMAIN),
        'BTN' => __('Bhutanese ngultrum', BMPS_PLUGIN_TEXTDOMAIN),
        'BWP' => __('Botswana pula', BMPS_PLUGIN_TEXTDOMAIN),
        'BYR' => __('Belarusian ruble', BMPS_PLUGIN_TEXTDOMAIN),
        'BZD' => __('Belize dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'CAD' => __('Canadian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'CDF' => __('Congolese franc', BMPS_PLUGIN_TEXTDOMAIN),
        'CHF' => __('Swiss franc', BMPS_PLUGIN_TEXTDOMAIN),
        'CLP' => __('Chilean peso', BMPS_PLUGIN_TEXTDOMAIN),
        'CNY' => __('Chinese yuan', BMPS_PLUGIN_TEXTDOMAIN),
        'COP' => __('Colombian peso', BMPS_PLUGIN_TEXTDOMAIN),
        'CRC' => __('Costa Rican col&oacute;n', BMPS_PLUGIN_TEXTDOMAIN),
        'CUC' => __('Cuban convertible peso', BMPS_PLUGIN_TEXTDOMAIN),
        'CUP' => __('Cuban peso', BMPS_PLUGIN_TEXTDOMAIN),
        'CVE' => __('Cape Verdean escudo', BMPS_PLUGIN_TEXTDOMAIN),
        'CZK' => __('Czech koruna', BMPS_PLUGIN_TEXTDOMAIN),
        'DJF' => __('Djiboutian franc', BMPS_PLUGIN_TEXTDOMAIN),
        'DKK' => __('Danish krone', BMPS_PLUGIN_TEXTDOMAIN),
        'DOP' => __('Dominican peso', BMPS_PLUGIN_TEXTDOMAIN),
        'DZD' => __('Algerian dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'EGP' => __('Egyptian pound', BMPS_PLUGIN_TEXTDOMAIN),
        'ERN' => __('Eritrean nakfa', BMPS_PLUGIN_TEXTDOMAIN),
        'ETB' => __('Ethiopian birr', BMPS_PLUGIN_TEXTDOMAIN),
        'EUR' => __('Euro', BMPS_PLUGIN_TEXTDOMAIN),
        'FJD' => __('Fijian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'FKP' => __('Falkland Islands pound', BMPS_PLUGIN_TEXTDOMAIN),
        'GBP' => __('Pound sterling', BMPS_PLUGIN_TEXTDOMAIN),
        'GEL' => __('Georgian lari', BMPS_PLUGIN_TEXTDOMAIN),
        'GGP' => __('Guernsey pound', BMPS_PLUGIN_TEXTDOMAIN),
        'GHS' => __('Ghana cedi', BMPS_PLUGIN_TEXTDOMAIN),
        'GIP' => __('Gibraltar pound', BMPS_PLUGIN_TEXTDOMAIN),
        'GMD' => __('Gambian dalasi', BMPS_PLUGIN_TEXTDOMAIN),
        'GNF' => __('Guinean franc', BMPS_PLUGIN_TEXTDOMAIN),
        'GTQ' => __('Guatemalan quetzal', BMPS_PLUGIN_TEXTDOMAIN),
        'GYD' => __('Guyanese dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'HKD' => __('Hong Kong dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'HNL' => __('Honduran lempira', BMPS_PLUGIN_TEXTDOMAIN),
        'HRK' => __('Croatian kuna', BMPS_PLUGIN_TEXTDOMAIN),
        'HTG' => __('Haitian gourde', BMPS_PLUGIN_TEXTDOMAIN),
        'HUF' => __('Hungarian forint', BMPS_PLUGIN_TEXTDOMAIN),
        'IDR' => __('Indonesian rupiah', BMPS_PLUGIN_TEXTDOMAIN),
        'ILS' => __('Israeli new shekel', BMPS_PLUGIN_TEXTDOMAIN),
        'IMP' => __('Manx pound', BMPS_PLUGIN_TEXTDOMAIN),
        'INR' => __('Indian rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'IQD' => __('Iraqi dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'IRR' => __('Iranian rial', BMPS_PLUGIN_TEXTDOMAIN),
        'IRT' => __('Iranian toman', BMPS_PLUGIN_TEXTDOMAIN),
        'ISK' => __('Icelandic kr&oacute;na', BMPS_PLUGIN_TEXTDOMAIN),
        'JEP' => __('Jersey pound', BMPS_PLUGIN_TEXTDOMAIN),
        'JMD' => __('Jamaican dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'JOD' => __('Jordanian dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'JPY' => __('Japanese yen', BMPS_PLUGIN_TEXTDOMAIN),
        'KES' => __('Kenyan shilling', BMPS_PLUGIN_TEXTDOMAIN),
        'KGS' => __('Kyrgyzstani som', BMPS_PLUGIN_TEXTDOMAIN),
        'KHR' => __('Cambodian riel', BMPS_PLUGIN_TEXTDOMAIN),
        'KMF' => __('Comorian franc', BMPS_PLUGIN_TEXTDOMAIN),
        'KPW' => __('North Korean won', BMPS_PLUGIN_TEXTDOMAIN),
        'KRW' => __('South Korean won', BMPS_PLUGIN_TEXTDOMAIN),
        'KWD' => __('Kuwaiti dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'KYD' => __('Cayman Islands dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'KZT' => __('Kazakhstani tenge', BMPS_PLUGIN_TEXTDOMAIN),
        'LAK' => __('Lao kip', BMPS_PLUGIN_TEXTDOMAIN),
        'LBP' => __('Lebanese pound', BMPS_PLUGIN_TEXTDOMAIN),
        'LKR' => __('Sri Lankan rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'LRD' => __('Liberian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'LSL' => __('Lesotho loti', BMPS_PLUGIN_TEXTDOMAIN),
        'LYD' => __('Libyan dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'MAD' => __('Moroccan dirham', BMPS_PLUGIN_TEXTDOMAIN),
        'MDL' => __('Moldovan leu', BMPS_PLUGIN_TEXTDOMAIN),
        'MGA' => __('Malagasy ariary', BMPS_PLUGIN_TEXTDOMAIN),
        'MKD' => __('Macedonian denar', BMPS_PLUGIN_TEXTDOMAIN),
        'MMK' => __('Burmese kyat', BMPS_PLUGIN_TEXTDOMAIN),
        'MNT' => __('Mongolian t&ouml;gr&ouml;g', BMPS_PLUGIN_TEXTDOMAIN),
        'MOP' => __('Macanese pataca', BMPS_PLUGIN_TEXTDOMAIN),
        'MRO' => __('Mauritanian ouguiya', BMPS_PLUGIN_TEXTDOMAIN),
        'MUR' => __('Mauritian rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'MVR' => __('Maldivian rufiyaa', BMPS_PLUGIN_TEXTDOMAIN),
        'MWK' => __('Malawian kwacha', BMPS_PLUGIN_TEXTDOMAIN),
        'MXN' => __('Mexican peso', BMPS_PLUGIN_TEXTDOMAIN),
        'MYR' => __('Malaysian ringgit', BMPS_PLUGIN_TEXTDOMAIN),
        'MZN' => __('Mozambican metical', BMPS_PLUGIN_TEXTDOMAIN),
        'NAD' => __('Namibian dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'NGN' => __('Nigerian naira', BMPS_PLUGIN_TEXTDOMAIN),
        'NIO' => __('Nicaraguan c&oacute;rdoba', BMPS_PLUGIN_TEXTDOMAIN),
        'NOK' => __('Norwegian krone', BMPS_PLUGIN_TEXTDOMAIN),
        'NPR' => __('Nepalese rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'NZD' => __('New Zealand dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'OMR' => __('Omani rial', BMPS_PLUGIN_TEXTDOMAIN),
        'PAB' => __('Panamanian balboa', BMPS_PLUGIN_TEXTDOMAIN),
        'PEN' => __('Peruvian nuevo sol', BMPS_PLUGIN_TEXTDOMAIN),
        'PGK' => __('Papua New Guinean kina', BMPS_PLUGIN_TEXTDOMAIN),
        'PHP' => __('Philippine peso', BMPS_PLUGIN_TEXTDOMAIN),
        'PKR' => __('Pakistani rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'PLN' => __('Polish z&#x142;oty', BMPS_PLUGIN_TEXTDOMAIN),
        'PRB' => __('Transnistrian ruble', BMPS_PLUGIN_TEXTDOMAIN),
        'PYG' => __('Paraguayan guaran&iacute;', BMPS_PLUGIN_TEXTDOMAIN),
        'QAR' => __('Qatari riyal', BMPS_PLUGIN_TEXTDOMAIN),
        'RON' => __('Romanian leu', BMPS_PLUGIN_TEXTDOMAIN),
        'RSD' => __('Serbian dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'RUB' => __('Russian ruble', BMPS_PLUGIN_TEXTDOMAIN),
        'RWF' => __('Rwandan franc', BMPS_PLUGIN_TEXTDOMAIN),
        'SAR' => __('Saudi riyal', BMPS_PLUGIN_TEXTDOMAIN),
        'SBD' => __('Solomon Islands dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'SCR' => __('Seychellois rupee', BMPS_PLUGIN_TEXTDOMAIN),
        'SDG' => __('Sudanese pound', BMPS_PLUGIN_TEXTDOMAIN),
        'SEK' => __('Swedish krona', BMPS_PLUGIN_TEXTDOMAIN),
        'SGD' => __('Singapore dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'SHP' => __('Saint Helena pound', BMPS_PLUGIN_TEXTDOMAIN),
        'SLL' => __('Sierra Leonean leone', BMPS_PLUGIN_TEXTDOMAIN),
        'SOS' => __('Somali shilling', BMPS_PLUGIN_TEXTDOMAIN),
        'SRD' => __('Surinamese dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'SSP' => __('South Sudanese pound', BMPS_PLUGIN_TEXTDOMAIN),
        'STD' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', BMPS_PLUGIN_TEXTDOMAIN),
        'SYP' => __('Syrian pound', BMPS_PLUGIN_TEXTDOMAIN),
        'SZL' => __('Swazi lilangeni', BMPS_PLUGIN_TEXTDOMAIN),
        'THB' => __('Thai baht', BMPS_PLUGIN_TEXTDOMAIN),
        'TJS' => __('Tajikistani somoni', BMPS_PLUGIN_TEXTDOMAIN),
        'TMT' => __('Turkmenistan manat', BMPS_PLUGIN_TEXTDOMAIN),
        'TND' => __('Tunisian dinar', BMPS_PLUGIN_TEXTDOMAIN),
        'TOP' => __('Tongan pa&#x2bb;anga', BMPS_PLUGIN_TEXTDOMAIN),
        'TRY' => __('Turkish lira', BMPS_PLUGIN_TEXTDOMAIN),
        'TTD' => __('Trinidad and Tobago dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'TWD' => __('New Taiwan dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'TZS' => __('Tanzanian shilling', BMPS_PLUGIN_TEXTDOMAIN),
        'UAH' => __('Ukrainian hryvnia', BMPS_PLUGIN_TEXTDOMAIN),
        'UGX' => __('Ugandan shilling', BMPS_PLUGIN_TEXTDOMAIN),
        'USD' => __('United States dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'UYU' => __('Uruguayan peso', BMPS_PLUGIN_TEXTDOMAIN),
        'UZS' => __('Uzbekistani som', BMPS_PLUGIN_TEXTDOMAIN),
        'VEF' => __('Venezuelan bol&iacute;var', BMPS_PLUGIN_TEXTDOMAIN),
        'VND' => __('Vietnamese &#x111;&#x1ed3;ng', BMPS_PLUGIN_TEXTDOMAIN),
        'VUV' => __('Vanuatu vatu', BMPS_PLUGIN_TEXTDOMAIN),
        'WST' => __('Samoan t&#x101;l&#x101;', BMPS_PLUGIN_TEXTDOMAIN),
        'XAF' => __('Central African CFA franc', BMPS_PLUGIN_TEXTDOMAIN),
        'XCD' => __('East Caribbean dollar', BMPS_PLUGIN_TEXTDOMAIN),
        'XOF' => __('West African CFA franc', BMPS_PLUGIN_TEXTDOMAIN),
        'XPF' => __('CFP franc', BMPS_PLUGIN_TEXTDOMAIN),
        'YER' => __('Yemeni rial', BMPS_PLUGIN_TEXTDOMAIN),
        'ZAR' => __('South African rand', BMPS_PLUGIN_TEXTDOMAIN),
        'ZMW' => __('Zambian kwacha', BMPS_PLUGIN_TEXTDOMAIN),
                    )
            )
    );
}

/**
 * Get Currency symbol.
 *
 * @param string $currency (default: '')
 * @return string
 */
function get_bmps_currency_symbol($currency = '') {
    if (!$currency) {
        $currency = get_bmps_currency();
    }

    $symbols = apply_filters('bmps_currency_symbols', array(
        'AED' => '&#x62f;.&#x625;',
        'AFN' => '&#x60b;',
        'ALL' => 'L',
        'AMD' => 'AMD',
        'ANG' => '&fnof;',
        'AOA' => 'Kz',
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => '&fnof;',
        'AZN' => 'AZN',
        'BAM' => 'KM',
        'BBD' => '&#36;',
        'BDT' => '&#2547;&nbsp;',
        'BGN' => '&#1083;&#1074;.',
        'BHD' => '.&#x62f;.&#x628;',
        'BIF' => 'Fr',
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => 'Bs.',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTC' => '&#3647;',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYR' => 'Br',
        'BZD' => '&#36;',
        'CAD' => '&#36;',
        'CDF' => 'Fr',
        'CHF' => '&#67;&#72;&#70;',
        'CLP' => '&#36;',
        'CNY' => '&yen;',
        'COP' => '&#36;',
        'CRC' => '&#x20a1;',
        'CUC' => '&#36;',
        'CUP' => '&#36;',
        'CVE' => '&#36;',
        'CZK' => '&#75;&#269;',
        'DJF' => 'Fr',
        'DKK' => 'DKK',
        'DOP' => 'RD&#36;',
        'DZD' => '&#x62f;.&#x62c;',
        'EGP' => 'EGP',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '&euro;',
        'FJD' => '&#36;',
        'FKP' => '&pound;',
        'GBP' => '&pound;',
        'GEL' => '&#x10da;',
        'GGP' => '&pound;',
        'GHS' => '&#x20b5;',
        'GIP' => '&pound;',
        'GMD' => 'D',
        'GNF' => 'Fr',
        'GTQ' => 'Q',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => 'L',
        'HRK' => 'Kn',
        'HTG' => 'G',
        'HUF' => '&#70;&#116;',
        'IDR' => 'Rp',
        'ILS' => '&#8362;',
        'IMP' => '&pound;',
        'INR' => '&#8377;',
        'IQD' => '&#x639;.&#x62f;',
        'IRR' => '&#xfdfc;',
        'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
        'ISK' => 'kr.',
        'JEP' => '&pound;',
        'JMD' => '&#36;',
        'JOD' => '&#x62f;.&#x627;',
        'JPY' => '&yen;',
        'KES' => 'KSh',
        'KGS' => '&#x441;&#x43e;&#x43c;',
        'KHR' => '&#x17db;',
        'KMF' => 'Fr',
        'KPW' => '&#x20a9;',
        'KRW' => '&#8361;',
        'KWD' => '&#x62f;.&#x643;',
        'KYD' => '&#36;',
        'KZT' => 'KZT',
        'LAK' => '&#8365;',
        'LBP' => '&#x644;.&#x644;',
        'LKR' => '&#xdbb;&#xdd4;',
        'LRD' => '&#36;',
        'LSL' => 'L',
        'LYD' => '&#x644;.&#x62f;',
        'MAD' => '&#x62f;.&#x645;.',
        'MDL' => 'MDL',
        'MGA' => 'Ar',
        'MKD' => '&#x434;&#x435;&#x43d;',
        'MMK' => 'Ks',
        'MNT' => '&#x20ae;',
        'MOP' => 'P',
        'MRO' => 'UM',
        'MUR' => '&#x20a8;',
        'MVR' => '.&#x783;',
        'MWK' => 'MK',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => 'MT',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => 'C&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#x631;.&#x639;.',
        'PAB' => 'B/.',
        'PEN' => 'S/.',
        'PGK' => 'K',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PRB' => '&#x440;.',
        'PYG' => '&#8370;',
        'QAR' => '&#x631;.&#x642;',
        'RMB' => '&yen;',
        'RON' => 'lei',
        'RSD' => '&#x434;&#x438;&#x43d;.',
        'RUB' => '&#8381;',
        'RWF' => 'Fr',
        'SAR' => '&#x631;.&#x633;',
        'SBD' => '&#36;',
        'SCR' => '&#x20a8;',
        'SDG' => '&#x62c;.&#x633;.',
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&pound;',
        'SLL' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '&#36;',
        'SSP' => '&pound;',
        'STD' => 'Db',
        'SYP' => '&#x644;.&#x633;',
        'SZL' => 'L',
        'THB' => '&#3647;',
        'TJS' => '&#x405;&#x41c;',
        'TMT' => 'm',
        'TND' => '&#x62f;.&#x62a;',
        'TOP' => 'T&#36;',
        'TRY' => '&#8378;',
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => 'Sh',
        'UAH' => '&#8372;',
        'UGX' => 'UGX',
        'USD' => '&#36;',
        'UYU' => '&#36;',
        'UZS' => 'UZS',
        'VEF' => 'Bs F',
        'VND' => '&#8363;',
        'VUV' => 'Vt',
        'WST' => 'T',
        'XAF' => 'Fr',
        'XCD' => '&#36;',
        'XOF' => 'Fr',
        'XPF' => 'Fr',
        'YER' => '&#xfdfc;',
        'ZAR' => '&#82;',
        'ZMW' => 'ZK',
    ));

    $currency_symbol = isset($symbols[$currency]) ? $symbols[$currency] : '';

    return apply_filters('bmps_currency_symbol', $currency_symbol, $currency);
}

/**
 *
 * Get times as option-list.
 *
 * @return string List of times
 */
function bmps_get_business_hours($default = '10:00', $interval = '+30 minutes') {

    $output = '';

    $current = strtotime('00:00');
    $end = strtotime('24:00');

    while ($current <= $end) {
        $time = date('H:i', $current);
        $sel = ( $time == $default ) ? ' selected' : '';

        $output .= '<option value="' . $time . '" ' . $sel . '>' . date('h.i A', $current) . '</option>';
        $current = strtotime($interval, $current);
    }

    return $output;
}
