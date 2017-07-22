<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function bmps_format_currency_to_decimal( $value, $decimal_number = NULL, $decimal_saperator = NULL, $thousand_saperator = NULL){
    $decimal_saperator  = empty($decimal_saperator) ? get_option('bmps_price_decimal_sep') : $decimal_saperator;
    $decimal_number     = empty($decimal_number) ? get_option('bmps_price_num_decimals') : $decimal_number;
    $thousand_saperator = empty( $thousand_saperator ) ? get_option('bmps_price_thousand_sep') : $thousand_saperator;
    return number_format($value, $decimal_number, $decimal_saperator, $thousand_saperator);
}

/**
 * Format a price with BMPS Currency Locale settings.
 * @param  string $value
 * @return string
 */
function bmps_format_localized_price($value) {
    return str_replace('.', bmps_price_decimal_separator(), strval($value));
}

/**
 * Return the decimal separator for prices.
 * @since  1.0
 * @return string
 */
function bmps_price_decimal_separator() {
    $separator = apply_filters('bmps_price_decimal_separator', get_option('bmps_price_decimal_sep'));
    return $separator ? stripslashes($separator) : '.';
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * @param string|array $var
 * @return string|array
 */
function bmps_clean($var) {
    if (is_array($var)) {
        return array_map('bmps_clean', $var);
    } else {
        return is_scalar($var) ? sanitize_text_field($var) : $var;
    }
}

/**
 * Formats currency symbols when saved in settings.
 * @param  string $value
 * @param  array $option
 * @param  string $raw_value
 * @return string
 */
function bmps_format_option_price_separators($value, $option, $raw_value) {
    return wp_kses_post($raw_value);
}

add_filter('bmps_admin_settings_sanitize_option_bmps_price_decimal_sep', 'bmps_format_option_price_separators', 10, 3);
add_filter('bmps_admin_settings_sanitize_option_bmps_price_thousand_sep', 'bmps_format_option_price_separators', 10, 3);

/**
 * Formats decimals when saved in settings.
 * @param  string $value
 * @param  array $option
 * @param  string $raw_value
 * @return string
 */
function bmps_format_option_price_num_decimals($value, $option, $raw_value) {
    return is_null($raw_value) ? 2 : absint($raw_value);
}

add_filter('bmps_admin_settings_sanitize_option_bmps_price_num_decimals', 'bmps_format_option_price_num_decimals', 10, 3);

/**
 * Formats hold stock option and sets cron event up.
 * @param  string $value
 * @param  array $option
 * @param  string $raw_value
 * @return string
 */
function bmps_format_option_hold_stock_minutes($value, $option, $raw_value) {
    $value = !empty($raw_value) ? absint($raw_value) : ''; // Allow > 0 or set to ''

    wp_clear_scheduled_hook('bmps_cancel_unpaid_bookings');

    if ('' !== $value) {
        wp_schedule_single_event(time() + ( absint($value) * 60 ), 'bmps_cancel_unpaid_bookings');
    }

    return $value;
}

add_filter('bmps_admin_settings_sanitize_option_bmps_hold_stock_minutes', 'bmps_format_option_hold_stock_minutes', 10, 3);
