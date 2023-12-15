<?php
/*
Plugin Name: HelloWP | LearnDash for WooCommerce Extras
Plugin URI: https://github.com/Lonsdale201/learndash-for-wc-shortcodes
Description: Speciális kiegészítő ami további kombinációkat biztosít a Learndash és WooCommerce-hez.
Version: 2.0-beta3
Author: Soczó Kristóf
Author URI: https://github.com/Lonsdale201/learndash-for-wc-shortcodes
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class LD_For_WC {
    public function __construct() {
        add_action('add_meta_boxes_sfwd-courses', array($this, 'add_meta_box'));
        add_action('save_post_sfwd-courses', array($this, 'save_meta_box'));
        add_action('wp_enqueue_scripts', array($this, 'ld_wc_enqueue_scripts'));
        add_action('wp_ajax_woocommerce_ajax_add_to_cart', array($this, 'woocommerce_ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array($this, 'woocommerce_ajax_add_to_cart'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));

        add_filter('manage_sfwd-courses_posts_columns', array($this, 'ld_for_wc_add_product_column'));
        add_action('manage_sfwd-courses_posts_custom_column', array($this, 'ld_for_wc_show_product_column'), 10, 2);

        add_action('admin_init', array($this, 'check_wc_status'));

        // new visibility system
        require_once plugin_dir_path(__FILE__) . 'includes/learndash-visibility-manager.php';
        new LearnDash_Visibility_Manager();

         // new menu visibility system
         require_once plugin_dir_path(__FILE__) . 'includes/learndash-menu-visibility-manager.php';
         new LearnDash_Menu_Visibility_Manager();

        // new settings system
        require_once plugin_dir_path(__FILE__) . 'includes/learndash-for-settings.php';
        new LearnDash_for_Settings();

        // new shortcodes
        require_once plugin_dir_path(__FILE__) . 'includes/learndash-extra-shortcodes.php';
        new LearnDash_extra_Shortcodes();

        // new endpoint system
        require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-learndash-endpoint.php';
        new WooCommerce_LearnDash_Endpoint();

        // new thankyou page message
        require_once plugin_dir_path(__FILE__) . 'includes/learndash-wc-thankyoupage.php';
        new WooCommerce_LearnDash_ThankyouPage();

        // Include the shortcode file
        include plugin_dir_path(__FILE__) . 'wc-ld-shortcode.php';
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=learndash-extras') . '">Beállítások</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function ld_wc_enqueue_scripts() {
        // Register the script
        wp_register_script('ld-wc-add-to-cart', plugins_url('assets/js/ld-wc-add-to-cart.js', __FILE__), array('jquery'), '1.0', true);
    
        // Create nonce
        $nonce = wp_create_nonce('woocommerce-ajax_add_to_cart-nonce');
    
        // Localize the script
        wp_localize_script('ld-wc-add-to-cart', 'wc_add_to_cart_params_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $nonce,
        ));

        // Get the cart status
        $cart_status = $this->get_cart_status();

        // Localize the script
        wp_localize_script('ld-wc-add-to-cart', 'cartStatus', $cart_status);
        
        // Enqueued script with localized data
        wp_enqueue_script('ld-wc-add-to-cart');
    
        // Enqueue custom CSS file
        wp_enqueue_style('ld-wc-customcss', plugins_url('assets/css/lc-dc-customcss.css', __FILE__));
    }
    

    public function woocommerce_ajax_add_to_cart() {
        $nonce= $_POST['nonce'];
       

        if ( ! wp_verify_nonce( $nonce, 'woocommerce-ajax_add_to_cart-nonce' ) ) {
            die( __( 'Security check', 'textdomain' ) );
        }

        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
        $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    
        if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity)) {
            do_action('woocommerce_ajax_added_to_cart', $product_id);
    
            if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
                wc_add_to_cart_message(array($product_id => $quantity), true);
            }
    
            WC_AJAX::get_refreshed_fragments();
        } else {
            $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));
    
            echo wp_send_json($data);
        }
    
        wp_die();
    }

    public function get_cart_status() {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_status = array();
    
        foreach ($items as $item => $values) {
            $product_id = $values['data']->get_id();
            $cart_status[$product_id] = true;
        }
    
        return $cart_status;
    }
    


    public function add_meta_box() {
        add_meta_box(
            'ld_for_wc_product_selector',       
            'WC Termék kiválasztása',           
            array($this, 'display_meta_box'),  
            'sfwd-courses',                   
            'side'                              
        );
    }

    public function display_meta_box($post) {
        
        wp_nonce_field('ld_for_wc_save', 'ld_for_wc_nonce');

      
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => ['publish', 'draft']
        );
        $products = get_posts($args);

        // Get the current selected product ID
        $selected_product_id = get_post_meta($post->ID, 'ld_for_wc_product', true);

        // Display select box
        echo '<select name="ld_for_wc_product" class="ld_for_wc_product_selector">';
        echo '<option></option>'; // Empty option for placeholder
        foreach ($products as $product) {
            $selected = '';
            if ($product->ID == $selected_product_id) {
                $selected = ' selected';
            }
            echo '<option value="' . $product->ID . '"' . $selected . '>' . $product->post_title . ' (' . $product->post_name . ')</option>';
        }
        echo '</select>';

        // Inlinejs to Select2
        echo '
        <script>
        jQuery(document).ready(function($) {
            $(".ld_for_wc_product_selector").select2({
                placeholder: "Termék keresése",
                allowClear: true,
                width: "100%"
            });
        });
        </script>';
    }

    public function save_meta_box($post_id) {
        
        if (!isset($_POST['ld_for_wc_nonce'])) {
            return;
        }

       
        if (!wp_verify_nonce($_POST['ld_for_wc_nonce'], 'ld_for_wc_save')) {
            return;
        }

        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize user input.
        $ld_for_wc_product_id = sanitize_text_field($_POST['ld_for_wc_product']);

        // Update the meta field in the database.
        update_post_meta($post_id, 'ld_for_wc_product', $ld_for_wc_product_id);
    }

    public function ld_for_wc_add_product_column($columns) {
        $new_columns = array();
        $i = 0;
        foreach ($columns as $key => $value) {
            if ($i == 2) { 
                $new_columns['ld_for_wc_product'] = 'Hozzárendelt termék';
            }
            $new_columns[$key] = $value;
            $i++;
        }
    
        
        if ($i <= 3) {
            $new_columns['ld_for_wc_product'] = 'WooCommerce Product';
        }
    
        return $new_columns;
    }
    
    
    public function ld_for_wc_show_product_column($column, $post_id) {
        if ($column == 'ld_for_wc_product') {
            // Get the product ID from the post meta
            $product_id = get_post_meta($post_id, 'ld_for_wc_product', true);
    
            // If there is a product ID, get the product name and status
            if ($product_id) {
                $product_post = get_post($product_id);
    
                // If the product post exists and is published, display the product name and link
                if ($product_post && $product_post->post_status == 'publish') {
                    $product_name = $product_post->post_title;
                    $edit_link = get_edit_post_link($product_id);
                    echo '<a href="' . esc_url($edit_link) . '" target="_blank">' . esc_html($product_name) . '</a>';
                }
            }
        }
    }
    
    function ld_for_wc_admin_notice() {
        $class = 'notice notice-warning is-dismissible';
        $message = __('A LearnDash for WooCommerce Shortcodes bővítmény megfelelő működéséhez először telepítsük és aktiváljuk a WooCommerce és LearnDash bővítményeket.', 'ld-for-wc');
    
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    public function check_wc_status() {
        if (!class_exists('WooCommerce') || !class_exists('SFWD_LMS')) {
            add_action('admin_notices', array($this, 'ld_for_wc_admin_notice_missing_plugins'));
        }
    }

    public function ld_for_wc_admin_notice_missing_plugins() {
        $class = 'notice notice-warning is-dismissible';
        $message = __('A LearnDash for WooCommerce Shortcodes bővítmény megfelelő működéséhez először telepítsük és aktiváljuk a WooCommerce és LearnDash bővítményeket.', 'ld-for-wc');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
}

function ld_for_wc_activation_check() {
    if (!class_exists('WooCommerce') || !class_exists('SFWD_LMS')) {
        add_action('admin_notices', 'ld_for_wc_admin_notice');
    }
}

new LD_For_WC();

function ld_for_wc_activate() {
    $endpoint = new WooCommerce_LearnDash_Endpoint();
    $endpoint->flush_rewrite_rules_on_activation();
}
register_activation_hook(__FILE__, 'ld_for_wc_activate');
