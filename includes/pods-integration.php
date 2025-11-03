<?php
/**
 * Pods Integration for TW Settings
 *
 * This file creates a custom Pods shortcode that allows Pods templates to
 * retrieve display settings (like image sizes) from our central settings panel.
 *
 * @package TW_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers our custom Pods shortcode [tw_get_image_size].
 *
 * This function hooks into Pods and tells it about our new shortcode and the
 * PHP function that should run when it's used.
 */
function tw_settings_register_pods_shortcode() {
    // Check if the Pods shortcode function exists before trying to use it.
    if ( function_exists( 'pods_shortcode' ) ) {
        pods_shortcode(
            'tw_get_image_size',      // The name of our new shortcode.
            'tw_settings_pods_shortcode_handler' // The PHP function to call.
        );
    }
}
// We hook into 'pods_init' to make sure Pods is fully loaded before we register anything.
add_action( 'pods_init', 'tw_settings_register_pods_shortcode' );


/**
 * The handler function for our [tw_get_image_size] shortcode.
 *
 * This is the function that actually runs when Pods encounters the shortcode.
 * It retrieves the saved setting and outputs it as a simple string.
 *
 * @param array $atts The attributes passed to the shortcode, e.g., [tw_get_image_size context="play_poster_detail"].
 * @return string The saved image size (e.g., 'large', 'medium') or a default.
 */
function tw_settings_pods_shortcode_handler( $atts ) {
    
    // --- 1. Get the 'context' attribute from the shortcode ---
    // We use wp_parse_args to safely handle the attributes and set a default.
    $attributes = shortcode_atts(
        [
            'context' => '', // Default to an empty string if no context is provided.
        ],
        $atts
    );

    $context = $attributes['context'];

    // If no context was provided in the shortcode, we can't do anything.
    if ( empty( $context ) ) {
        return 'medium'; // Return a safe default.
    }

    // --- 2. Get all our saved settings ---
    // We will create the 'tw_display_settings' option on our settings page later.
    $all_settings = get_option( 'tw_display_settings', [] );


    // --- 3. Look up the specific setting for our context ---
    // For example, it will look for $all_settings['play_poster_detail_size'].
    $setting_key = $context . '_size'; // e.g., 'play_poster_detail_size'
    $saved_size = $all_settings[ $setting_key ] ?? 'medium'; // Safely get the value, or default to 'medium'.


    // --- 4. Define our list of allowed, safe image sizes ---
    $allowed_sizes = [ 'thumbnail', 'medium', 'large', 'full' ];

    
    // --- 5. Validate and return the result ---
    // If the saved value is a valid image size, return it. Otherwise, return the default.
    if ( in_array( $saved_size, $allowed_sizes, true ) ) {
        return $saved_size;
    } else {
        return 'medium'; // Final safety net.
    }
}
