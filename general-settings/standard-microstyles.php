<?php
/**
 * Braftonium STANDARD MicroStyles — registration + enqueue.
 *
 * Ships a set of generic, project-agnostic microstyles as PLUGIN standards so
 * every Braftonium site gets them without theme code:
 *   1. Registers them in the "Braftonium MicroStyles" editor control (the
 *      `braftonium_class_list` filter) — universal ones on every block, text ones
 *      on text-bearing blocks.
 *   2. Enqueues the stylesheet (general-settings/standard-microstyles.css) on
 *      `enqueue_block_assets`, so the CSS loads on BOTH the front end and inside
 *      the block-editor / Site-Editor canvas iframe (editor parity).
 *
 * Canonical class names are `braftonium-*`; the CSS file also carries `ms-*`
 * legacy aliases for back-compat. New work uses the `braftonium-*` labels offered
 * here. Per-project microstyles still live in the active theme's own
 * `braftonium_class_list` hook.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the standard microstyles in the editor's class-list control.
 *
 * @param array  $class_list Existing { label, value } options.
 * @param string $block_type Block being edited (e.g. "core/paragraph").
 * @return array
 */
function braftonium_register_standard_microstyles( $class_list, $block_type ) {

	// Universal — offered on every block.
	$universal = array(
		array( 'label' => 'No top margin',                         'value' => 'braftonium-mt-0' ),
		array( 'label' => 'No bottom margin',                      'value' => 'braftonium-mb-0' ),
		array( 'label' => 'Full bleed (edge to edge)',             'value' => 'braftonium-full-bleed' ),
		array( 'label' => 'Dark image overlay',                    'value' => 'braftonium-overlay-dark' ),
		array( 'label' => 'Span 2 grid columns',                   'value' => 'braftonium-col-span-2' ),
		array( 'label' => 'Span 3 grid columns',                   'value' => 'braftonium-col-span-3' ),
		array( 'label' => 'Span 4 grid columns',                   'value' => 'braftonium-col-span-4' ),
		array( 'label' => 'Half top padding (grouped section)',    'value' => 'braftonium-pt-half' ),
		array( 'label' => 'Half bottom padding (grouped section)', 'value' => 'braftonium-pb-half' ),
	);

	// Text — only meaningful on text-bearing blocks.
	$text_blocks = array( 'core/heading', 'core/paragraph', 'core/list', 'core/buttons', 'core/group' );
	$text = array(
		array( 'label' => 'Eyebrow (uppercase label)', 'value' => 'braftonium-eyebrow' ),
		array( 'label' => 'Uppercase',                 'value' => 'braftonium-uppercase' ),
		array( 'label' => 'Tight letter-spacing',      'value' => 'braftonium-tracking-tight' ),
		array( 'label' => 'Wide letter-spacing',       'value' => 'braftonium-tracking-wide' ),
		array( 'label' => 'Constrain reading width',   'value' => 'braftonium-measure' ),
	);

	$class_list = array_merge( $class_list, $universal );
	if ( in_array( $block_type, $text_blocks, true ) ) {
		$class_list = array_merge( $class_list, $text );
	}

	return $class_list;
}
add_filter( 'braftonium_class_list', 'braftonium_register_standard_microstyles', 10, 2 );

/**
 * Enqueue the standard-microstyles stylesheet on the front end AND in the editor
 * canvas (iframe). `enqueue_block_assets` fires in both contexts.
 */
function braftonium_enqueue_standard_microstyles() {
	$relative_path = 'general-settings/standard-microstyles.css';
	$absolute_path = plugin_dir_path( dirname( __FILE__ ) ) . $relative_path;
	$version       = file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : '1.0';

	wp_enqueue_style(
		'braftonium-standard-microstyles',
		plugin_dir_url( dirname( __FILE__ ) ) . $relative_path,
		array(),
		$version
	);
}
add_action( 'enqueue_block_assets', 'braftonium_enqueue_standard_microstyles' );
