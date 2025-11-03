<?php
/**
 * Plugin Name:       TW Settings
 * Plugin URI:        
 * Description:       Provides a central settings panel for all Theatre West custom plugins.
 * Version:           1.0.0
 * Author:            Adam Michaels
 * Author URI:        
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tw-settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define a constant for the plugin path.
 * This makes including files cleaner and more reliable.
 */
define( 'TW_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );


// =========================================================================
// == Load Plugin Components
// =========================================================================

// 1. Load the Admin Settings Page UI.
// This file is responsible for creating the "TW Settings" menu and page.
require_once TW_SETTINGS_PATH . 'admin/settings-page.php';

// 2. Load the Pods Integration.
// This file provides the custom [tw_get_image_size] shortcode for Pods templates.
require_once TW_SETTINGS_PATH . 'includes/pods-integration.php';
