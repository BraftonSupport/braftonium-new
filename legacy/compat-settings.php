<?php
/**
 * Compatibility helpers for legacy ACF option data.
 *
 * Do not load the old ACF settings pages here; those pages use the same slugs
 * and several of the same function names as the native implementation. These
 * helpers let native settings read old data and mirror saves back to ACF when
 * ACF is installed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function braftonium_legacy_acf_get_option( $selector, $default = null ) {
    if ( ! function_exists( 'get_field' ) ) {
        return $default;
    }

    $value = get_field( $selector, 'option' );
    return null === $value ? $default : $value;
}

function braftonium_legacy_acf_update_option( $selector, $value ) {
    if ( function_exists( 'update_field' ) ) {
        update_field( $selector, $value, 'option' );
    }
}

function braftonium_get_general_settings() {
    $settings = get_option( 'braftonium_general_settings', array() );
    if ( ! is_array( $settings ) ) {
        $settings = array();
    }

    if ( empty( $settings['admin_override'] ) ) {
        $legacy_admin = braftonium_legacy_acf_get_option( 'admin-override', '' );
        if ( is_string( $legacy_admin ) && '' !== $legacy_admin ) {
            $settings['admin_override'] = $legacy_admin;
        }
    }

    if ( empty( $settings['google_api_key'] ) ) {
        $legacy_google_api_key = braftonium_legacy_acf_get_option( 'google-api-key', '' );
        if ( is_string( $legacy_google_api_key ) && '' !== $legacy_google_api_key ) {
            $settings['google_api_key'] = $legacy_google_api_key;
        }
    }

    return $settings;
}

function braftonium_get_dev_tools_settings() {
    $settings = get_option( 'braftonium_dev_tools', array() );
    if ( ! is_array( $settings ) ) {
        $settings = array();
    }

    if ( ! array_key_exists( 'debug_on', $settings ) ) {
        $legacy_debug = braftonium_legacy_acf_get_option( 'debug-on', array() );
        $settings['debug_on'] = is_array( $legacy_debug ) && in_array( 'on', $legacy_debug, true );
    }

    return $settings;
}

function braftonium_get_custom_taxonomies_option() {
    $taxonomies = get_option( 'braftonium_custom_post_types_taxonomies', array() );
    if ( ! empty( $taxonomies ) ) {
        return $taxonomies;
    }

    return braftonium_legacy_acf_get_option( 'custom_post_types_taxonomies', array() );
}

function braftonium_get_custom_post_types_option() {
    $post_types = get_option( 'braftonium_custom_post_types_new', array() );
    if ( ! empty( $post_types ) ) {
        return $post_types;
    }

    return braftonium_legacy_acf_get_option( 'custom_post_types_new', array() );
}

function braftonium_get_global_injector_option() {
    $rules = get_option( 'braftonium_injector', array() );
    if ( ! empty( $rules ) ) {
        return $rules;
    }

    return braftonium_legacy_acf_get_option( 'braftonium_injector', array() );
}

function braftonium_get_local_injector_option( $post_id ) {
    $rules = get_post_meta( $post_id, '_braftonium_injector', true );
    if ( ! empty( $rules ) && is_array( $rules ) ) {
        return $rules;
    }

    if ( function_exists( 'get_field' ) ) {
        $legacy_rules = get_field( 'braftonium_injector', $post_id );
        if ( is_array( $legacy_rules ) ) {
            return $legacy_rules;
        }
    }

    return array();
}
