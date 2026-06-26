<?php
/**
 * Legacy compatibility bootstrap.
 *
 * Keeps existing ACF-based Braftonium content working while the root plugin
 * uses native WordPress fields and native Gutenberg blocks as the core.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/compat-settings.php';

if ( braftonium_feature_enabled( 'blocks' ) && function_exists( 'acf_register_block_type' ) ) {
    require_once __DIR__ . '/blocks/blocks.php';
}
