<?php
/**
 * General setting metaboxes for Parkings
 */
?>
<div id="parking_pricing_option" class="panel bmps_options_panel">
    <div class="options_group pricing">
        <div class="parking_type_option">
            <?php
            bmps_wp_checkbox(array(
                'id' => '_rental_type_daily',
                //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
                'desc_tip' => 'true',
                'label' => __('Daily: ', BMPS_PLUGIN_TEXTDOMAIN),
                'description' => __('This parking is only available for Daily booking.', BMPS_PLUGIN_TEXTDOMAIN),
            ));
            bmps_wp_checkbox(array(
                'id' => '_rental_type_monthly',
                //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
                'desc_tip' => 'true',
                'label' => __('Monthly: ', BMPS_PLUGIN_TEXTDOMAIN),
                'description' => __('This Parking is only Available for Monthly Booking', BMPS_PLUGIN_TEXTDOMAIN),
            ));
            ?>
        </div>
        <div class="show_if_daily show_if_both hide">
            <?php
            bmps_wp_text_input(array(
                'id' => '_hourly_price',
                //'value' => $parking_object->get_hourly_price('edit'),
                'label' => __('Price Per Hour(optional)', BMPS_PLUGIN_TEXTDOMAIN) . ' (' . get_bmps_currency_symbol() . ')',
                'data_type' => 'price',
                'desc_tip' => 'true',
                'description' => __('What is hourly charge for this parking', BMPS_PLUGIN_TEXTDOMAIN)
            ));
            bmps_wp_text_input(array(
                'id' => '_daily_price',
                //'value' => $parking_object->get_daily_price('edit'),
                'label' => __('Price Per Day', BMPS_PLUGIN_TEXTDOMAIN) . ' (' . get_bmps_currency_symbol() . ')',
                'data_type' => 'price',
                'desc_tip' => 'true',
                'description' => __('What is daily charge for this parking', BMPS_PLUGIN_TEXTDOMAIN)
            ));
            ?>
        </div>
        <div class="show_if_monthly show_if_both hide">
            <?php
            bmps_wp_text_input(array(
                'id' => '_monthly_price',
                //'value' => $parking_object->get_monthly_price('edit'),
                'label' => __('Price Per Month', BMPS_PLUGIN_TEXTDOMAIN) . ' (' . get_bmps_currency_symbol() . ')',
                'data_type' => 'price',
                'desc_tip' => 'true',
                'description' => __('What is monthly charge for this parking', BMPS_PLUGIN_TEXTDOMAIN)
            ));
            ?>
        </div>
        <?php
        do_action('bmps_parking_pricing_options');
        ?>
    </div>
    <div class="option_parking_slot">
        <?php
        bmps_wp_text_input(array(
            'id' => '_parking_slots',
            //'value' => $parking_object->get_monthly_price('edit'),
            'value' => 0,
            'label' => __('No. of Spaces', BMPS_PLUGIN_TEXTDOMAIN),
            'type' => 'number',
            'desc_tip' => 'true',
            'description' => __('No of Parking spaces you get for above price.', BMPS_PLUGIN_TEXTDOMAIN)
        ));
        ?>
    </div>
    <?php if (bmps_tax_enabled()) : ?>
        <div class="options_group show_if_simple show_if_external show_if_variable">
            <?php
            bmps_wp_select(array(
                'id' => '_tax_status',
                'value' => $parking_object->get_tax_status('edit'),
                'label' => __('Tax status', BMPS_PLUGIN_TEXTDOMAIN),
                'options' => array(
                    'taxable' => __('Taxable', BMPS_PLUGIN_TEXTDOMAIN),
                    'shipping' => __('Shipping only', BMPS_PLUGIN_TEXTDOMAIN),
                    'none' => _x('None', 'Tax status', BMPS_PLUGIN_TEXTDOMAIN),
                ),
                'multiple' => 'multiple',
                'desc_tip' => 'true',
                'description' => __('Define whether or not the entire product is taxable, or just the cost of shipping it.', BMPS_PLUGIN_TEXTDOMAIN),
            ));

            bmps_wp_select(array(
                'id' => '_tax_class',
                'value' => $parking_object->get_tax_class('edit'),
                'label' => __('Tax class', BMPS_PLUGIN_TEXTDOMAIN),
                'options' => wc_get_product_tax_class_options(),
                'desc_tip' => 'true',
                'description' => __('Choose a tax class for this parking.', BMPS_PLUGIN_TEXTDOMAIN),
            ));

            do_action('bmps_parking_tax_options');
            ?>
        </div>
    <?php endif; ?>

    <?php do_action('bmps_parking_options_parking_pricing_data'); ?>
</div>
