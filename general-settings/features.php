<?php
/**
 * Feature flags.
 *
 * Lets an admin switch whole plugin features on/off from the main Braftonium
 * settings page. Stored inside the braftonium_general_settings option under
 * the 'features' key. Anything not explicitly set defaults to ENABLED, so
 * existing installs keep all features until the admin changes something.
 *
 * Loaded before the feature modules so includes can be gated on it.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Toggleable features: key => array( label, description ).
 */
function braftonium_feature_registry() {
    return array(
        'blocks'             => array(
            'label'       => __( 'Blocks', 'braftonium' ),
            'description' => __( 'Native Braftonium Gutenberg blocks (banner, cta, slider, lists…).', 'braftonium' ),
        ),
        'patterns'           => array(
            'label'       => __( 'Block Patterns', 'braftonium' ),
            'description' => __( 'Braftonium block pattern library.', 'braftonium' ),
        ),
        'inject_scripts'     => array(
            'label'       => __( 'Scripts & Styles', 'braftonium' ),
            'description' => __( 'Inline JS/CSS and external enqueue rules (global + per post).', 'braftonium' ),
        ),
        'custom_posts'       => array(
            'label'       => __( 'Custom Post Types', 'braftonium' ),
            'description' => __( 'Register custom post types and taxonomies.', 'braftonium' ),
        ),
        'dev_tools'          => array(
            'label'       => __( 'Debug', 'braftonium' ),
            'description' => __( 'Debug toggle, debug log viewer and email delivery log.', 'braftonium' ),
        ),
    );
}

/**
 * Whether a feature is enabled. Unknown / unset keys default to true.
 */
function braftonium_feature_enabled( $key ) {
    $settings = get_option( 'braftonium_general_settings', array() );
    $features = ( isset( $settings['features'] ) && is_array( $settings['features'] ) ) ? $settings['features'] : array();

    if ( ! array_key_exists( $key, $features ) ) {
        return true;
    }

    return ! empty( $features[ $key ] );
}
