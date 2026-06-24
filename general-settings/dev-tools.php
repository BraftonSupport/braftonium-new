<?php
/**
 * Dev Tools: system info, debug log viewer and email delivery log.
 *
 * Read-only diagnostics for developers / support, plus a record of outgoing
 * email so deliverability can be confirmed or debugged.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ---- Settings helpers ---------------------------------------------------- */

function braftonium_dev_tools_settings() {
    return wp_parse_args( get_option( 'braftonium_dev_tools', array() ), array( 'email_log' => true ) );
}

function braftonium_email_log_enabled() {
    $s = braftonium_dev_tools_settings();
    return ! empty( $s['email_log'] );
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
        __( 'Dev Tools', 'braftonium' ),
        __( 'Dev Tools', 'braftonium' ),
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

    update_option( 'braftonium_dev_tools', array( 'email_log' => ! empty( $_POST['email_log'] ) ) );

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

add_action( 'admin_post_braftonium_clear_email_log', 'braftonium_clear_email_log' );
function braftonium_clear_email_log() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }
    check_admin_referer( 'braftonium_clear_email_log' );

    update_option( 'braftonium_email_log', array(), false );

    wp_safe_redirect( add_query_arg( array( 'page' => 'braftonium-dev-tools', 'cleared' => 'email' ), admin_url( 'admin.php' ) ) );
    exit;
}

function braftonium_system_info() {
    global $wpdb;
    $theme = wp_get_theme();

    return array(
        __( 'WordPress version', 'braftonium' ) => get_bloginfo( 'version' ),
        __( 'Multisite', 'braftonium' )         => is_multisite() ? __( 'Yes', 'braftonium' ) : __( 'No', 'braftonium' ),
        __( 'PHP version', 'braftonium' )        => phpversion(),
        __( 'Database', 'braftonium' )           => $wpdb->db_version(),
        __( 'Server', 'braftonium' )             => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '—',
        __( 'PHP memory_limit', 'braftonium' )   => ini_get( 'memory_limit' ),
        __( 'WP memory limit', 'braftonium' )    => ( defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : '—' ) . ' / ' . ( defined( 'WP_MAX_MEMORY_LIMIT' ) ? WP_MAX_MEMORY_LIMIT : '—' ),
        __( 'Max execution time', 'braftonium' ) => ini_get( 'max_execution_time' ) . 's',
        __( 'Upload max filesize', 'braftonium' ) => ini_get( 'upload_max_filesize' ),
        __( 'Post max size', 'braftonium' )      => ini_get( 'post_max_size' ),
        __( 'Max input vars', 'braftonium' )     => ini_get( 'max_input_vars' ),
        __( 'Active theme', 'braftonium' )       => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
        __( 'Active plugins', 'braftonium' )     => (string) count( (array) get_option( 'active_plugins', array() ) ),
        __( 'WP_DEBUG', 'braftonium' )           => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? __( 'On', 'braftonium' ) : __( 'Off', 'braftonium' ),
        __( 'WP_DEBUG_LOG', 'braftonium' )       => ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ? __( 'On', 'braftonium' ) : __( 'Off', 'braftonium' ),
    );
}

