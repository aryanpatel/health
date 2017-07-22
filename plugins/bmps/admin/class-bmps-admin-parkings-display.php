<?php

/**
 * Modify Parkings post type table and view/edit page
 *
 * @author   Krutarth
 * @category Admin
 * @package  BMPS/Admin
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

class BMPS_Admin_Parking_Display {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Define custom columns for parkings.
     * @param  array $existing_columns
     * @return array
     */
    public function parking_columns($existing_columns) {
        if (empty($existing_columns) && !is_array($existing_columns)) {
            $existing_columns = array();
        }

        unset($existing_columns['title'], $existing_columns['comments'], $existing_columns['date']);

        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['thumb'] = '<span class="bmps-image tips" data-tip="' . esc_attr__('Image', BMPS_PLUGIN_TEXTDOMAIN) . '">' . __('Image', BMPS_PLUGIN_TEXTDOMAIN) . '</span>';
        $columns['name'] = __('Name', BMPS_PLUGIN_TEXTDOMAIN);
        $columns['availability'] = __('Availability', BMPS_PLUGIN_TEXTDOMAIN);
        $columns['price'] = __('Price', BMPS_PLUGIN_TEXTDOMAIN);
        $columns['suitable_for'] = __('Suitable For', BMPS_PLUGIN_TEXTDOMAIN);

        return array_merge($columns, $existing_columns);
    }

    /**
     * Ouput custom columns for products.
     *
     * @param string $column
     */
    public function render_parking_columns($column) {
        global $post, $the_parking;

        if (empty($the_parking) || $the_parking->get_id() != $post->ID) {
            $the_parking = bmps_clean_get_parking($post);
        }

        // Only continue if we have a product.
        if (empty($the_parking)) {
            return;
        }

        switch ($column) {
            case 'thumb' :
                echo '<a href="' . get_edit_post_link($post->ID) . '">' . $the_product->get_image('thumbnail') . '</a>';
                break;
            case 'name' :
                $edit_link = get_edit_post_link($post->ID);
                $title = _draft_or_post_title();

                echo '<strong><a class="row-title" href="' . esc_url($edit_link) . '">' . esc_html($title) . '</a>';

                _post_states($post);

                echo '</strong>';

                if ($post->post_parent > 0) {
                    echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link($post->post_parent) . '">' . get_the_title($post->post_parent) . '</a>';
                }

                // Excerpt view
                if (isset($_GET['mode']) && 'excerpt' == $_GET['mode']) {
                    echo apply_filters('the_excerpt', $post->post_excerpt);
                }

                get_inline_data($post);

                /* Custom inline data for woocommerce. */
                echo '
					<div class="hidden" id="woocommerce_inline_' . absint($post->ID) . '">
						<div class="menu_order">' . absint($the_product->get_menu_order()) . '</div>
						<div class="sku">' . esc_html($the_product->get_sku()) . '</div>
						<div class="regular_price">' . esc_html($the_product->get_regular_price()) . '</div>
						<div class="sale_price">' . esc_html($the_product->get_sale_price()) . '</div>
						<div class="weight">' . esc_html($the_product->get_weight()) . '</div>
						<div class="length">' . esc_html($the_product->get_length()) . '</div>
						<div class="width">' . esc_html($the_product->get_width()) . '</div>
						<div class="height">' . esc_html($the_product->get_height()) . '</div>
						<div class="shipping_class">' . esc_html($the_product->get_shipping_class()) . '</div>
						<div class="visibility">' . esc_html($the_product->get_catalog_visibility()) . '</div>
						<div class="stock_status">' . esc_html($the_product->get_stock_status()) . '</div>
						<div class="stock">' . esc_html($the_product->get_stock_quantity()) . '</div>
						<div class="manage_stock">' . esc_html(wc_bool_to_string($the_product->get_manage_stock())) . '</div>
						<div class="featured">' . esc_html(wc_bool_to_string($the_product->get_featured())) . '</div>
						<div class="product_type">' . esc_html($the_product->get_type()) . '</div>
						<div class="product_is_virtual">' . esc_html(wc_bool_to_string($the_product->get_virtual())) . '</div>
						<div class="tax_status">' . esc_html($the_product->get_tax_status()) . '</div>
						<div class="tax_class">' . esc_html($the_product->get_tax_class()) . '</div>
						<div class="backorders">' . esc_html($the_product->get_backorders()) . '</div>
					</div>
				';

                break;
            case 'sku' :
                echo $the_product->get_sku() ? esc_html($the_product->get_sku()) : '<span class="na">&ndash;</span>';
                break;
            case 'product_type' :
                if ($the_product->is_type('grouped')) {
                    echo '<span class="product-type tips grouped" data-tip="' . esc_attr__('Grouped', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                } elseif ($the_product->is_type('external')) {
                    echo '<span class="product-type tips external" data-tip="' . esc_attr__('External/Affiliate', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                } elseif ($the_product->is_type('simple')) {

                    if ($the_product->is_virtual()) {
                        echo '<span class="product-type tips virtual" data-tip="' . esc_attr__('Virtual', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                    } elseif ($the_product->is_downloadable()) {
                        echo '<span class="product-type tips downloadable" data-tip="' . esc_attr__('Downloadable', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                    } else {
                        echo '<span class="product-type tips simple" data-tip="' . esc_attr__('Simple', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                    }
                } elseif ($the_product->is_type('variable')) {
                    echo '<span class="product-type tips variable" data-tip="' . esc_attr__('Variable', BMPS_PLUGIN_TEXTDOMAIN) . '"></span>';
                } else {
                    // Assuming that we have other types in future
                    echo '<span class="product-type tips ' . esc_attr(sanitize_html_class($the_product->get_type())) . '" data-tip="' . esc_attr(ucfirst($the_product->get_type())) . '"></span>';
                }
                break;
            case 'price' :
                echo $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>';
                break;
            case 'product_cat' :
            case 'product_tag' :
                if (!$terms = get_the_terms($post->ID, $column)) {
                    echo '<span class="na">&ndash;</span>';
                } else {
                    $termlist = array();
                    foreach ($terms as $term) {
                        $termlist[] = '<a href="' . admin_url('edit.php?' . $column . '=' . $term->slug . '&post_type=product') . ' ">' . $term->name . '</a>';
                    }

                    echo implode(', ', $termlist);
                }
                break;
            case 'featured' :
                $url = wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_feature_product&product_id=' . $post->ID), 'woocommerce-feature-product');
                echo '<a href="' . esc_url($url) . '" aria-label="' . __('Toggle featured', BMPS_PLUGIN_TEXTDOMAIN) . '">';
                if ($the_product->is_featured()) {
                    echo '<span class="wc-featured tips" data-tip="' . esc_attr__('Yes', BMPS_PLUGIN_TEXTDOMAIN) . '">' . __('Yes', BMPS_PLUGIN_TEXTDOMAIN) . '</span>';
                } else {
                    echo '<span class="wc-featured not-featured tips" data-tip="' . esc_attr__('No', BMPS_PLUGIN_TEXTDOMAIN) . '">' . __('No', BMPS_PLUGIN_TEXTDOMAIN) . '</span>';
                }
                echo '</a>';
                break;
            case 'is_in_stock' :

                if ($the_product->is_in_stock()) {
                    $stock_html = '<mark class="instock">' . __('In stock', BMPS_PLUGIN_TEXTDOMAIN) . '</mark>';
                } else {
                    $stock_html = '<mark class="outofstock">' . __('Out of stock', BMPS_PLUGIN_TEXTDOMAIN) . '</mark>';
                }

                if ($the_product->managing_stock()) {
                    $stock_html .= ' (' . wc_stock_amount($the_product->get_stock_quantity()) . ')';
                }

                echo apply_filters('woocommerce_admin_stock_html', $stock_html, $the_product);

                break;
            default :
                break;
        }
    }

    /**
     * Make columns sortable - https://gist.github.com/906872.
     *
     * @param  array $columns
     * @return array
     */
    public function parking_sortable_columns($columns) {
        $custom = array(
            'price' => 'price',
            'name' => 'title',
        );
        return wp_parse_args($custom, $columns);
    }

}
