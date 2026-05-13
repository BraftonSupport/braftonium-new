<?php
/**
 * Plugin Name: New Brafton Plugin
 * Description: Custom Plugin for blocks, custom posts, taxonomies, helper functions, widget areas, debug, inject scripts/stylesheets/JS/CSS & swop templates for specific users.
 * Version: 1.0
 * Developers: Jonathan Kowensky, Deryk King, James Allan, Fritz Bester
 * Website: https://www.brafton.com
 * Requires: ACF Pro(https://www.advancedcustomfields.com/) & NPM if using Sass(optional)
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

 //stop direct access
if ( ! defined( 'ABSPATH' ) )  exit;

// Optionally load ACF Pro if it is present (no longer a hard requirement).
$braftonium_acf_path = ABSPATH . 'wp-content/plugins/advanced-custom-fields-pro/acf.php';
if ( file_exists( $braftonium_acf_path ) ) {
    require_once $braftonium_acf_path;
}

require_once dirname(__FILE__).'/gutenberg-addon/class-loader.php';
add_action('enqueue_block_editor_assets', function() {
	wp_enqueue_script('braftonium-gutenberg-filters', plugin_dir_url(__FILE__) . '/gutenberg-addon/build/index.js', ['wp-edit-post']);
});

// Register all native Braftonium blocks (no ACF required).
include __DIR__ . '/blocks/blocks.php';

// Include useful functions (safe without ACF).
include __DIR__ . '/general-settings/useful-functions.php';

// Include patterns.
include __DIR__ . '/patterns/include-patterns.php';

// ACF-dependent settings only load when ACF is available.
if ( function_exists( 'acf_add_local_field_group' ) ) {
    include __DIR__ . '/general-settings/settings.php';
} else {
    add_action( 'admin_notices', function () {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo '<div class="notice notice-info"><p>'
            . esc_html__( 'Braftonium: ACF Pro is not installed. Native blocks are active; legacy ACF settings pages are disabled.', 'braftonium' )
            . '</p></div>';
    } );
}