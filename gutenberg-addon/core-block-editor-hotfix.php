<?php
/**
 * Hotfix for a missing null guard in core's block editor bundle.
 *
 * wp-includes/js/dist/block-editor.js, useBlockToolbarPopoverProps():
 *
 *     const blockView = selectedBlockElement?.ownerDocument?.defaultView;
 *     if ( blockView.ResizeObserver ) { ... }
 *
 * defaultView is null once the document that held the block element is gone -
 * a torn down editor or pattern preview iframe - so the layout effect throws
 * "Cannot read properties of null (reading 'ResizeObserver')" and the editor
 * error boundary replaces the screen. It shows up here when copying patterns.
 *
 * Gutenberg trunk added the missing `?.`; no released WordPress has it
 * (checked 6.6 through 6.9), so we serve a patched copy of the bundle from
 * uploads instead. Everything is keyed off the core file's mtime and size, so
 * a core update regenerates the patch, and once core ships the guard the
 * pattern stops matching and we fall back to serving core untouched.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Serve the patched bundle in place of core's.
 *
 * @param string $src    Script source URL.
 * @param string $handle Script handle.
 *
 * @return string Source URL.
 */
function braftonium_block_editor_hotfix_src( $src, $handle ) {
    if ( 'wp-block-editor' !== $handle ) {
        return $src;
    }

    static $patched_url = null;

    if ( null === $patched_url ) {
        $patched_url = braftonium_block_editor_hotfix_build();
    }

    return $patched_url ? $patched_url : $src;
}
add_filter( 'script_loader_src', 'braftonium_block_editor_hotfix_src', 10, 2 );

/**
 * Path of the core bundle currently being served.
 *
 * @return string Absolute path.
 */
function braftonium_block_editor_hotfix_source_path() {
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    return ABSPATH . WPINC . '/js/dist/block-editor' . $suffix . '.js';
}

/**
 * Whether to log the offending element when the guard fires.
 *
 * Define BRAFTONIUM_BLOCK_EDITOR_DEBUG to control this independently of
 * WP_DEBUG.
 *
 * @return bool True to log.
 */
function braftonium_block_editor_hotfix_is_debug() {
    if ( defined( 'BRAFTONIUM_BLOCK_EDITOR_DEBUG' ) ) {
        return (bool) BRAFTONIUM_BLOCK_EDITOR_DEBUG;
    }

    return defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * Build the patched bundle, reusing it once written.
 *
 * @return string|false URL of the patched bundle, or false to use core's.
 */
function braftonium_block_editor_hotfix_build() {
    $source = braftonium_block_editor_hotfix_source_path();

    if ( ! is_readable( $source ) ) {
        return false;
    }

    $uploads = wp_get_upload_dir();

    if ( ! empty( $uploads['error'] ) ) {
        return false;
    }

    $debug    = braftonium_block_editor_hotfix_is_debug();
    $key      = substr( md5( $source . filemtime( $source ) . filesize( $source ) . (int) $debug ), 0, 12 );
    $filename = 'block-editor-' . $key . '.js';
    $dir      = trailingslashit( $uploads['basedir'] ) . 'braftonium-core-patches';
    $file     = $dir . '/' . $filename;
    $url      = trailingslashit( $uploads['baseurl'] ) . 'braftonium-core-patches/' . $filename;

    if ( file_exists( $file ) ) {
        // An empty file is the marker for "this core build needs no patch".
        return filesize( $file ) ? $url : false;
    }

    if ( ! wp_mkdir_p( $dir ) ) {
        return false;
    }

    $js = file_get_contents( $source );

    if ( false === $js ) {
        return false;
    }

    $count = 0;

    // With debugging on, report the offending element before skipping the
    // observer, so whatever leaves a block element in a window-less document
    // can be identified instead of guessed at. `$1` is the defaultView, `$2`
    // the block element; the surrounding statement is a comma sequence that
    // returns the effect's cleanup function, so prepending an expression to it
    // is safe. Minified core only - SCRIPT_DEBUG core gets the plain guard.
    if ( $debug ) {
        $js = preg_replace(
            '/([A-Za-z_$][\w$]*)=([A-Za-z_$][\w$]*)\?\.ownerDocument\?\.defaultView;return \1\.ResizeObserver&&\(/',
            '$1=$2?.ownerDocument?.defaultView;return ($1||console.warn("[braftonium] block toolbar: ownerDocument.defaultView is null",{element:$2,ownerDocument:$2?.ownerDocument,html:$2?.outerHTML?.slice(0,300)})),$1.ResizeObserver&&(',
            $js,
            -1,
            $count
        );
    }

    // Minified core reads `X.ResizeObserver&&(`, SCRIPT_DEBUG core reads
    // `if ( X.ResizeObserver )`. Only the block toolbar hook matches either -
    // every other use in the bundle goes through `window.ResizeObserver`.
    $guarded = 0;

    $patched = preg_replace(
        array(
            '/(?<![\w$.])([A-Za-z_$][\w$]*)\.ResizeObserver&&\(/',
            '/if\s*\(\s*([A-Za-z_$][\w$]*)\.ResizeObserver\s*\)/',
        ),
        array(
            '$1?.ResizeObserver&&(',
            'if ( $1?.ResizeObserver )',
        ),
        $js,
        -1,
        $guarded
    );

    if ( ! $guarded ) {
        // The guard is the point of this file; a log line alone isn't worth
        // serving a copy of the bundle for.
        $patched = null;
    }

    if ( null === $patched || ! $guarded ) {
        // Core is fixed (or unrecognisable): stop rebuilding on every load.
        file_put_contents( $file, '' );

        return false;
    }

    if ( false === file_put_contents( $file, $patched ) ) {
        return false;
    }

    braftonium_block_editor_hotfix_cleanup( $dir, $filename );

    return $url;
}

/**
 * Drop patches built for older core files.
 *
 * @param string $dir  Patch directory.
 * @param string $keep Filename to keep.
 *
 * @return void
 */
function braftonium_block_editor_hotfix_cleanup( $dir, $keep ) {
    $stale = glob( $dir . '/block-editor-*.js' );

    if ( ! is_array( $stale ) ) {
        return;
    }

    foreach ( $stale as $path ) {
        if ( basename( $path ) !== $keep ) {
            @unlink( $path );
        }
    }
}
