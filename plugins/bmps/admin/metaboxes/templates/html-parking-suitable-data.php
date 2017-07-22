<?php
/**
 * Suitable for setting metaboxes for Parkings
 */
?>
<div id="parking_suitable_for" class="panel bmps_options_panel hidden">
    <div class="options_group pricing">
        <?php
        bmps_wp_select(array(
            'id' => '_parking_suitable_for',
            //'value' => $parking_object->get_tax_status('edit'),
            'label' => __('Suitable For', BMPS_PLUGIN_TEXTDOMAIN),
            'options' => array(
                'small' => __('Small - (2 Door)', BMPS_PLUGIN_TEXTDOMAIN),
                'medium' => __('Medium - (4 Door)', BMPS_PLUGIN_TEXTDOMAIN),
                'large' => __('Large - (4x4)', BMPS_PLUGIN_TEXTDOMAIN),
                'van' => __('Van', BMPS_PLUGIN_TEXTDOMAIN),
                'minibus' => __('MiniBus', BMPS_PLUGIN_TEXTDOMAIN),
                'bus' => __('Bus', BMPS_PLUGIN_TEXTDOMAIN),
                'lorry' => __('Lorry', BMPS_PLUGIN_TEXTDOMAIN),
                'motercycle' => __('Moter Cycle', BMPS_PLUGIN_TEXTDOMAIN),
            ),
            'class' => 'select',
            'multiple' => 'multiple',
            'desc_tip' => 'true',
            'description' => __('Choose for which type of vehicles your parking is suitable for. You can choose multiple.', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        ?>
    </div>
</div>
