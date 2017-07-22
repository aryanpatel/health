<?php
/**
 * Admin View: Settings
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap bmps">
    <form method="<?php echo esc_attr(apply_filters('bmps_settings_form_method_tab_' . $current_tab, 'post')); ?>" id="mainform" action="" enctype="multipart/form-data">
        <nav class="nav-tab-wrapper bmps-nav-tab-wrapper">
            <?php
            foreach ($tabs as $name => $label) {
                echo '<a href="' . admin_url('admin.php?page=bmps-settings&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            }
            do_action('bmps_settings_tabs');
            ?>
        </nav>
        <h1 class="screen-reader-text"><?php echo esc_html($tabs[$current_tab]); ?></h1>
        <?php
        do_action('bmps_sections_' . $current_tab);

        self::show_messages();

        do_action('bmps_settings_' . $current_tab);
        ?>
        <p class="submit">
            <?php if (empty($GLOBALS['hide_save_button'])) : ?>
                <input name="save" class="button-primary bmps-save-button" type="submit" value="<?php esc_attr_e('Save changes', BMPS_PLUGIN_TEXTDOMAIN); ?>" />
            <?php endif; ?>
            <?php wp_nonce_field('bmps-settings'); ?>
        </p>
    </form>
</div>
