<?php
// includes/learndash-visibility-manager.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class LearnDash_Visibility_Manager {
    private $options;

    public function __construct() {
        $this->options = get_option('learndash_extras_settings');
        if ($this->is_feature_enabled('enable_elementor_visibility')) {
            add_filter('elementor/frontend/section/should_render', array($this, 'check_cond'), 10, 2);
            add_filter('elementor/frontend/column/should_render', array($this, 'check_cond'), 10, 2);
            add_filter('elementor/frontend/widget/should_render', array($this, 'check_cond'), 10, 2);
        }
    }

    private function is_feature_enabled($feature) {
        return isset($this->options[$feature]) && $this->options[$feature] == '1';
    }

    public function check_cond($should_render, $element) {
        if (is_admin() || (function_exists('Elementor\Plugin') && Elementor\Plugin::$instance->editor->is_edit_mode())) {
            return $should_render;
        }

        if (is_singular('sfwd-courses')) {
            $course_id = get_the_ID();
            $user_id = get_current_user_id();
            $settings = $element->get_settings_for_display();

            if ($this->should_hide_widget($settings, $course_id, $user_id)) {
                return false; // Ne rendereljük a widgetet
            }
        }
        return $should_render; 
    }

    private function should_hide_widget($settings, $course_id, $user_id) {
        if (isset($settings['_css_classes'])) {
            if (strpos($settings['_css_classes'], 'learndash--enrolled') !== false && !sfwd_lms_has_access($course_id, $user_id)) {
                return true;
            }
            if (strpos($settings['_css_classes'], 'learndash--logged_in') !== false && empty($user_id)) {
                return true;
            }
            if (strpos($settings['_css_classes'], 'learndash--logged_out') !== false && !empty($user_id)) {
                return true;
            }
        }
        return false;
    }
}
