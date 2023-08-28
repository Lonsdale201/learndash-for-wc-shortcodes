<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


function ld_wc_display_product_name($atts) {
    global $post;
    $user_id = get_current_user_id();


    // Set default attributes
    $default_atts = array(
        'title' => 'true',
        'image' => 'false',
        'badge' => 'false',
        'price' => 'true',
        'stock' => 'false',
        'addtocart' => 'Kosárhoz adás',
        'shortdesc' => 'false',
        'customlabel' => '',
        'separator' => 'false',
        'fallbackimg' => '',
        'onsuccess-text' => "Termék a kosárban",
        'access-text' => '',
        'access-link' => 'false',
        'footer' => '',
        'outofstock' => 'A kurzus már nem megvásárolható.'
    );
    
    $atts = shortcode_atts($default_atts, $atts, 'ld_wc_product_name');

    // Get the course ID from the post meta
    $course_id = $post->ID;


    // Get the product ID from the post meta
    $product_id = get_post_meta($post->ID, 'ld_for_wc_product', true);

    // If there is no product ID, return empty string
    if (!$product_id) {
        return '';
    }

    // Get the product post object
    $product_post = get_post($product_id);

    // If the product post doesn't exist or is not published, return empty string
    if (!$product_post || $product_post->post_status != 'publish') {
        return '';
    }

    // Get the product object
    $product = wc_get_product($product_id);

    // Check if the product is variable
    $is_variable = $product->is_type('variable');

    $is_nyp = 'no';  // default value
    if (function_exists('WC_Name_Your_Price')) {
    $is_nyp = get_post_meta($product_id, '_nyp', true);
    }

    // Check if the product is on sale
    $is_on_sale = $product->is_on_sale();

    // Initialize the output variable
    $output = '<div class="wc-ld-wrapper">';

   // Check if the product is out of stock
    if (!$product->is_in_stock()) {
    // If the product is out of stock, display the out of stock text
    $output .= '<div class="wc-ld-outofstock">' . esc_html($atts['outofstock']);

    // Check if the user has access to the course
    if (ld_course_check_user_access($course_id, get_current_user_id())) {
        // Add the custom message within a span with class "extraaccess"
        $output .= ' <span class="extraaccess">De továbbra is van hozzáférésed.</span>';
    }

    $output .= '</div>'; // Close the wc-ld-outofstock div

    $output .= '</div>'; // Close the wc-ld-wrapper div
    return $output;
}



    // If the image attribute is set to true, get the product image or fallback image
    if ($atts['image'] == 'true') {
        $output .= '<div class="wc-ld-image-wrapper">';

        if (has_post_thumbnail($product_id)) {
            // There is a featured image, display it
            $output .= get_the_post_thumbnail($product_id, 'full');
        } else if (!empty($atts['fallbackimg'])) {
            // There is no featured image, but a fallback image URL is provided
            $fallback_img_url = esc_url($atts['fallbackimg']);
            $output .= '<img src="' . $fallback_img_url . '" alt="' . esc_attr($product_post->post_title) . '">';
        
        }

        // If the product is on sale and the badge attribute is not empty, display the badge
        if ($is_on_sale && !empty($atts['badge'])) {
            $badge_text = $atts['badge'];

            // If the badge text contains 's%', replace it with the actual sale percentage
            if (strpos($badge_text, 's%') !== false) {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                $badge_text = str_replace('s%', $percentage . '%', $badge_text);
            }

            $output .= '<div class="wc-ld-badge">' . esc_html($badge_text) . '</div>';
        }
        
        $output .= '</div>';  // Close the wc-ld-image-wrapper div
    }

    // If the title attribute is set to true, get the product name
    if ($atts['title'] == 'true') {
        $output .= '<h3 class="wc-ld-title">' . esc_html($product_post->post_title) . '</h3>';
    }

     // If the 'stock' attribute is set to true, display the stock quantity
     if ($atts['stock'] == 'true') {
        $stock_quantity = $product->get_stock_quantity();
        if ($stock_quantity !== null && $stock_quantity > 0) {
            $output .= '<span class="wc-ld-stock">' . esc_html($stock_quantity) . ' készleten</span>';
        }
    }

    // If the 'shortdesc' attribute is set to true, get the product short description
    if ($atts['shortdesc'] == 'true') {
        $output .= '<div class="wc-ld-shortdesc">' . $product->get_short_description() . '</div>';
    }
    // Display the product price if 'price' attribute is true
    // Check if the user does not have access to the course
    if (!ld_course_check_user_access($course_id, $user_id)) {
        if ($atts['price'] == 'true') {
            $price = $product->get_price_html();
            $output .= '<div class="wc-ld-price">' . $price . '</div>';
        }
    }


    if (!empty($atts['customlabel'])) {
        $output .= '<span class="wc-ld-custom-label">' . esc_html($atts['customlabel']) . '</span>';
    }

    if ($atts['separator'] == 'true') {
        $output .= '<div class="wc-ld-separator"></div>';
    }
    
     // Check if the user has access to the course
        // If the "access-text" attribute is defined
       // Check if the user has access to the course
        if (ld_course_check_user_access($course_id, $user_id)) {
            if (!empty($atts['access-text'])) {
                $output .= '<div class="wc-ld-already-have-access">';
                if ($atts['access-link'] == 'true') {
                    $course_link = get_permalink($course_id); // Get the course single page link
                    $output .= '<a href="' . esc_url($course_link) . '">' . esc_html($atts['access-text']) . '</a>';
                } else {
                    $output .= esc_html($atts['access-text']);
                }
                $output .= '</div>';
            }
            } else {
            if ($is_variable || ($is_nyp == 'yes')) {
                $output .= '<a href="' . esc_url(get_permalink($product_id)) . '" class="wc-ld-add-to-cart button alt wc-ld-variable-product-link"><span class="add-to-cart-text">' . esc_html('Opciók választása') . '</span></a>';
            } else {
                // Add to cart button for simple products
                $output .= '<button class="wc-ld-add-to-cart button alt" data-product_id="' . $product_id . '" data-on-success-text="' . $atts['onsuccess-text'] . '"><span class="add-to-cart-text">' . $atts['addtocart'] . '</span></button>';
            }
        }

        if (!empty($atts['footer'])) {
            $output .= '<span class="wc-ld-footer-text">' . esc_html($atts['footer']) . '</span>';
        }

        // Close the wrapper div
        $output .= '</div>';

        return $output;

        }
