<?php

if (!function_exists('bmps_tax_enabled')) {

    /**
     * Are store-wide taxes enabled?
     * @return bool
     */
    function bmps_tax_enabled() {
        return apply_filters('bmps_tax_enabled', get_option('bmps_calc_taxes') === 'yes');
    }

}