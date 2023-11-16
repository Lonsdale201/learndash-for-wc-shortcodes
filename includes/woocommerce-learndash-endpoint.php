<?php
// includes/woocommerce-learndash-endpoint.php
if (!defined('ABSPATH')) {
    exit;
}

class WooCommerce_LearnDash_Endpoint {
    private $slug;
    private $options = null;

    public function __construct() {
        add_action('init', array($this, 'add_endpoints'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_menu_item'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 100);
    }

    private function get_options() {
        if ($this->options === null) {
            $this->options = get_option('learndash_extras_settings');
        }
        return $this->options;
    }

    public function flush_rewrite_rules_on_activation() {
        flush_rewrite_rules();
    }

    public function add_endpoints() {
        $options = $this->get_options();
        if (!empty($options['enable_woocommerce_course_menu']) && !empty($options['woocommerce_course_menu_label'])) {
            $this->slug = sanitize_title($options['woocommerce_course_menu_label']);
            add_rewrite_endpoint($this->slug, EP_ROOT | EP_PAGES);
            add_action('woocommerce_account_' . $this->slug . '_endpoint', array($this, 'endpoint_content'));
        }
    }

    public function add_menu_item($items) {
        $options = $this->get_options();
        if (!empty($options['enable_woocommerce_course_menu']) && !empty($options['woocommerce_course_menu_label']) && $this->should_display_menu($options)) {
            $slug = sanitize_title($options['woocommerce_course_menu_label']);
            
            $new_items = array();
            foreach ($items as $key => $item) {
                $new_items[$key] = $item;
                if ($key === 'orders') {
                    $new_items[$slug] = $options['woocommerce_course_menu_label'];
                }
            }
            return $new_items;
        }
        return $items;
    }

    private function should_display_menu($options) {
        if ($options['woocommerce_course_menu_visibility'] === 'anybody') {
            return true;
        }

        if ($options['woocommerce_course_menu_visibility'] === 'purchased') {
            $user_id = get_current_user_id();
            $user_courses = ld_get_mycourses($user_id);
            return !empty($user_courses);
        }

        return false;
    }

    public function endpoint_content() {
        include plugin_dir_path(__FILE__) . 'woocommerce-endpoint-template.php';
    }

    public function enqueue_styles() {
        if (is_account_page()) {
            wp_enqueue_style('my-course-style', plugins_url('assets/frontend/my-course.css', dirname(__FILE__)), array(), false, 'all');
        }
    }
    
}
