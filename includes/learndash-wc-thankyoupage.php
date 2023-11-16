<?php
// learndash-wc-thankyoupage.php

class WooCommerce_LearnDash_ThankyouPage {
    public function __construct() {
        add_action('woocommerce_order_details_before_order_table', array($this, 'add_custom_message_before_order_details'));
    }

    public function add_custom_message_before_order_details($order_id) {
        if ($this->check_for_learndash_course_in_order($order_id)) {
            $options = get_option('learndash_extras_settings');
            $thankyou_message = $options['learndash_thankyou_message'] ?? '';

            if (!empty($thankyou_message)) {
               
                $slug = isset($options['woocommerce_course_menu_label']) ? sanitize_title($options['woocommerce_course_menu_label']) : '';

                $my_account_page_id = get_option('woocommerce_myaccount_page_id');
                if ($my_account_page_id) {
                    $my_account_page_url = get_permalink($my_account_page_id);
                    $url = trailingslashit($my_account_page_url) . $slug;
                } else {
                    // Alapértelmezett URL
                    $url = site_url('my-account/' . $slug);
                }

                // Smart link cseréje
                $thankyou_message = str_replace('{fiokom}', '<a href="' . esc_url($url) . '">' . esc_html($options['woocommerce_course_menu_label']) . '</a>', $thankyou_message);

                echo '<div class="custom-thankyou-message">' . wp_kses_post($thankyou_message) . '</div>';
            }
        }
    }
    

    private function check_for_learndash_course_in_order($order_id) {
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $course_id = get_post_meta($product_id, '_related_course', true);

            if (!empty($course_id)) {
                return true;
            }
        }

        return false;
    }

    private function replace_smart_link_with_endpoint_url($text) {
        $options = get_option('learndash_extras_settings');
        if (!empty($options['woocommerce_course_menu_label'])) {
            $slug = sanitize_title($options['woocommerce_course_menu_label']);
            $endpoint_url = wc_get_account_endpoint_url($slug);
            $text = str_replace('{fiokom}', $endpoint_url, $text);
        }
        return $text;
    }
}
