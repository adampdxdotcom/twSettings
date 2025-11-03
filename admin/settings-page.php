<?php
/**
 * Admin Settings Page for TW Settings
 *
 * This file creates the "TW Settings" admin menu and renders the settings page
 * where users can control global display options for the other TW plugins.
 *
 * @package TW_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * 1. Initializes all hooks for the settings page.
 */
function tw_settings_page_init() {
    // Hook to create the admin menu.
    add_action( 'admin_menu', 'tw_settings_add_menu_page' );
    // Hook to register our settings so they can be saved.
    add_action( 'admin_init', 'tw_settings_register_settings' );
}
add_action( 'init', 'tw_settings_page_init' );


/**
 * 2. Creates the top-level "TW Settings" admin menu item.
 */
function tw_settings_add_menu_page() {
    add_menu_page(
        'Theatre West Global Settings', // Page title
        'TW Settings',                  // Menu title
        'manage_options',               // Capability required
        'tw-settings',                  // Menu slug
        'tw_settings_render_page',      // Function to render the page content
        'dashicons-admin-generic',      // A nice gear icon
        85                              // Position in the menu (low, near the bottom)
    );
}


/**
 * 3. Registers the settings, sections, and fields with the WordPress Settings API.
 */
function tw_settings_register_settings() {
    // Register the setting group. This is what our form will save.
    // The option 'tw_display_settings' is what our Pods shortcode will read.
    register_setting(
        'tw_settings_group',       // A unique name for the settings group.
        'tw_display_settings',     // The name of the option to save in the database.
        'tw_settings_sanitize'     // The sanitization callback function.
    );

    // Add a section to the settings page.
    add_settings_section(
        'tw_settings_template_section', // Unique ID for the section.
        'Template Display Settings',    // Title of the section.
        'tw_settings_template_section_callback', // Function to render the section's intro text.
        'tw-settings'                   // The page slug where this section should appear.
    );

    // Add our first field: the image size dropdown.
    add_settings_field(
        'play_poster_detail_size',      // Unique ID for the field.
        'Poster Size (Play Detail Page)', // Label for the field.
        'tw_settings_field_image_size_callback', // Function to render the field's HTML.
        'tw-settings',                  // The page slug.
        'tw_settings_template_section'  // The section this field belongs to.
    );
}


/**
 * 4. Renders the main settings page wrapper and form.
 */
function tw_settings_render_page() {
    // Security check.
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting group.
            settings_fields( 'tw_settings_group' );
            // Output the sections and fields for our page.
            do_settings_sections( 'tw-settings' );
            // Output the save button.
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}


/**
 * 5. Callback functions to render the HTML for our sections and fields.
 */

// Renders the introductory text for the "Template Display Settings" section.
function tw_settings_template_section_callback() {
    echo '<p>Control the default display settings for various templates and shortcodes across the TW plugin suite.</p>';
}

// Renders the HTML for our image size dropdown field.
function tw_settings_field_image_size_callback() {
    // Get the full array of saved settings.
    $options = get_option( 'tw_display_settings', [] );
    // Get the specific value for our field, defaulting to 'large'.
    $current_value = $options['play_poster_detail_size'] ?? 'large';
    ?>
    <select name="tw_display_settings[play_poster_detail_size]" id="play_poster_detail_size">
        <option value="thumbnail" <?php selected( $current_value, 'thumbnail' ); ?>>Thumbnail</option>
        <option value="medium" <?php selected( $current_value, 'medium' ); ?>>Medium</option>
        <option value="large" <?php selected( $current_value, 'large' ); ?>>Large</option>
        <option value="full" <?php selected( $current_value, 'full' ); ?>>Full (Original)</option>
    </select>
    <p class="description">
        Select the image size for posters on single Play detail pages.
    </p>
    <?php
}


/**
 * 6. Sanitization function to make sure our saved data is clean and secure.
 */
function tw_settings_sanitize( $input ) {
    // Start with the existing saved options, so we don't erase other settings in the future.
    $existing_options = get_option( 'tw_display_settings', [] );
    $sanitized_output = $existing_options;

    $allowed_sizes = [ 'thumbnail', 'medium', 'large', 'full' ];

    // Sanitize the play poster detail size.
    if ( ! empty( $input['play_poster_detail_size'] ) ) {
        $submitted_size = $input['play_poster_detail_size'];
        if ( in_array( $submitted_size, $allowed_sizes, true ) ) {
            // Correctly save it to the array.
            $sanitized_output['play_poster_detail_size'] = $submitted_size;
        }
    }

    // Add sanitization for future settings here...

    return $sanitized_output;
}
