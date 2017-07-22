<div class="panel-wrap product_data">
    <ul class="parking_data_tabs bmps-tabs">
        <?php foreach (self::get_parking_data_tabs() as $key => $tab) : ?>
            <li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode(' ', (array) $tab['class']); ?>">
                <a href="#<?php echo $tab['target']; ?>"><span><?php echo esc_html($tab['label']); ?></span></a>
            </li>
        <?php endforeach; ?>
        <?php do_action('bmps_parking_write_panel_tabs'); ?>
    </ul>

    <?php
    self::output_tabs();
    do_action('bmps_parking_data_sections');
    wp_nonce_field('bmps_save_data', 'bmps_meta_nonce');
    ?>
    <div class="clear"></div>
</div>
