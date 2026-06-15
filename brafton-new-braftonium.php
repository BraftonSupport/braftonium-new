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

// get acf, see if plugin exists
require_once ABSPATH . 'wp-content/plugins/advanced-custom-fields-pro/acf.php';
require_once dirname(__FILE__).'/gutenberg-addon/class-loader.php';
add_action('enqueue_block_editor_assets', function() {
	$script_path  = plugin_dir_path(__FILE__) . 'gutenberg-addon/build/index.js';
	$asset_path   = plugin_dir_path(__FILE__) . 'gutenberg-addon/build/index.asset.php';
	// Use the build's generated dependencies (react, react-dom, react-jsx-runtime, …)
	// plus wp-edit-post so the wp.* editor globals the script reads are loaded first.
	$asset        = file_exists($asset_path) ? include $asset_path : array('dependencies' => array(), 'version' => false);
	$dependencies = array_unique(array_merge($asset['dependencies'], array('wp-edit-post')));
	$version      = $asset['version'] ? $asset['version'] : (file_exists($script_path) ? filemtime($script_path) : false);
	wp_enqueue_script('braftonium-gutenberg-filters', plugin_dir_url(__FILE__) . 'gutenberg-addon/build/index.js', $dependencies, $version, true);
});
// make acf options
if(!function_exists("acf_add_local_field_group")){
	_e( "Hey, do you have the ACF plugin? You don\'t need to activate it but it\'ll be nice if it was there.", "braftonium" );
} else {
    //Register all acf blocks
    include ("blocks/blocks.php");

    //Include General Settings - (General, Custom Posts, Template Swopper, Style/Script Injection)
    include ("general-settings/settings.php");

    //Include useful functions
    include("general-settings/useful-functions.php");

    //Include patterns
    include("patterns/include-patterns.php");
}