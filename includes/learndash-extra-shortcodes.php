<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class LearnDash_extra_Shortcodes {
    public function __construct() {
        add_shortcode('ld_extra_lessons', array($this, 'ld_extra_lessons_shortcode'));
        add_shortcode('ld_extra_topics', array($this, 'ld_extra_topics_shortcode'));
        add_shortcode('ld_extra_quiz', array($this, 'ld_extra_quiz_shortcode'));
        add_shortcode('ld_extra_reward_points', array($this, 'ld_extra_reward_points_shortcode')); 
        add_shortcode('ld_extra_student_limit', array($this, 'ld_extra_student_limit_shortcode'));
        add_shortcode('ld_extra_access_points', array($this, 'ld_extra_access_points_shortcode'));
        add_shortcode('ld_extra_access_type', array($this, 'ld_extra_access_type_shortcode'));
        add_shortcode('ld_extra_course_category', array($this, 'ld_extra_course_category_shortcode'));
        add_shortcode('ld_extra_status', array($this, 'ld_extra_status_shortcode'));
        

        // globális
        add_shortcode('ld_extra_mypoints', array($this, 'ld_extra_mypoints_shortcode'));
        add_shortcode('ld_extra_total_courses_owned', array($this, 'ld_extra_total_courses_owned_shortcode'));
        add_shortcode('ld_extra_completed_courses_count', array($this, 'ld_extra_completed_courses_count_shortcode'));

        // course loop
        add_shortcode('ld_extra_product_price', array($this, 'ld_extra_product_price_shortcode'));

    }
    

    private function check_conditions($atts) {
        global $post;

        

        if (!is_singular('sfwd-courses') || empty($post->ID)) {
            return false;
        }

        if ($atts['loggedin'] === 'true' && !is_user_logged_in()) {
            return false;
        }

        if ($atts['enrolled'] === 'true' && !sfwd_lms_has_access($post->ID, get_current_user_id())) {
            return false;
        }

        return true;
    }

    public function ld_extra_lessons_shortcode($atts = array()) {
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false
            ),
            $atts
        );

        if (!$this->check_conditions($attributes)) {
            return '';
        }

        $lessons = learndash_get_lesson_list(get_the_ID());
        $lesson_count = count($lessons);

        return $this->generate_output($attributes, $lesson_count, 'lessons');
    }

    public function ld_extra_product_price_shortcode($atts = array()) {
        global $post;
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
            ),
            $atts
        );
    
        if (!is_singular('sfwd-courses') && !is_post_type_archive('sfwd-courses') || empty($post->ID)) {
            return '';
        }
    
        $product_id = get_post_meta($post->ID, 'ld_for_wc_product', true);
        if (empty($product_id)) {
            return '';
        }
    
        $product = wc_get_product($product_id);
        if (!$product) {
            return ''; 
        }
    
        $product_post_status = get_post_status($product_id);
        if ($product_post_status !== 'publish') {
            return ''; 
        }
    
        if (!$product->is_in_stock()) {
            return wc_get_stock_html($product); 
        }
    
        $is_nyp = function_exists('WC_Name_Your_Price') && get_post_meta($product_id, '_nyp', true) === 'yes';
        if ($is_nyp) {
            $suggested_price_html = WC_Name_Your_Price_Helpers::get_suggested_price_html($product);
            if ($suggested_price_html) {
                return $suggested_price_html;
            }
        }
    
        $price_output = $attributes['label'] ? $attributes['label'] . ' ' : '';
        if ($product->is_on_sale()) {
            $price_output .= $product->get_sale_price() ? wc_format_sale_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price())), wc_get_price_to_display($product)) : wc_price(wc_get_price_to_display($product));
        } else {
            $price_output .= wc_price(wc_get_price_to_display($product));
        }
        return $price_output;
    }
    
    
    

    public function ld_extra_topics_shortcode($atts = array()) {
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false,
                'empty' => 'true'
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        // Összesítjük a témák számát
        $lessons = learndash_get_lesson_list(get_the_ID());
        $topic_count = 0;
        foreach ($lessons as $lesson) {
            $topics = learndash_topic_dots($lesson->ID, false, 'array');
            if (is_array($topics)) {
                $topic_count += count($topics);
            }
        }
    
        if ($topic_count == 0 && $attributes['empty'] === 'false') {
            return '';
        }
    
        return $this->generate_output($attributes, $topic_count, 'topics');
    }
    

    public function ld_extra_quiz_shortcode($atts = array()) {
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false,
                'empty' => 'true' 
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        // Lekérjük a kurzus összes leckéjét
        $lessons = learndash_get_lesson_list(get_the_ID());
        $quiz_count = 0;
        foreach ($lessons as $lesson) {
            // Lekérjük a lecke kvízeit
            $lesson_quizzes = learndash_get_lesson_quiz_list($lesson->ID);
            $quiz_count += is_array($lesson_quizzes) ? count($lesson_quizzes) : 0;
    
            // Lekérjük a lecke témáit
            $topics = learndash_topic_dots($lesson->ID, false, 'array');
            if (is_array($topics)) {
                foreach ($topics as $topic) {
                    // Lekérjük a téma kvízeit
                    $topic_quizzes = learndash_get_lesson_quiz_list($topic->ID);
                    $quiz_count += is_array($topic_quizzes) ? count($topic_quizzes) : 0;
                }
            }
        }
    
        
        if ($attributes['empty'] === 'false' && $quiz_count == 0) {
            return '';
        }
    
        return $this->generate_output($attributes, $quiz_count, 'quiz');
    }    

    public function ld_extra_reward_points_shortcode($atts = array()) {
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false,
                'empty' => 'true' 
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        $reward_points = learndash_get_course_points(get_the_ID());
    
        if ($attributes['empty'] === 'false' && empty($reward_points)) {
            return '';
        }
    
        return $this->generate_output($attributes, $reward_points, 'reward-points');
    }

    public function ld_extra_access_points_shortcode($atts = array()) {
        global $post;
    
        if (!is_singular('sfwd-courses') || empty($post->ID)) {
            return '';
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        $access_points = learndash_get_setting($post, 'course_points_access');
        if (empty($access_points)) {
            return '';
        }
    
        return $this->generate_output($attributes, $access_points, 'access-points');
    }

    public function ld_extra_access_type_shortcode($atts = array()) {
        global $post;
    
        if (!is_singular('sfwd-courses') || empty($post->ID)) {
            return '';
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        $access_type = learndash_get_course_meta_setting($post->ID, 'course_price_type');
    
        switch ($access_type) {
            case 'open':
                $access_type_text = 'Nyitott';
                break;
            case 'free':
                $access_type_text = 'Ingyenes';
                break;
            case 'paynow':
                $access_type_text = 'Fizetős';
                break;
            case 'subscribe':
                $access_type_text = 'Előfizető - Ismétlődő';
                break;
            case 'closed':
                $access_type_text = 'Fizetős';
                break;
            default:
                $access_type_text = 'Ismeretlen';
        }
    
        return $this->generate_output($attributes, $access_type_text, 'access-type');
    }
    
    public function ld_extra_course_category_shortcode($atts = array()) {
        global $post;
    
        if (!is_singular('sfwd-courses') || empty($post->ID)) {
            return '';
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'icon'  => '',
                'loggedin' => false,
                'enrolled' => false,
                'linkable' => true
            ),
            $atts
        );
    
        if (!$this->check_conditions($attributes)) {
            return '';
        }
    
        $categories = get_the_terms($post->ID, 'ld_course_category');
    
        // Hibakezelés és ellenőrzés, hogy vannak-e érvényes kategóriák
        if (is_wp_error($categories) || empty($categories)) {
            return '';
        }
    
        $category_links = array();
        foreach ($categories as $category) {
            $category_name = esc_html($category->name);
            if ($attributes['linkable'] === 'true') {
                $category_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . $category_name . '</a>';
            } else {
                $category_links[] = $category_name;
            }
        }
    
        $categories_output = implode(', ', $category_links);
    
        return $this->generate_output($attributes, $categories_output, 'course-category');
    }

    public function ld_extra_status_shortcode($atts = array()) {
        global $post;
    
        if (!is_singular('sfwd-courses') || empty($post->ID)) {
            return '';
        }
    
        $user_id = get_current_user_id();
        if (!sfwd_lms_has_access($post->ID, $user_id)) {
            return '';
        }
    
        $course_progress = learndash_user_get_course_progress($user_id, $post->ID, 'legacy');
        $course_status = isset($course_progress['status']) ? $course_progress['status'] : 'unknown';
    
        $status_class = 'course-status-' . $course_status;
        $status_text = 'Ismeretlen';
        switch ($course_status) {
            case 'completed':
                $status_text = 'Elvégezve';
                break;
            case 'not_started':
                $status_text = 'Nincs elkezdve';
                break;
            case 'in_progress':
                $status_text = 'Folyamatban';
                break;
        }
    
        
        $content = '<span class="' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
    
        return $this->generate_output($atts, $content, 'status');
    }
    


    // globális shortcodeok
    public function ld_extra_mypoints_shortcode($atts = array()) {
        if (!is_user_logged_in()) {
            return '';
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'empty' => 'true' 
            ),
            $atts
        );
    
        $user_id = get_current_user_id();
        $user_points = learndash_get_user_course_points($user_id);
    
        if ($attributes['empty'] === 'false' && $user_points == 0) {
            return '';
        }
    
        $output = '<div class="ld-extra-mypoints-wrapper">';
        if (!empty($attributes['label'])) {
            $output .= '<span class="label">' . esc_html($attributes['label']) . '</span>';
        }
        $output .= '<span class="mypoints-value">' . esc_html($user_points) . '</span>';
        $output .= '</div>';
    
        return $output;
    }

    public function ld_extra_total_courses_owned_shortcode($atts = array()) {
        if (!is_user_logged_in()) {
            return ''; 
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'empty' => 'true'
            ),
            $atts
        );
    
        $user_id = get_current_user_id();
        $courses_owned = learndash_user_get_enrolled_courses($user_id, array(), true);
    
        if ($attributes['empty'] === 'false' && empty($courses_owned)) {
            return '';
        }
    
        $course_count = count($courses_owned);
    
        $output = '<div class="ld-extra-total-courses-owned-wrapper">';
        if (!empty($attributes['label'])) {
            $output .= '<span class="label">' . esc_html($attributes['label']) . '</span>';
        }
        $output .= '<span class="total-courses-owned-count">' . esc_html($course_count) . '</span>';
        $output .= '</div>';
    
        return $output;
    }
    
    public function ld_extra_completed_courses_count_shortcode($atts = array()) {
        if (!is_user_logged_in()) {
            return ''; // Csak bejelentkezett felhasználóknak
        }
    
        $attributes = shortcode_atts(
            array(
                'label' => '',
                'empty' => 'true'
            ),
            $atts
        );
    
        $user_id = get_current_user_id();
        $course_info = get_user_meta($user_id, '_sfwd-course_progress', true);
    
        $completed_courses_count = 0;
        if (!empty($course_info)) {
            foreach ($course_info as $course_id => $progress) {
                if (!empty($progress['completed']) && intval($progress['completed']) > 0) {
                    $completed_courses_count++;
                }
            }
        }
    
        if ($attributes['empty'] === 'false' && $completed_courses_count == 0) {
            return '';
        }
    
        $output = '<div class="ld-extra-completed-courses-count-wrapper">';
        if (!empty($attributes['label'])) {
            $output .= '<span class="label">' . esc_html($attributes['label']) . '</span>';
        }
        $output .= '<span class="completed-courses-count">' . esc_html($completed_courses_count) . '</span>';
        $output .= '</div>';
    
        return $output;
    }
    
    
    
    private function generate_output($attributes, $content, $type) {
       
        $allowed_html = array(
            'span' => array(
                'class' => array()
            ),
            'div' => array(
                'class' => array()
            ),
			 'i' => array(
                'class' => array()
            ),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'class' => array()
            )
        );
    
        $output = '<div class="ld-extra-' . esc_attr($type) . '-wrapper">';
        if (!empty($attributes['icon'])) {
            $output .= '<span class="my-ld-icon">' . wp_kses($attributes['icon'], $allowed_html) . '</span> ';
        }
        if (!empty($attributes['label'])) {
            $output .= '<span class="label">' . esc_html($attributes['label']) . ' </span>'; 
        }
        $output .= '<span class="' . esc_attr($type) . '-content">' . wp_kses($content, $allowed_html) . '</span>'; 
        $output .= '</div>';
    
        return $output;
    }
    

}
