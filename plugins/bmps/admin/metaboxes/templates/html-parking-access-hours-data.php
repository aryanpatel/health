<?php
/**
 * Access hours metaboxes for Parkings
 */
?>
<div id="parking_access_hours" class="panel bmps_options_panel hidden">
    <div class="options_group pricing">
        <?php
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_sunday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Sunday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_fullday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_fullday',
            'label' => __('Saturday: ', BMPS_PLUGIN_TEXTDOMAIN),
            'class' => 'hidden'
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_monday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Monday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_tuesday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Tuesday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_wednesday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Wednesday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_thursday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Thursday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_friday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Friday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        bmps_wp_checkbox(array(
            'id' => '_parking_operationl_days_saturday',
            //'value' => $product_object->get_manage_stock('edit') ? 'yes' : 'no',
            'name' => '_parking_operationl_days',
            'label' => __('Saturday: ', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        ?>
    </div>
</div>