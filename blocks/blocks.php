<?php
/**
 * Braftonium native Gutenberg block loader.
 *
 * Discovers every block.json under /blocks/<slug>/block.json and
 * registers it with WordPress. Dynamic blocks are rendered by the
 * accompanying render.php files referenced from block.json.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add the "braftonium" block category to the editor.
 */
add_filter( 'block_categories_all', function ( $categories ) {
    foreach ( $categories as $cat ) {
        if ( isset( $cat['slug'] ) && $cat['slug'] === 'braftonium' ) {
            return $categories;
        }
    }
    array_unshift( $categories, array(
        'slug'  => 'braftonium',
        'title' => __( 'Braftonium', 'braftonium' ),
        'icon'  => null,
    ) );
    return $categories;
} );

/**
 * Microstyles shipped with the plugin.
 *
 * Exposes two background-width microstyles in the Braftonium Microstyles
 * control (block editor → Advanced panel) for the container blocks. The inner
 * content always stays at the content width; the microstyle only changes how
 * wide the block's background is:
 *   - Full Width (braftonium-bg-full): background spans the full viewport width.
 *   - Wrapper    (braftonium-bg-wrap): background is constrained to the content width.
 *
 * Registered through the documented braftonium_class_list filter, so themes can
 * still add their own microstyles alongside these.
 */
add_filter( 'braftonium_class_list', function ( $class_list, $block_type ) {
    $supported = array(
        'braftonium/banner',
        'braftonium/cta',
        'braftonium/custom-row',
        'braftonium/custom-list',
        'braftonium/slider',
    );

    if ( ! in_array( $block_type, $supported, true ) ) {
        return $class_list;
    }

    if ( ! is_array( $class_list ) ) {
        $class_list = array();
    }

    $class_list[] = array(
        'label' => __( 'Full Width', 'braftonium' ),
        'value' => 'braftonium-bg-full',
    );
    $class_list[] = array(
        'label' => __( 'Wrapper', 'braftonium' ),
        'value' => 'braftonium-bg-wrap',
    );

    return $class_list;
}, 10, 2 );

/**
 * Register every block whose folder contains a block.json file.
 */
add_action( 'init', function () {
    $blocks_dir = __DIR__;
    $entries    = glob( $blocks_dir . '/*/block.json' );

    if ( empty( $entries ) ) {
        return;
    }

    foreach ( $entries as $block_json ) {
        register_block_type( dirname( $block_json ) );
    }
} );

/**
 * Enqueue Slick in the block editor so the Slider block's "Preview" toggle can
 * initialise a live carousel while authoring.
 */
add_action( 'enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
        array(),
        '1.8.1'
    );
    wp_enqueue_style(
        'slick-carousel-theme',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
        array( 'slick-carousel' ),
        '1.8.1'
    );
    wp_enqueue_script(
        'slick-carousel',
        'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
        array( 'jquery' ),
        '1.8.1',
        true
    );
} );

/**
 * Enqueue Slick (slider) and Swiper assets on the frontend when those
 * blocks are present on the current page.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( is_admin() ) {
        return;
    }

    if ( ! function_exists( 'has_block' ) ) {
        return;
    }

    $needs_slick   = has_block( 'braftonium/slider' );
    $needs_swiper  = has_block( 'braftonium/swiper' );

    if ( $needs_slick ) {
        wp_enqueue_style(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
            array(),
            '1.8.1'
        );
        wp_enqueue_style(
            'slick-carousel-theme',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
            array( 'slick-carousel' ),
            '1.8.1'
        );
        wp_enqueue_script(
            'slick-carousel',
            'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
            array( 'jquery' ),
            '1.8.1',
            true
        );
        wp_add_inline_script( 'slick-carousel', "jQuery(function(\$){\$('.braftonium-slider').each(function(){var \$el=\$(this);if(\$el.hasClass('slick-initialized'))return;var cfg=\$el.data('slick-config')||{};\$el.find('> .braftonium-slide').wrapAll('<div class=\"braftonium-slider-track\"></div>');\$el.children('.braftonium-slider-track').slick(cfg);});});" );
    }

    if ( $needs_swiper ) {
        wp_enqueue_style(
            'swiper-css',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            array(),
            '11'
        );
        wp_enqueue_script(
            'swiper-js',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            array(),
            '11',
            true
        );
        wp_add_inline_script( 'swiper-js', "document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.braftonium-swiper .swiper').forEach(function(el){if(el.swiper)return;new Swiper(el,{slidesPerView:1,spaceBetween:16,loop:false,pagination:{el:el.querySelector('.swiper-pagination'),clickable:true},navigation:{nextEl:el.querySelector('.swiper-button-next'),prevEl:el.querySelector('.swiper-button-prev')}});});});" );
    }
} );
