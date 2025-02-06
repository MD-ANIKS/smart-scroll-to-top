<?php
/*
 * Plugin Name:       Smart Scroll To Top
 * Plugin URI:        https://wordpress.org/plugins/smart-scroll-to-top/
 * Description:       Smart Scroll To Top plugin allows the visitor to easily scroll back to the top of the page, with fully customizable options and image.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            anikwpstudio
 * Author URI:        https://github.com/MD-ANIKS
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       smart-scroll-to-top
 */

// Load plugin JS and CSS
function wpsstt_enqueue_script()
{
    // Assuming the file is placed in /assets/css/all.min.css
    wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . 'assets/css/all.min.css', array(), '6.0.0-beta3.min');

    // Enqueue built-in jQuery in WordPress
    wp_enqueue_script('jquery');

    // Enqueue Smart Scroll To Top JS
    wp_enqueue_script('wpsstt_plugin_script', plugins_url('assets/js/jquery.smart-scroll-to-top.min.js', __FILE__), array('jquery'), '1.0.0', true);
};
add_action('wp_enqueue_scripts', 'wpsstt_enqueue_script');


// plugin customization setting 
function wpsstt_scroll_to_top($wp_customize)
{

    $wp_customize->add_section('wpsstt_scroll_to_top_section', array(
        'title' => __('Smart Scroll To Top', 'smart-scroll-to-top'),
        'description' => 'Add a customizable "Scroll to Top" button to your WordPress site for improved navigation and user experience.'
    ));

    // Setting for custom icon file upload
    $wp_customize->add_setting('wpsstt_custom_icon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'wpsstt_custom_icon', array(
        'label' => __('Upload Custom Icon (PNG)', 'smart-scroll-to-top'),
        'description' => __("Upload your own custom icon for the Scroll to Top button. You can use only PNG file to match your site's theme and branding. If you don't upload a custom icon, the default Font Awesome icon will be used.", 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'settings' => 'wpsstt_custom_icon',
    )));

    // Searchable Icon Picker (Font Awesome)
    $wp_customize->add_setting('wpsstt_scroll_to_top_search_icon', array(
        'default' => 'fa-angle-up',
        'transport' => 'refresh',
    ));

    // Only display the searchable icon picker if the user has not uploaded a custom icon
    $wp_customize->add_control('wpsstt_scroll_to_top_search_icon', array(
        'label'   => __('Select Searchable Icon (Font Awesome)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'select',
        'description' => 'Choose an icon from Font Awesome for the Scroll to Top button. This option allows you to pick a pre-defined icon, such as an arrow or chevron, directly from a list. If you upload a custom icon, this option will be hidden.',
        'choices' => array(
            'fa-angle-up'    => __('Angle Up', 'smart-scroll-to-top'),
            'fa-arrow-up'     => __('Arrow Up', 'smart-scroll-to-top'),
            'fa-chevron-up'   => __('Chevron Up', 'smart-scroll-to-top'),
            'fa-caret-up'      => __('Caret Up', 'smart-scroll-to-top'),
            'fa-level-up' => __('Arrow Level Up', 'smart-scroll-to-top'),
            // Add more Font Awesome icons as needed
        ),
        'active_callback' => function () {
            // Only show this control if no custom SVG icon is uploaded
            return !get_theme_mod('wpsstt_custom_icon');
        },
    ));

    // Font Size Setting
    $wp_customize->add_setting('wpsstt_scroll_to_top_font_size', array(
        'default'           => '18px', // Default font size
        'sanitize_callback' => 'sanitize_text_field', // Sanitize input
        // 'transport'         => 'refresh', // Refresh the page on change
    ));

    // Font Size Control
    $wp_customize->add_control('wpsstt_scroll_to_top_font_size', array(
        'label'       => __('Button Font Size', 'smart-scroll-to-top'),
        'description' => __('Set the font size for the Scroll to Top button (e.g., 14px, 1em, 100%).', 'smart-scroll-to-top'),
        'section'     => 'wpsstt_scroll_to_top_section',
        'type'        => 'text', // Text input for font size
        'input_attrs' => array(
            'placeholder' => 'e.g., 14px, 1em, 100%',
        ),
    ));

    // Scroll to Top Button Background Color
    $wp_customize->add_setting('wpsstt_scroll_to_top_bg', array(
        'default' => '#262626',
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_bg',  array(
        'label' => __('Scroll to Top Button Background Color', 'smart-scroll-to-top'),
        'description' => "Change the background color of the Scroll to Top button. Choose any color that complements your website's design to make the button stand out or blend in with the overall theme.",
        'section' => 'wpsstt_scroll_to_top_section',
        'type' => 'color'
    ));

    // Scroll to Top Button Icon Color
    $wp_customize->add_setting('wpsstt_scroll_to_top_color', array(
        'default' => '#fff',
    ));
    $wp_customize->add_control('wpsstt_scroll_to_top_color', array(
        'label' => __('Scroll to Top Button Icon Color',  'smart-scroll-to-top'),
        'description' => "Set the color of the icon on the Scroll to Top button. You can customize the icon color to match your button's background color or make it more visible with a contrasting shade. If you upload a custom icon, this option will be hidden.",
        'section' => 'wpsstt_scroll_to_top_section',
        'type' => 'color',
        'active_callback' => function () {
            // Only show this control if no custom SVG icon is uploaded
            return !get_theme_mod('wpsstt_custom_icon');
        },
    ));

    // Add Border Radius Type Option
    $wp_customize->add_setting('wpsstt_scroll_to_top_radius_type', array(
        'default' => 'square', // Default is "square"
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_radius_type', array(
        'label' => __('Scroll to Top Button Radius', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'settings' => 'wpsstt_scroll_to_top_radius_type',
        'type' => 'radio',
        'choices' => array(
            'rounded' => __('Rounded', 'smart-scroll-to-top'),
            'square' => __('Square', 'smart-scroll-to-top'),
            'custom' => __('Custom Radius', 'smart-scroll-to-top'),
        ),
    ));

    // Add Custom Border Radius Input (Only shown if "Custom Radius" is selected)
    $wp_customize->add_setting('wpsstt_scroll_to_top_custom_radius', array(
        'default' => '',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_custom_radius', array(
        'label' => __('Custom Border Radius (e.g., 10px, 50%)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type' => 'text',
        'description' => __('If you select the "Custom" option for border radius, enter a custom value here. You can set the radius in pixels (e.g., 10px) or percentage (e.g., 50%) to achieve a specific roundness or shape for the button.', 'smart-scroll-to-top'),
        'input_attrs' => array(
            'placeholder' => 'e.g., 10px, 50%',
        ),
        'active_callback' => function () {
            // Only show this control if no custom SVG icon is uploaded
            return get_theme_mod('wpsstt_scroll_to_top_radius_type') == 'custom';
        },
    ));

    // Add setting for windowScrollShow (scroll position threshold to show the button)
    $wp_customize->add_setting('wpsstt_scroll_to_top_show_position', array(
        'default' => 600, // Default value is 600
        'sanitize_callback' => 'absint', // Ensure it's an integer
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_show_position', array(
        'label'   => __('Scroll Position to Show Button (px)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'number',
        'description' => __('Control when the Scroll to Top button appears by setting a scroll threshold. Enter the height (in pixels) at which the button will become visible. For example, setting this to 600 means the button will appear once the user has scrolled 600px down the page.', 'smart-scroll-to-top'),
        'input_attrs' => array(
            'min' => 0, // Ensure a positive value
            'step' => 10, // Step in increments of 10px
            'placeholder' => __('e.g., 400, 600, etc.', 'smart-scroll-to-top')
        ),
    ));


    // Add setting for scroll animation speed
    $wp_customize->add_setting('wpsstt_scroll_to_top_speed', array(
        'default' => 300, // Default value is 800
        'sanitize_callback' => 'absint', // Ensure it's an integer
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_speed', array(
        'label'   => __('Scroll Animation Speed (ms)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'number',
        'description' => __('Customize the speed at which the page scrolls back to the top when the button is clicked. The value is in milliseconds. The default value is 300ms, but you can make it faster or slower according to your preference.', 'smart-scroll-to-top'),
        'input_attrs' => array(
            'min' => 100, // Minimum value (for example)
            'step' => 100, // Step increment for the value
            'placeholder' => __('e.g., 300, 600, etc.', 'smart-scroll-to-top')
        ),
    ));

    // Add Setting for Width 
    $wp_customize->add_setting('wpsstt_scroll_to_top_width', array(
        'default' => '45px', // Default Width,
        'sanitize_callback' => 'sanitize_text_field'
    ));

    // Add Control for Width
    $wp_customize->add_control('wpsstt_scroll_to_top_width', array(
        'label' => __('Button Width (px)', 'smart-scroll-to-top'),
        'description' => 'Set the width of the "Scroll to Top" button. This value controls how wide the button will appear on the screen. You can specify it in pixels (e.g., 45px) or any other valid CSS unit. Default is 45px.',
        'setting' => 'wpsstt_scroll_to_top_width',
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'text', // You can set it as a text box where users can input values like '50px', '60px', etc.
        'input_attrs' => array(
            'placeholder' => __('e.g., 45px, 60%, etc.', 'smart-scroll-to-top'),
        )
    ));

    // Add Setting for Height
    $wp_customize->add_setting('wpsstt_scroll_to_top_height', array(
        'default' => '45px', // default height
        'sanitize_callback' => 'sanitize_text_field'
    ));

    // Add Control for Height
    $wp_customize->add_control('wpsstt_scroll_to_top_height', array(
        'label' => __('Button Height (px)', 'smart-scroll-to-top'),
        'description' => 'Define the height of the "Scroll to Top" button. The height determines how tall the button will appear on the screen. Specify it in pixels (e.g., 45px) or another CSS unit. Default is 45px.',
        'setting' => 'wpsstt_scroll_to_top_height',
        'section' => 'wpsstt_scroll_to_top_section',
        'type' => 'text',
        'input_attrs' => array(
            'placeholder' => __('e.g., 45px, 60%, etc.', 'smart-scroll-to-top'),
        )
    ));


    // Add setting and control for bottom position
    $wp_customize->add_setting('wpsstt_scroll_to_top_bottom', array(
        'default' => '25px', // Default bottom position
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_bottom', array(
        'label'   => __('Bottom Position (px)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'text',
        'description' => __('Set the bottom position of the Scroll to Top button in pixels. Default is 25px.', 'smart-scroll-to-top'),
        'input_attrs' => array(
            'placeholder' => 'e.g., 25px',
        ),
    ));

    // Add setting and control for right position
    $wp_customize->add_setting('wpsstt_scroll_to_top_right', array(
        'default' => '25px', // Default right position
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('wpsstt_scroll_to_top_right', array(
        'label'   => __('Right Position (px)', 'smart-scroll-to-top'),
        'section' => 'wpsstt_scroll_to_top_section',
        'type'    => 'text',
        'description' => __('Set the right position of the Scroll to Top button in pixels. Default is 25px.', 'smart-scroll-to-top'),
        'input_attrs' => array(
            'placeholder' => 'e.g., 25px',
        ),
    ));



    // Show Scroll to Top Button Only on Mobile Devices
    $wp_customize->add_setting('wpsstt_scroll_to_top_button', array(
        'default'     => false,
        'transport'   => 'refresh',
    ));
    $wp_customize->add_control('wpsstt_scroll_to_top_button', array(
        'label' => __('Show Scroll to Top Button Only on Mobile Devices', 'smart-scroll-to-top'),
        'description' => 'Choose whether to display the "Scroll to Top" button only on mobile devices. If enabled, the button will only be visible on smaller screen sizes (e.g., smartphones and tablets). If disabled, the button will appear on all devices, including desktops and laptops. This setting allows you to optimize your site’s design for different screen sizes and enhance the user experience on mobile.',
        'section' => 'wpsstt_scroll_to_top_section',
        'type' => 'radio',
        'choices' => array(
            'true' => __('Yes, show only on mobile devices', 'smart-scroll-to-top'),
            'false' => __('No, show on all devices', 'smart-scroll-to-top'),
        ),
    ));
};
add_action('customize_register', 'wpsstt_scroll_to_top');



// theme css customization 
function wpsstt_theme_css_cust()
{
?>
    <style type="text/css">
        #wpsstt_gotop {
            background-color: <?php echo esc_attr(get_theme_mod('wpsstt_scroll_to_top_bg', '#262626')); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('wpsstt_scroll_to_top_color', '#fff')); ?> !important;
            font-size: <?php echo esc_html(get_theme_mod('wpsstt_scroll_to_top_font_size', '14px')); ?>;
        }

        #wpsstt_gotop img {
            width: <?php echo esc_html(get_theme_mod('wpsstt_scroll_to_top_font_size', '14px')); ?>;
        }
    </style>
<?php
};
add_action('wp_head', 'wpsstt_theme_css_cust');

// theme js customization 
function wpsstt_theme_js_cust()
{
?>
    <script>
        // Customizer settings for Custom Icon and Font Awesome icon
        var wpssttIconUrl = "<?php echo esc_url(get_theme_mod('wpsstt_custom_icon', '')); ?>";
        var wpssttSearchIcon = "<?php echo esc_attr(get_theme_mod('wpsstt_scroll_to_top_search_icon', 'fa-angle-up')); ?>";

        var wpssttRadiusType = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_radius_type', 'rounded')); ?>;
        var wpssttCustomRadius = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_custom_radius', '')); ?>;

        // Get the dynamic scroll position threshold from the Customizer
        var wpssttWindowScrollShow = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_show_position', 600)); ?>;
        var wpssttScrollSpeed = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_speed', 300)); ?>;

        // Dynamic Width and Height for the Button
        var wpssttButtonWidth = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_width', '45px')); ?>;
        var wpssttButtonHeight = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_height', '45px')); ?>;

        // Dynamic Bottom and Right positions for the button
        var wpssttBottomPosition = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_bottom', '25px')); ?>;
        var wpssttRightPosition = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_right', '25px')); ?>;

        var wpssttMobileOnly = <?php echo wp_json_encode(get_theme_mod('wpsstt_scroll_to_top_button', 'false') === 'true'); ?>;
    </script>
<?php
};
add_action('wp_footer', 'wpsstt_theme_js_cust');

?>