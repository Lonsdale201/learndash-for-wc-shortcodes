<?php
// includes/learndash-auto-enroll.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LearnDash_Auto_Enroll_System {
    public function __construct() {
        add_action('user_register', array($this, 'auto_enroll_user_to_course'), 10, 1);
    }

    public function auto_enroll_user_to_course($user_id) {
        $options = get_option('learndash_extras_settings');
        $course_ids = $options['learndash_courses_select'] ?? [];

        if (!is_array($course_ids) || empty($course_ids)) {
            return; 
        }

        foreach ($course_ids as $course_id) {
            ld_update_course_access($user_id, $course_id, false);
        }
    }
}
