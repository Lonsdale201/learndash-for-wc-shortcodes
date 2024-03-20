<?php
// learndash-for-settings.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class LearnDash_for_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'initialize_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'woocommerce', 
            'LearnDash Extras', 
            'LearnDash Extras', 
            'manage_options', 
            'learndash-extras',
            array($this, 'render_settings_page') 
        );
    }

    public function initialize_settings() {
        register_setting('learndash_extras_settings', 'learndash_extras_settings');

        add_settings_section(
            'learndash_extras_main_section',
            'Main Settings',
            null, 
            'learndash-extras' 
        );

        add_settings_field(
            'learndash_courses_select',
            'Select LearnDash Courses',
            array($this, 'render_select2_course_field'),
            'learndash-extras',
            'learndash_extras_main_section',
            array(
                'label_for' => 'learndash_courses_select',
                'name' => 'learndash_courses_select',
            )
        );

        add_settings_field(
            'enable_elementor_visibility',
            'Enable Elementor Visibility',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_main_section',
            array(
                'label_for' => 'enable_elementor_visibility',
                'name' => 'enable_elementor_visibility'
            )
        );

        add_settings_field(
            'enable_menu_visibility',
            'Enable Menu Visibility',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_main_section',
            array(
                'label_for' => 'enable_menu_visibility',
                'name' => 'enable_menu_visibility'
            )
        );

        
        add_settings_section(
            'learndash_extras_woocommerce_section',
            'WooCommerce fiókom',
            array($this, 'render_woocommerce_section_description'),
            'learndash-extras'
        );

       
        add_settings_field(
            'enable_woocommerce_course_menu',
            'Enable WooCommerce Course menu',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'enable_woocommerce_course_menu',
                'name' => 'enable_woocommerce_course_menu'
            )
        );

      
        add_settings_field(
            'woocommerce_course_menu_label',
            'Menu label',
            array($this, 'render_text_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'woocommerce_course_menu_label',
                'name' => 'woocommerce_course_menu_label'
            )
        );

        add_settings_field(
            'woocommerce_learndash_statistics',
            'Show user Learndash statistics',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'woocommerce_learndash_statistics',
                'name' => 'woocommerce_learndash_statistics'
            )
        );

       
        add_settings_field(
            'woocommerce_course_menu_visibility',
            'Menu Visibility',
            array($this, 'render_radio_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'name' => 'woocommerce_course_menu_visibility',
                'options' => array(
                    'anybody' => 'Anybody can see the Course menu',
                    'purchased' => 'Only available if user have any purchased course'
                )
            )
        );

        add_settings_field(
            'course_list_type',
            'Course List Type',
            array($this, 'render_radio_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'name' => 'course_list_type',
                'options' => array(
                    'simple' => 'Show Simple course list',
                    'advanced' => 'Show advanced course list'
                )
            )
        );

        add_settings_field(
            'course_list_type',
            'Course List Type',
            array($this, 'render_radio_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'name' => 'course_list_type',
                'options' => array(
                    'simple' => 'Show Simple course list',
                    'advanced' => 'Show advanced course list'
                )
            )
        );

        add_settings_field(
            'items_per_page',
            'Number of items per page ',
            array($this, 'render_number_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'items_per_page',
                'name' => 'items_per_page'
            )
        );

        add_settings_field(
            'coursegrid_compatibility',
            'CourseGrid Compatibility',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'coursegrid_compatibility',
                'name' => 'coursegrid_compatibility'
            )
        );

        add_settings_field(
            'columns_per_row',
            'Column numbers',
            array($this, 'render_number_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'columns_per_row',
                'name' => 'columns_per_row'
            )
        );

        add_settings_field(
            'enable_woocommerce_course_featured_image',
            'Show featured image',
            array($this, 'render_checkbox_field'),
            'learndash-extras',
            'learndash_extras_woocommerce_section',
            array(
                'label_for' => 'enable_woocommerce_course_featured_image',
                'name' => 'enable_woocommerce_course_featured_image'
            )
        );

        
        add_settings_section(
            'learndash_extras_thankyou_section',
            'WooCommerce Thank You Page',
            array($this, 'render_thankyou_section_description'),
            'learndash-extras'
        );

        add_settings_field(
            'learndash_thankyou_message',
            'Custom Thank You Message',
            array($this, 'render_textarea_field'),
            'learndash-extras',
            'learndash_extras_thankyou_section',
            array(
                'label_for' => 'learndash_thankyou_message',
                'name' => 'learndash_thankyou_message'
            )
        );

    }

     public function render_woocommerce_section_description() {
        echo '<p>Here you can configure your course menu item created on my WooCommerce account page.</p>';
    }

    public function render_thankyou_section_description() {
        echo '<p>If a product linked to a course is purchased, you can display a custom message on the WooCommerce Thank You page, such as instructions.</p>';
    }
    
    public function render_textarea_field($args) {
        $options = get_option('learndash_extras_settings');
        $value = $options[$args['name']] ?? '';
        echo '<textarea id="' . $args['label_for'] . '" name="learndash_extras_settings[' . $args['name'] . ']" rows="4" cols="50">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">Use the <code>{fiokom}</code> smartlink to provide a link to the course menu item you have created on my woocomerce account page (if you have enabled it)</p>';
    }
    
    public function render_select2_course_field($args) {
        wp_enqueue_script('wc-enhanced-select');
        wp_enqueue_style('woocommerce_admin_styles');
    
        $options = get_option('learndash_extras_settings');
        $selected_courses = $options[$args['name']] ?? [];
        $courses = get_posts([
            'post_type' => 'sfwd-courses', 
            'numberposts' => -1,
            'post_status' => 'publish' 
        ]);
        
        echo '<p>Select the courses where the user is automatically enrolled after registration.</p>';
        
        echo '<select class="wc-enhanced-select" id="' . $args['label_for'] . '" name="learndash_extras_settings[' . $args['name'] . '][]" multiple="multiple" data-placeholder="Choose Courses" style="width:50%;">';
        foreach ($courses as $course) {
            $selected = in_array($course->ID, $selected_courses) ? 'selected' : '';
            echo '<option value="' . $course->ID . '" ' . $selected . '>' . $course->post_title . '</option>';
        }
        echo '</select>';
    
        echo '<script>
            jQuery(document).ready(function($) { 
                $("#' . $args['label_for'] . '").select2({
                    placeholder: "Choose Courses",
                    allowClear: true
                }); 
            });
        </script>';
    }
    
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>LearnDash Extras Settings</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('learndash_extras_settings');
                do_settings_sections('learndash-extras');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    
     public function render_text_field($args) {
        $options = get_option('learndash_extras_settings');
        $value = $options[$args['name']] ?? '';
        echo '<input type="text" id="' . $args['label_for'] . '" name="learndash_extras_settings[' . $args['name'] . ']" value="' . esc_attr($value) . '">';
    }

    public function render_number_field($args) {
        $options = get_option('learndash_extras_settings');
        $default_value = $args['name'] === 'items_per_page' ? 6 : ($args['name'] === 'columns_per_row' ? 3 : '');
        $value = isset($options[$args['name']]) ? $options[$args['name']] : $default_value;
        echo '<input type="number" id="' . $args['label_for'] . '" name="learndash_extras_settings[' . $args['name'] . ']" value="' . esc_attr($value) . '">';
    }

    public function render_checkbox_field($args) {
        $options = get_option('learndash_extras_settings');
        $checked = isset($options[$args['name']]) ? checked($options[$args['name']], 1, false) : '';
        echo '<input type="checkbox" id="' . $args['label_for'] . '" name="learndash_extras_settings[' . $args['name'] . ']" value="1"' . $checked . '>';
    }

    public function render_radio_field($args) {
        $options = get_option('learndash_extras_settings');
        $selected_option = $options[$args['name']] ?? '';
    
        foreach ($args['options'] as $value => $label) {
            $checked = checked($selected_option, $value, false);
            echo '<label><input type="radio" name="learndash_extras_settings[' . $args['name'] . ']" value="' . esc_attr($value) . '"' . $checked . '> ' . esc_html($label) . '</label><br>';
        }
    }
    

    
    
}

