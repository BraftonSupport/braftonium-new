<?php
/**
 * Block pattern loader.
 *
 * Registers block patterns the same way a block THEME does: instead of calling
 * register_block_pattern() by hand in every file, you drop a header-annotated
 * `.php` file into the plugin's /patterns folder (or a theme's
 * /braftonium/patterns folder) and it is registered automatically.
 *
 * A pattern file is just a docblock of recognised headers followed by the block
 * markup, e.g.:
 *
 *     <?php
 *     /**
 *      * Title:          Two Column (Text + Image)
 *      * Slug:           braftonium/two-column
 *      * Categories:     braftonium, braftonium-sections
 *      * Keywords:       two column, media text, split
 *      * Viewport Width: 1200
 *      * Description:    A basic two-column layout.
 *      *
 *      * (An AI / REUSE FRONTAGE docblock can follow here — extra @-tag lines are
 *      *  ignored by WordPress, so they are safe to keep alongside the headers.)
 *      *\/
 *     ?>
 *     <!-- wp:columns -->…<!-- /wp:columns -->
 *
 * Recognised headers mirror WordPress core's theme pattern headers: Title, Slug,
 * Description, Viewport Width, Inserter, Categories, Keywords, Block Types,
 * Post Types, Template Types. Title + Slug are required; a file lacking either is
 * skipped (so this loader file, READMEs, partials, etc. are ignored).
 *
 * The file's OUTPUT is the pattern content, so PHP in the file runs first (handy
 * for content_url() image paths, etc.) — exactly like core theme patterns.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base pattern categories registered for the plugin. Files reference these by
 * slug in their `Categories:` header. Any category a file references that is not
 * in this list is auto-registered with a humanised label, so new categories work
 * drop-in without editing this file.
 */
function braftonium_pattern_categories() {
	return array(
		'braftonium'            => __( 'Braftonium', 'braftonium' ),
		'braftonium-sections'   => __( 'Braftonium: Sections', 'braftonium' ),
		'braftonium-containers' => __( 'Braftonium: Containers', 'braftonium' ),
		'braftonium-components' => __( 'Braftonium: Components', 'braftonium' ),
	);
}

/**
 * Ensure a pattern category is registered (idempotent). Unknown slugs get a
 * humanised label derived from the slug.
 *
 * @param string $category_slug Category slug, e.g. "braftonium-sections".
 */
function braftonium_ensure_pattern_category( $category_slug ) {
	$categories_registry = WP_Block_Pattern_Categories_Registry::get_instance();
	if ( $categories_registry->is_registered( $category_slug ) ) {
		return;
	}

	$known_categories = braftonium_pattern_categories();
	$label            = isset( $known_categories[ $category_slug ] )
		? $known_categories[ $category_slug ]
		: ucwords( str_replace( array( '-', '_', '/' ), ' ', $category_slug ) );

	register_block_pattern_category( $category_slug, array( 'label' => $label ) );
}

/**
 * Register every header-annotated pattern file in a directory.
 *
 * @param string $patterns_directory Absolute path to a folder of pattern files.
 */
function braftonium_register_patterns_from_dir( $patterns_directory ) {
	if ( ! is_dir( $patterns_directory ) ) {
		return;
	}

	$pattern_files = glob( $patterns_directory . '/*.php' );
	if ( empty( $pattern_files ) ) {
		return;
	}

	$header_map = array(
		'title'         => 'Title',
		'slug'          => 'Slug',
		'description'   => 'Description',
		'viewportWidth' => 'Viewport Width',
		'inserter'      => 'Inserter',
		'categories'    => 'Categories',
		'keywords'      => 'Keywords',
		'blockTypes'    => 'Block Types',
		'postTypes'     => 'Post Types',
		'templateTypes' => 'Template Types',
	);
	$list_headers     = array( 'categories', 'keywords', 'blockTypes', 'postTypes', 'templateTypes' );
	$patterns_registry = WP_Block_Patterns_Registry::get_instance();

	foreach ( $pattern_files as $pattern_file ) {
		// Skip this loader file and any "partial" file prefixed with an underscore
		// (e.g. _template.php) — those are not patterns.
		if ( '_' === substr( basename( $pattern_file ), 0, 1 ) ) {
			continue;
		}

		$headers = get_file_data( $pattern_file, $header_map );

		// Title + Slug are required; anything else (READMEs, helpers) is skipped.
		if ( empty( $headers['title'] ) || empty( $headers['slug'] ) ) {
			continue;
		}
		if ( $patterns_registry->is_registered( $headers['slug'] ) ) {
			continue;
		}

		$pattern_properties = array( 'title' => $headers['title'] );

		foreach ( $list_headers as $list_header ) {
			if ( ! empty( $headers[ $list_header ] ) ) {
				$pattern_properties[ $list_header ] = array_values(
					array_filter( array_map( 'trim', explode( ',', $headers[ $list_header ] ) ) )
				);
			}
		}

		if ( '' !== $headers['description'] ) {
			$pattern_properties['description'] = $headers['description'];
		}
		if ( '' !== $headers['viewportWidth'] ) {
			$pattern_properties['viewportWidth'] = (int) $headers['viewportWidth'];
		}
		if ( '' !== $headers['inserter'] ) {
			$pattern_properties['inserter'] = ! in_array(
				strtolower( $headers['inserter'] ),
				array( 'no', 'false', '0' ),
				true
			);
		}

		// Make sure every referenced category exists before registering.
		if ( ! empty( $pattern_properties['categories'] ) ) {
			foreach ( $pattern_properties['categories'] as $category_slug ) {
				braftonium_ensure_pattern_category( $category_slug );
			}
		}

		// The file's output is the pattern markup (PHP in the file runs first).
		ob_start();
		include $pattern_file;
		$pattern_properties['content'] = ob_get_clean();

		if ( '' === trim( (string) $pattern_properties['content'] ) ) {
			continue;
		}

		register_block_pattern( $headers['slug'], $pattern_properties );
	}
}

/**
 * Register the base categories, then auto-load pattern files from the plugin and,
 * for backwards compatibility, from a theme's /braftonium/patterns folder.
 */
function braftonium_register_block_patterns() {
	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return;
	}

	foreach ( braftonium_pattern_categories() as $category_slug => $category_label ) {
		braftonium_ensure_pattern_category( $category_slug );
	}

	// Plugin patterns: <plugin>/patterns/*.php
	braftonium_register_patterns_from_dir( dirname( __DIR__ ) . '/patterns' );

	// Theme-supplied Braftonium patterns: <active-theme>/braftonium/patterns/*.php
	braftonium_register_patterns_from_dir( get_template_directory() . '/braftonium/patterns' );
	if ( get_template_directory() !== get_stylesheet_directory() ) {
		braftonium_register_patterns_from_dir( get_stylesheet_directory() . '/braftonium/patterns' );
	}
}
add_action( 'init', 'braftonium_register_block_patterns' );
