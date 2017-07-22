<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BMPS_Meta_Box_Parking_Images {

    public static function render($post) {
        ?>
        <div id="parking_images_container">
            <ul class="parking_images">
                <?php
                if (metadata_exists('post', $post->ID, '_parking_image_gallery')) {
                    $parking_image_gallery = get_post_meta($post->ID, '_parking_image_gallery', true);
                } else {
                    $attachment_ids = get_posts('post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_bmps_exclude_image&meta_value=0');
                    $attachment_ids = array_diff($attachment_ids, array(get_post_thumbnail_id()));
                    $parking_image_gallery = implode(',', $attachment_ids);
                }
                $attachments = array_filter(explode(',', $parking_image_gallery));
                $update_meta = false;
                $updated_gallery_ids = array();

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment_id) {
                        $attachment = wp_get_attachment_image($attachment_id, 'thumbnail');
                        // if attachment is empty skip
                        if (empty($attachment)) {
                            $update_meta = true;
                            continue;
                        }
                        echo '<li class="image" data-attachment_id="' . esc_attr($attachment_id) . '">
								' . $attachment . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . esc_attr__('Delete image', BMPS_PLUGIN_TEXTDOMAIN) . '">' . __('Delete', BMPS_PLUGIN_TEXTDOMAIN) . '</a></li>
								</ul>
							</li>';

                        // rebuild ids to be saved
                        $updated_gallery_ids[] = $attachment_id;
                    }
                    // need to update product meta to set new gallery ids
                    if ($update_meta) {
                        update_post_meta($post->ID, '_parking_image_gallery', implode(',', $updated_gallery_ids));
                    }
                }
                ?>
            </ul>
            <input type="hidden" id="product_image_gallery" name="_parking_image_gallery" value="<?php echo esc_attr($parking_image_gallery); ?>" />
        </div>
        <p class="add_parking_images hide-if-no-js">
            <a href="#" data-choose="<?php esc_attr_e('Add images to parking gallery', BMPS_PLUGIN_TEXTDOMAIN); ?>" data-update="<?php esc_attr_e('Add to gallery', BMPS_PLUGIN_TEXTDOMAIN); ?>" data-delete="<?php esc_attr_e('Delete image', BMPS_PLUGIN_TEXTDOMAIN); ?>" data-text="<?php esc_attr_e('Delete', 'woocommerce'); ?>"><?php _e('Add parking gallery images', BMPS_PLUGIN_TEXTDOMAIN); ?></a>
        </p>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @param int $post_id
     * @param WP_Post $post
     */
    public static function save($post_id, $post) {
        $attachment_ids = isset($_POST['_parking_image_gallery']) ? array_filter(explode(',', wc_clean($_POST['_parking_image_gallery']))) : array();

        update_post_meta($post_id, '_parking_image_gallery', implode(',', $attachment_ids));
    }

}
