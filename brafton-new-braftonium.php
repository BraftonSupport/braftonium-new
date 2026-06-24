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
	$build_path  = __DIR__ . '/gutenberg-addon/build/index.js';
	$asset_path  = __DIR__ . '/gutenberg-addon/build/index.asset.php';

	if ( ! file_exists( $build_path ) ) {
		return;
	}

	// Dependencies the bundle accesses as WordPress globals (wp.*). These
	// are not detected by wp-scripts because the source reads them off the
	// global `wp` object rather than importing them.
	$wp_deps = array(
		'wp-hooks',
		'wp-element',
		'wp-blocks',
		'wp-block-editor',
		'wp-components',
		'wp-compose',
		'wp-data',
		'wp-i18n',
		'wp-api-fetch',
	);

	$asset   = file_exists( $asset_path ) ? require $asset_path : array( 'dependencies' => array(), 'version' => '1.0' );
	$deps    = array_values( array_unique( array_merge( $wp_deps, (array) ( $asset['dependencies'] ?? array() ) ) ) );
	$version = $asset['version'] ?? '1.0';

	wp_enqueue_script(
		'braftonium-gutenberg-filters',
		plugin_dir_url(__FILE__) . 'gutenberg-addon/build/index.js',
		$deps,
		$version,
		true
	);
});

// Feature flags (must load before the gated includes below).
include __DIR__ . '/general-settings/features.php';

// Register all native Braftonium blocks (no ACF required).
if ( braftonium_feature_enabled( 'blocks' ) ) {
    include __DIR__ . '/blocks/blocks.php';
}

// Include useful functions (safe without ACF).
include __DIR__ . '/general-settings/useful-functions.php';

// Include patterns.
if ( braftonium_feature_enabled( 'patterns' ) ) {
    include __DIR__ . '/patterns/include-patterns.php';
}

// Native settings pages (no ACF requirement). Always loaded — this is where the
// feature toggles themselves live.
include __DIR__ . '/general-settings/settings.php';