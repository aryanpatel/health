<?php

/**
 * BMPS General Settings
 *
 * @author      Krutarth
 * @category    Admin
 * @package     BMPS/Admin
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('WC_Settings_General', false)) :

    class BMPS_General_Settings extends BMPS_Settings_Main {

        /**
         * Constructor.
         */
        public function __construct() {

            $this->id = 'general';
            $this->label = __('General', BMPS_PLUGIN_TEXTDOMAIN);

            add_filter('bmps_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('bmps_settings_' . $this->id, array($this, 'render'));
            add_action('bmps_settings_save_' . $this->id, array($this, 'save'));
        }

        /**
         * Get settings array.
         *
         * @return array
         */
        public function get_settings() {

            $currency_code_options = get_bmps_currencies();
            foreach ($currency_code_options as $code => $name) {
                $currency_code_options[$code] = $name . ' (' . get_bmps_currency_symbol($code) . ')';
            }

            $settings = apply_filters('bmps_general_settings', array(
                array('title' => __('General options', BMPS_PLUGIN_TEXTDOMAIN), 'type' => 'title', 'desc' => '', 'id' => 'general_options'),
                array(
                    'title' => __('Base Location', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('This is the base location for your business. Tax rates will be based on this country.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_default_country',
                    'css' => 'min-width:350px;',
                    'default' => 'GB',
                    'type' => 'single_select_country',
                    'desc_tip' => true,
                    'parentclass' => ''
                ),
                //For multiple location
//                array(
//                    'title' => __('Other Locations', BMPS_PLUGIN_TEXTDOMAIN),
//                    'desc' => __('Do you operate at other locations', BMPS_PLUGIN_TEXTDOMAIN),
//                    'id' => 'bmps_other_locations',
//                    'default' => 'no',
//                    'type' => 'checkbox',
//                ),
//                array(
//                    'title' => __('Choose other countries where you operate', BMPS_PLUGIN_TEXTDOMAIN),
//                    'desc' => '',
//                    'id' => 'bmps_specific_allowed_countries',
//                    'css' => 'min-width: 350px;',
//                    'default' => '',
//                    'type' => 'multi_select_countries',
//                    'parentclass' => 'hide'
//                ),
                array(
                    'title' => __('Enable taxes', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('Do you want to enable taxes and tax calculations?', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_calc_taxes',
                    'default' => 'no',
                    'type' => 'checkbox',
                ),
                array('type' => 'sectionend', 'id' => 'pricing_options'),
                array('title' => __('Currency options', BMPS_PLUGIN_TEXTDOMAIN), 'type' => 'title', 'desc' => __('This will change how price will be displayed to customers.', BMPS_PLUGIN_TEXTDOMAIN), 'id' => 'pricing_options'),
                array(
                    'title' => __('Default Currency', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('Default currency you want to accept payments in.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_currency',
                    'css' => 'min-width:350px;',
                    'default' => 'USD',
                    'type' => 'select',
                    'class' => 'bmps-enhanced-select',
                    'desc_tip' => true,
                    'options' => $currency_code_options,
                ),
                array(
                    'title' => __('Currency position', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('This controls the position of the currency symbol.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_currency_pos',
                    'css' => 'min-width:350px;',
                    'class' => 'wc-enhanced-select',
                    'default' => 'left',
                    'type' => 'select',
                    'options' => array(
                        'left' => __('Left', BMPS_PLUGIN_TEXTDOMAIN) . ' (' . get_bmps_currency_symbol() . '99.99)',
                        'right' => __('Right', BMPS_PLUGIN_TEXTDOMAIN) . ' (99.99' . get_bmps_currency_symbol() . ')',
                        'left_space' => __('Left with space', BMPS_PLUGIN_TEXTDOMAIN) . ' (' . get_bmps_currency_symbol() . ' 99.99)',
                        'right_space' => __('Right with space', BMPS_PLUGIN_TEXTDOMAIN) . ' (99.99 ' . get_bmps_currency_symbol() . ')',
                    ),
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Thousand separator', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('This sets the thousand separator of displayed prices.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_price_thousand_sep',
                    'css' => 'width:50px;',
                    'default' => ',',
                    'type' => 'text',
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Decimal separator', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('This sets the decimal separator of displayed prices.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_price_decimal_sep',
                    'css' => 'width:50px;',
                    'default' => '.',
                    'type' => 'text',
                    'desc_tip' => true,
                ),
                array(
                    'title' => __('Number of decimals', BMPS_PLUGIN_TEXTDOMAIN),
                    'desc' => __('This sets the number of decimal points shown in displayed prices.', BMPS_PLUGIN_TEXTDOMAIN),
                    'id' => 'bmps_price_num_decimals',
                    'css' => 'width:50px;',
                    'default' => '2',
                    'desc_tip' => true,
                    'type' => 'number',
                    'custom_attributes' => array(
                        'min' => 0,
                        'step' => 1,
                    ),
                ),
                array('type' => 'sectionend', 'id' => 'pricing_options'),
            ));

            return apply_filters('bmps_get_settings_' . $this->id, $settings);
        }

        /**
         * Save settings.
         */
        public function save() {
            $settings = $this->get_settings();

            BMPS_Admin_Settings::save_fields($settings);
        }

    }

    endif;

return new BMPS_General_Settings();
