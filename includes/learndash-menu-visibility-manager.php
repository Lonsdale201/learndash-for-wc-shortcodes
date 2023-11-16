<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LearnDash_Menu_Visibility_Manager {
    private $options;

    public function __construct() {
        $this->options = get_option('learndash_extras_settings');
        add_filter('wp_get_nav_menu_items', array($this, 'filter_menu_items'), 10, 3);
    }

    public function filter_menu_items($items, $menu, $args) {
        if (is_admin()) {
            return $items;
        }

        if (!$this->is_feature_enabled('enable_menu_visibility')) {
            return $items;
        }

        $user_id = get_current_user_id();
        $user_courses = learndash_user_get_enrolled_courses($user_id);
        $user_groups = learndash_get_users_group_ids($user_id);

        foreach ($items as $key => $item) {
            if ($this->should_hide_menu_item($item, $user_courses, $user_groups)) {
                unset($items[$key]);
            }
        }

        return $items;
    }

    private function is_feature_enabled($feature) {
        return isset($this->options[$feature]) && $this->options[$feature] == '1';
    }

    private function should_hide_menu_item($item, $user_courses, $user_groups) {
        $classes_str = implode(' ', $item->classes);

        if (strpos($classes_str, 'learndash--menu-anyenrolled') !== false && empty($user_courses)) {
            return true;
        }
        if (strpos($classes_str, 'learndash--menu-anygroup_enrolled') !== false && empty($user_groups)) {
            return true;
        }

        return false;
    }
}
