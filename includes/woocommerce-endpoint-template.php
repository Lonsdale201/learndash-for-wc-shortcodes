<?php
// woocommerce-endpoint-template.php
if (!defined('ABSPATH')) {
    exit;
}



$user_id = get_current_user_id();
$options = get_option('learndash_extras_settings');
$course_list_type = $options['course_list_type'] ?? 'simple'; // Alapértelmezett érték: 'simple'
$items_per_page = $options['items_per_page'] ?? 6; // Alapértelmezett érték: 6


$menu_label = $options['woocommerce_course_menu_label'] ?? 'Kurzusaim';

echo '<h3 class="course-title">' . esc_html($menu_label) . '</h3>';

do_action('ld_course_list_after_menu_label', $menu_label, $user_id);

if (isset($options['woocommerce_learndash_statistics']) && $options['woocommerce_learndash_statistics'] == '1') {
    $courses_owned = learndash_user_get_enrolled_courses($user_id, array(), true);
    $course_info = get_user_meta($user_id, '_sfwd-course_progress', true);
    $user_points = learndash_get_user_course_points($user_id);

    $completed_courses_count = 0;
    if (!empty($course_info)) {
        foreach ($course_info as $course_id => $progress) {
            if (!empty($progress['completed']) && intval($progress['completed']) > 0) {
                $completed_courses_count++;
            }
        }
    }

    echo '<div class="ld-stats-container">';
    echo '<div class="ld-stat-box"><span class="ld-stat-label">Kurzusaim</span><span class="ld-stat-value">' . count($courses_owned) . '</span></div>';
    echo '<div class="ld-stat-box"><span class="ld-stat-label">Befejezett kurzusok</span><span class="ld-stat-value">' . $completed_courses_count . '</span></div>';
    echo '<div class="ld-stat-box"><span class="ld-stat-label">Pontok</span><span class="ld-stat-value">' . $user_points . '</span></div>';
    echo '</div>';
}

if ($course_list_type == 'advanced') {
    // Advanced kurzuslista megjelenítése
    echo '<div class="ld-advanced-course-list-wrapper">';
    echo do_shortcode('[ld_profile course_points_user="no" expand_all="no" profile_link="no" show_search="no" show_header="no" per_page="' . esc_attr($items_per_page) . '" show_quizzes="no"]');
    echo '</div>';
} else {
    // Simple kurzuslista megjelenítése
    $coursegrid_compatibility = isset($options['coursegrid_compatibility']) && $options['coursegrid_compatibility'] == '1';
    $show_thumbnail = isset($options['enable_woocommerce_course_featured_image']) && $options['enable_woocommerce_course_featured_image'] == '1' ? 'true' : 'false';
    $columns_per_row = isset($options['columns_per_row']) ? $options['columns_per_row'] : 3;

    $wrapper_class = 'ld-simple-course-list-wrapper';
    $wrapper_class .= $coursegrid_compatibility ? ' ld-course-grid-enabled' : ' ld-course-grid-disabled';

    $shortcode_params = $coursegrid_compatibility 
        ? 'course_grid="true" num="' . esc_attr($items_per_page) . '" col="' . esc_attr($columns_per_row) . '" progress_bar="true" show_thumbnail="' . $show_thumbnail . '"'
        : 'course_grid="false" num="' . esc_attr($items_per_page) . '"';

    echo '<div class="' . esc_attr($wrapper_class) . '">';
    echo do_shortcode('[ld_course_list mycourses="true" user_id="' . $user_id . '" ' . $shortcode_params . ']');
    echo '</div>';
}

// Action hook hozzáadása
do_action('ld_course_list_after', $user_id);
