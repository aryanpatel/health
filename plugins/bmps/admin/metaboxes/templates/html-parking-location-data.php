<?php
/**
 * General setting metaboxes for Parkings
 */
?>
<div id="parking_location_option" class="panel bmps_options_panel hidden">
    <div class="options_group location">
        <?php
        bmps_wp_textarea_input(array(
            'id' => '_parking_location',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_location',
            'desc_tip' => 'true',
            'label' => __('Location of parking space: ', BMPS_PLUGIN_TEXTDOMAIN),
            'description' => __('Please enter the location of parking space so users can find it easily.', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_text_input(array(
            'id' => '_google_map_link',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_google_map_link',
            'desc_tip' => 'true',
            'label' => __('Google map link: ', BMPS_PLUGIN_TEXTDOMAIN),
            'description' => __('Google Map link to your parking .', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        ?>
    </div>
</div>