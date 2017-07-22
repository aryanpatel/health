<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BMPS_Meta_Box_Short_Description {

    /**
     * Render the metabox.
     *
     * @param WP_Post $post
     */
    public static function render($post) {

        $settings = array(
            'textarea_name' => 'excerpt',
            'quicktags' => array('buttons' => 'em,strong,link'),
            'tinymce' => array(
                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                'theme_advanced_buttons2' => '',
            ),
            'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
        );

        wp_editor(htmlspecialchars_decode($post->post_excerpt), 'excerpt', apply_filters('bmps_short_description_editor_settings', $settings));
    }

}