function braftonium_render_dev_tools_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $settings  = braftonium_dev_tools_settings();
    $log_path  = braftonium_debug_log_path();
    $log_tail  = braftonium_tail_file( $log_path );
    $email_log = get_option( 'braftonium_email_log', array() );
    if ( ! is_array( $email_log ) ) {
        $email_log = array();
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Dev Tools', 'braftonium' ); ?></h1>

        <?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>
        <?php if ( isset( $_GET['cleared'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Log cleared.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <h2><?php esc_html_e( 'System Info', 'braftonium' ); ?></h2>
        <table class="widefat striped" style="max-width:760px;">
            <tbody>
                <?php foreach ( braftonium_system_info() as $label => $val ) : ?>
                    <tr>
                        <td style="width:220px;"><strong><?php echo esc_html( $label ); ?></strong></td>
                        <td><code><?php echo esc_html( $val ); ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

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

        <h2 style="margin-top:2em;"><?php esc_html_e( 'Email Delivery Log', 'braftonium' ); ?></h2>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:12px;">
            <input type="hidden" name="action" value="braftonium_save_dev_tools" />
            <?php wp_nonce_field( 'braftonium_dev_tools_save' ); ?>
            <label><input type="checkbox" name="email_log" value="1" <?php checked( ! empty( $settings['email_log'] ) ); ?> /> <?php esc_html_e( 'Log outgoing email (to / subject / status). Keeps the last 50.', 'braftonium' ); ?></label>
            <?php submit_button( __( 'Save', 'braftonium' ), 'secondary', 'submit', false ); ?>
        </form>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width:160px;"><?php esc_html_e( 'Time', 'braftonium' ); ?></th>
                    <th><?php esc_html_e( 'To', 'braftonium' ); ?></th>
                    <th><?php esc_html_e( 'Subject', 'braftonium' ); ?></th>
                    <th style="width:90px;"><?php esc_html_e( 'Status', 'braftonium' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $email_log ) ) : ?>
                    <tr><td colspan="4"><?php esc_html_e( 'No email logged yet.', 'braftonium' ); ?></td></tr>
                <?php else : ?>
                    <?php foreach ( $email_log as $entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( isset( $entry['time'] ) ? wp_date( 'Y-m-d H:i:s', (int) $entry['time'] ) : '—' ); ?></td>
                            <td><?php echo esc_html( $entry['to'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['subject'] ?? '' ); ?></td>
                            <td>
                                <?php if ( ( $entry['status'] ?? '' ) === 'failed' ) : ?>
                                    <span style="color:#b32d2e;font-weight:600;" title="<?php echo esc_attr( $entry['error'] ?? '' ); ?>"><?php esc_html_e( 'Failed', 'braftonium' ); ?></span>
                                <?php else : ?>
                                    <span style="color:#1a7f37;"><?php esc_html_e( 'Sent', 'braftonium' ); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ( ! empty( $email_log ) ) : ?>
            <p>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="braftonium_clear_email_log" />
                    <?php wp_nonce_field( 'braftonium_clear_email_log' ); ?>
                    <button type="submit" class="button"><?php esc_html_e( 'Clear Email Log', 'braftonium' ); ?></button>
                </form>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

/* ---- Email logging ------------------------------------------------------- */

add_filter( 'wp_mail', 'braftonium_log_wp_mail' );
function braftonium_log_wp_mail( $args ) {
    if ( ! braftonium_email_log_enabled() ) {
        return $args;
    }

    $to = isset( $args['to'] ) ? $args['to'] : '';
    if ( is_array( $to ) ) {
        $to = implode( ', ', $to );
    }

    $log = get_option( 'braftonium_email_log', array() );
    if ( ! is_array( $log ) ) {
        $log = array();
    }

    array_unshift( $log, array(
        'time'    => time(),
        'to'      => (string) $to,
        'subject' => isset( $args['subject'] ) ? (string) $args['subject'] : '',
        'status'  => 'sent',
        'error'   => '',
    ) );

    $log = array_slice( $log, 0, 50 );
    update_option( 'braftonium_email_log', $log, false );

    return $args;
}

add_action( 'wp_mail_failed', 'braftonium_log_wp_mail_failed' );
function braftonium_log_wp_mail_failed( $wp_error ) {
    if ( ! braftonium_email_log_enabled() ) {
        return;
    }

    $log = get_option( 'braftonium_email_log', array() );
    if ( ! is_array( $log ) || empty( $log ) ) {
        return;
    }

    // wp_mail_failed fires synchronously inside the same wp_mail() call, so the
    // most recent entry (index 0) is the one that just failed.
    $log[0]['status'] = 'failed';
    $log[0]['error']  = is_wp_error( $wp_error ) ? $wp_error->get_error_message() : '';
    update_option( 'braftonium_email_log', $log, false );
}