add_shortcode('ld_wc_product_name', 'ld_wc_display_product_name');


// prevent systems

function ld_wc_has_bought_product($product_id) {
    $customer_orders = get_posts(array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => get_current_user_id(),
        'post_type' => wc_get_order_types(),
        'post_status' => array('wc-on-hold', 'wc-completed', 'wc-processing'), // Check only these order statuses
        'suppress_filters' => true,
    ));

    foreach ($customer_orders as $customer_order) {
        $order = wc_get_order($customer_order);
        $items = $order->get_items();

        foreach ($items as $item) {
            if ($item['product_id'] == $product_id) {
                return true;
            }
        }
    }

    return false;
}


// we do not allow more than one cart to be added

function ld_wc_only_one_course_in_cart( $passed, $product_id, $quantity ) {
    // Get the product object
    $product = wc_get_product($product_id);

    // Check if the product is a course
    $is_course = false;
    $args = array(
        'post_type' => 'sfwd-courses',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'ld_for_wc_product',
                'value' => $product_id,
                'compare' => '=',
            )
        )
    );
    $courses = get_posts($args);
    if (!empty($courses)) {
        $is_course = true;
    }

    if ($is_course) {
        // If there are contents in the cart
        if ( WC()->cart->get_cart_contents_count() > 0 ) {
            // Cycle through each item in the cart
            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                if ( $product_id == $values['product_id'] ) {
                    $course_name = !empty($courses) ? $courses[0]->post_title : 'Ismeretlen kurzus'; 
                    wc_add_notice(sprintf(__('Ebből a termékből ("%s") csak egyet adhatsz hozzá a kosaradhoz', 'woocommerce'), $course_name), 'error');
                    $passed = false;
                    break;
                }
            }
        }
    }

    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'ld_wc_only_one_course_in_cart', 10, 3);
