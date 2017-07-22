<?php
/**
 * Features setting metaboxes for Parkings
 */
?>
<div id="parking_features_options" class="panel bmps_options_panel hidden">
    <div class="options_group pricing">
        <?php
        bmps_wp_select(array(
            'id' => '_parking_features',
            //'value' => $parking_object->get_tax_status('edit'),
            'label' => __('Parking Features', BMPS_PLUGIN_TEXTDOMAIN),
            'options' => array(
                'fullday' => __('24/7 Access', BMPS_PLUGIN_TEXTDOMAIN),
                'shelter' => __('Sheltered Parking', BMPS_PLUGIN_TEXTDOMAIN),
                'secureentry' => __('Security Gates', BMPS_PLUGIN_TEXTDOMAIN),
                'arrangedtransfer' => __('Arranged Transfer', BMPS_PLUGIN_TEXTDOMAIN),
                'allocatedspace' => __('Allocated Space', BMPS_PLUGIN_TEXTDOMAIN),
                'wash' => __('Car Wash', BMPS_PLUGIN_TEXTDOMAIN),
                'cctv' => __('CCTV', BMPS_PLUGIN_TEXTDOMAIN),
                'securitylight' => __('Security Lighting', BMPS_PLUGIN_TEXTDOMAIN),
                'guards' => __('Security Guards', BMPS_PLUGIN_TEXTDOMAIN),
                'key' => __('Security key', BMPS_PLUGIN_TEXTDOMAIN),
                'underground' => __('Underground Parking', BMPS_PLUGIN_TEXTDOMAIN),
                'restrooms' => __('Restrooms', BMPS_PLUGIN_TEXTDOMAIN),
                
            ),
            'class' => 'select',
            'multiple' => 'multiple',
            'desc_tip' => 'true',
            'description' => __('Choose the features you provide. Its plus for businner and you can choose multiple.', BMPS_PLUGIN_TEXTDOMAIN),
        ));
        ?>
    </div>
</div>
