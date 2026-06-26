<?php
/**
 * Debug: debug toggle and debug log viewer.
 *
 * Diagnostics for developers / support.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ---- Settings helpers ---------------------------------------------------- */

function braftonium_dev_tools_settings() {
    return wp_parse_args(
        braftonium_get_dev_tools_settings(),
        array(
            'debug_on' => false,
        )
    );
}

function braftonium_debug_log_path() {
    // Honours a custom WP_DEBUG_LOG path; otherwise the default location.
    if ( defined( 'WP_DEBUG_LOG' ) && is_string( WP_DEBUG_LOG ) ) {
        return WP_DEBUG_LOG;
    }
    return WP_CONTENT_DIR . '/debug.log';
}

/**
 * Read the tail of a file without loading the whole thing.
 */
function braftonium_tail_file( $path, $max_bytes = 262144, $max_lines = 400 ) {
    if ( ! is_readable( $path ) ) {
        return '';
    }
    $size = filesize( $path );
    $fh   = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
    if ( ! $fh ) {
        return '';
    }
    if ( $size > $max_bytes ) {
        fseek( $fh, -$max_bytes, SEEK_END );
        fgets( $fh ); // discard partial first line
    }
    $data = stream_get_contents( $fh );
    fclose( $fh ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

    $lines = preg_split( "/\r\n|\n|\r/", (string) $data );
    $lines = array_slice( $lines, -$max_lines );
    return implode( "\n", $lines );
}

/* ---- Admin page ---------------------------------------------------------- */

add_action( 'admin_menu', 'braftonium_register_dev_tools_page' );
function braftonium_register_dev_tools_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'Debug', 'braftonium' ),
        __( 'Debug', 'braftonium' ),
        'manage_options',
        'braftonium-dev-tools',
        'braftonium_render_dev_tools_page'
    );
}

add_action( 'admin_post_braftonium_save_dev_tools', 'braftonium_save_dev_tools' );
function braftonium_save_dev_tools() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }
    check_admin_referer( 'braftonium_dev_tools_save' );

    $settings = array(
        'debug_on' => ! empty( $_POST['debug_on'] ),
    );

    update_option( 'braftonium_dev_tools', $settings );
    braftonium_legacy_acf_update_option( 'debug-on', $settings['debug_on'] ? array( 'on' ) : array() );

    wp_safe_redirect( add_query_arg( array( 'page' => 'braftonium-dev-tools', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
    exit;
}

add_action( 'admin_post_braftonium_clear_debug_log', 'braftonium_clear_debug_log' );
function braftonium_clear_debug_log() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }
    check_admin_referer( 'braftonium_clear_debug_log' );

    $path = braftonium_debug_log_path();
    if ( is_writable( $path ) ) {
        file_put_contents( $path, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
    }

    wp_safe_redirect( add_query_arg( array( 'page' => 'braftonium-dev-tools', 'cleared' => 'debug' ), admin_url( 'admin.php' ) ) );
    exit;
}

function braftonium_render_dev_tools_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $settings = braftonium_dev_tools_settings();
    $log_path = braftonium_debug_log_path();
    $log_tail = braftonium_tail_file( $log_path );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Debug', 'braftonium' ); ?></h1>

        <?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>
        <?php if ( isset( $_GET['cleared'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Log cleared.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_dev_tools" />
            <?php wp_nonce_field( 'braftonium_dev_tools_save' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Turn Debug On', 'braftonium' ); ?></th>
                        <td><label><input type="checkbox" name="debug_on" value="1" <?php checked( ! empty( $settings['debug_on'] ) ); ?> /> <?php esc_html_e( 'Enable debug for administrators only.', 'braftonium' ); ?></label></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>

        <h2 style="margin-top:2em;"><?php esc_html_e( 'Debug Log', 'braftonium' ); ?></h2>
        <p class="description">
            <?php
            printf(
                /* translators: %s: log file path */
                esc_html__( 'Showing the end of %s. Requires WP_DEBUG_LOG to be enabled in wp-config.php.', 'braftonium' ),
                '<code>' . esc_html( $log_path ) . '</code>'
            );
            ?>
        </p>
        <textarea readonly rows="16" class="large-text code" style="white-space:pre;overflow:auto;"><?php echo esc_textarea( $log_tail ? $log_tail : __( '(log is empty or not found)', 'braftonium' ) ); ?></textarea>
        <p>
            <a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => 'braftonium-dev-tools' ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Refresh', 'braftonium' ); ?></a>
            <?php if ( is_writable( $log_path ) ) : ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
                    <input type="hidden" name="action" value="braftonium_clear_debug_log" />
                    <?php wp_nonce_field( 'braftonium_clear_debug_log' ); ?>
                    <button type="submit" class="button"><?php esc_html_e( 'Clear Debug Log', 'braftonium' ); ?></button>
                </form>
            <?php endif; ?>
        </p>
    </div>
    <?php
}

/* ---- Runtime: debug mode ------------------------------------------------- */

add_action( 'init', 'braftonium_apply_debug_mode', 1 );
function braftonium_apply_debug_mode() {
    $s = braftonium_dev_tools_settings();
    if ( ! empty( $s['debug_on'] ) && current_user_can( 'manage_options' ) ) {
        error_reporting( E_ALL );
        ini_set( 'display_errors', 1 );
    }
}
