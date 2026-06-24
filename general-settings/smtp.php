<?php
/**
 * SMTP email.
 *
 * Routes wp_mail() through an SMTP server (PHPMailer) so transactional email
 * actually delivers on hosts where the default mail() is unreliable. Includes a
 * "send test email" action.
 *
 * Settings stored in the braftonium_smtp option.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function braftonium_smtp_defaults() {
    return array(
        'enabled'    => false,
        'host'       => '',
        'port'       => 587,
        'encryption' => 'tls', // none | ssl | tls
        'auth'       => true,
        'username'   => '',
        'password'   => '',
        'from_email' => '',
        'from_name'  => '',
    );
}

function braftonium_get_smtp_settings() {
    return wp_parse_args( get_option( 'braftonium_smtp', array() ), braftonium_smtp_defaults() );
}

/* ---- Admin page ---------------------------------------------------------- */

add_action( 'admin_menu', 'braftonium_register_smtp_page' );
function braftonium_register_smtp_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'SMTP Email', 'braftonium' ),
        __( 'SMTP Email', 'braftonium' ),
        'manage_options',
        'braftonium-smtp',
        'braftonium_render_smtp_page'
    );
}

add_action( 'admin_post_braftonium_save_smtp', 'braftonium_save_smtp' );
function braftonium_save_smtp() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }
    check_admin_referer( 'braftonium_smtp_save' );

    $encryption = isset( $_POST['encryption'] ) ? sanitize_key( wp_unslash( $_POST['encryption'] ) ) : 'tls';
    if ( ! in_array( $encryption, array( 'none', 'ssl', 'tls' ), true ) ) {
        $encryption = 'tls';
    }

    $settings = array(
        'enabled'    => ! empty( $_POST['enabled'] ),
        'host'       => isset( $_POST['host'] ) ? sanitize_text_field( wp_unslash( $_POST['host'] ) ) : '',
        'port'       => isset( $_POST['port'] ) ? absint( $_POST['port'] ) : 587,
        'encryption' => $encryption,
        'auth'       => ! empty( $_POST['auth'] ),
        'username'   => isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '',
        'password'   => isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '',
        'from_email' => isset( $_POST['from_email'] ) ? sanitize_email( wp_unslash( $_POST['from_email'] ) ) : '',
        'from_name'  => isset( $_POST['from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['from_name'] ) ) : '',
    );

    update_option( 'braftonium_smtp', $settings );

    wp_safe_redirect( add_query_arg( array( 'page' => 'braftonium-smtp', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
    exit;
}

add_action( 'admin_post_braftonium_smtp_test', 'braftonium_smtp_test' );
function braftonium_smtp_test() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }
    check_admin_referer( 'braftonium_smtp_test' );

    $to = isset( $_POST['test_email'] ) ? sanitize_email( wp_unslash( $_POST['test_email'] ) ) : '';
    if ( ! is_email( $to ) ) {
        $to = get_option( 'admin_email' );
    }

    $error = '';
    $catch = function ( $wp_error ) use ( &$error ) {
        $error = $wp_error->get_error_message();
    };
    add_action( 'wp_mail_failed', $catch );

    $sent = wp_mail(
        $to,
        __( 'Braftonium SMTP test', 'braftonium' ),
        __( 'This is a test email from the Braftonium SMTP settings. If you received it, sending works.', 'braftonium' )
    );

    remove_action( 'wp_mail_failed', $catch );

    wp_safe_redirect( add_query_arg(
        array(
            'page'      => 'braftonium-smtp',
            'test_sent' => $sent ? '1' : '0',
            'test_to'   => rawurlencode( $to ),
            'test_err'  => $sent ? '' : rawurlencode( $error ),
        ),
        admin_url( 'admin.php' )
    ) );
    exit;
}

function braftonium_render_smtp_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $s = braftonium_get_smtp_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'SMTP Email', 'braftonium' ); ?></h1>

        <?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'SMTP settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <?php if ( isset( $_GET['test_sent'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $test_to  = isset( $_GET['test_to'] ) ? sanitize_email( wp_unslash( $_GET['test_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( '1' === $_GET['test_sent'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                <div class="notice notice-success is-dismissible"><p><?php printf( esc_html__( 'Test email sent to %s.', 'braftonium' ), '<code>' . esc_html( $test_to ) . '</code>' ); ?></p></div>
            <?php else :
                $test_err = isset( $_GET['test_err'] ) ? sanitize_text_field( wp_unslash( $_GET['test_err'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                <div class="notice notice-error is-dismissible"><p><?php printf( esc_html__( 'Test email failed: %s', 'braftonium' ), '<code>' . esc_html( $test_err ? $test_err : __( 'unknown error', 'braftonium' ) ) . '</code>' ); ?></p></div>
            <?php endif;
        endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_smtp" />
            <?php wp_nonce_field( 'braftonium_smtp_save' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable SMTP', 'braftonium' ); ?></th>
                        <td><label><input type="checkbox" name="enabled" value="1" <?php checked( $s['enabled'] ); ?> /> <?php esc_html_e( 'Route all WordPress email through this SMTP server.', 'braftonium' ); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-host"><?php esc_html_e( 'SMTP Host', 'braftonium' ); ?></label></th>
                        <td><input type="text" id="braftonium-smtp-host" name="host" class="regular-text" value="<?php echo esc_attr( $s['host'] ); ?>" placeholder="smtp.example.com" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-port"><?php esc_html_e( 'Port', 'braftonium' ); ?></label></th>
                        <td><input type="number" id="braftonium-smtp-port" name="port" class="small-text" value="<?php echo esc_attr( (string) $s['port'] ); ?>" /> <span class="description"><?php esc_html_e( '587 (TLS), 465 (SSL), 25 (none)', 'braftonium' ); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-encryption"><?php esc_html_e( 'Encryption', 'braftonium' ); ?></label></th>
                        <td>
                            <select id="braftonium-smtp-encryption" name="encryption">
                                <option value="none" <?php selected( $s['encryption'], 'none' ); ?>><?php esc_html_e( 'None', 'braftonium' ); ?></option>
                                <option value="ssl" <?php selected( $s['encryption'], 'ssl' ); ?>><?php esc_html_e( 'SSL', 'braftonium' ); ?></option>
                                <option value="tls" <?php selected( $s['encryption'], 'tls' ); ?>><?php esc_html_e( 'TLS', 'braftonium' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Authentication', 'braftonium' ); ?></th>
                        <td><label><input type="checkbox" name="auth" value="1" <?php checked( $s['auth'] ); ?> /> <?php esc_html_e( 'Use SMTP username & password.', 'braftonium' ); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-username"><?php esc_html_e( 'Username', 'braftonium' ); ?></label></th>
                        <td><input type="text" id="braftonium-smtp-username" name="username" class="regular-text" value="<?php echo esc_attr( $s['username'] ); ?>" autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-password"><?php esc_html_e( 'Password', 'braftonium' ); ?></label></th>
                        <td><input type="password" id="braftonium-smtp-password" name="password" class="regular-text" value="<?php echo esc_attr( $s['password'] ); ?>" autocomplete="new-password" /><p class="description"><?php esc_html_e( 'Stored in the database. Use an app password where possible.', 'braftonium' ); ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-from-email"><?php esc_html_e( 'From Email', 'braftonium' ); ?></label></th>
                        <td><input type="email" id="braftonium-smtp-from-email" name="from_email" class="regular-text" value="<?php echo esc_attr( $s['from_email'] ); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-smtp-from-name"><?php esc_html_e( 'From Name', 'braftonium' ); ?></label></th>
                        <td><input type="text" id="braftonium-smtp-from-name" name="from_name" class="regular-text" value="<?php echo esc_attr( $s['from_name'] ); ?>" /></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>

        <hr />
        <h2><?php esc_html_e( 'Send a test email', 'braftonium' ); ?></h2>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_smtp_test" />
            <?php wp_nonce_field( 'braftonium_smtp_test' ); ?>
            <input type="email" name="test_email" class="regular-text" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
            <?php submit_button( __( 'Send Test Email', 'braftonium' ), 'secondary', 'submit', false ); ?>
            <p class="description"><?php esc_html_e( 'Save your settings first, then send a test.', 'braftonium' ); ?></p>
        </form>
    </div>
    <?php
}

/* ---- Runtime: configure PHPMailer ---------------------------------------- */

add_action( 'phpmailer_init', 'braftonium_configure_phpmailer' );
function braftonium_configure_phpmailer( $phpmailer ) {
    $s = braftonium_get_smtp_settings();
    if ( empty( $s['enabled'] ) || empty( $s['host'] ) ) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $s['host'];
    $phpmailer->Port = (int) $s['port'];

    if ( 'none' === $s['encryption'] ) {
        $phpmailer->SMTPSecure  = '';
        $phpmailer->SMTPAutoTLS = false;
    } else {
        $phpmailer->SMTPSecure = $s['encryption'];
    }

    if ( ! empty( $s['auth'] ) ) {
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $s['username'];
        $phpmailer->Password = $s['password'];
    } else {
        $phpmailer->SMTPAuth = false;
    }
}

// Apply the configured From address/name to all outgoing mail.
add_filter( 'wp_mail_from', 'braftonium_smtp_mail_from' );
function braftonium_smtp_mail_from( $email ) {
    $s = braftonium_get_smtp_settings();
    if ( ! empty( $s['enabled'] ) && ! empty( $s['from_email'] ) && is_email( $s['from_email'] ) ) {
        return $s['from_email'];
    }
    return $email;
}

add_filter( 'wp_mail_from_name', 'braftonium_smtp_mail_from_name' );
function braftonium_smtp_mail_from_name( $name ) {
    $s = braftonium_get_smtp_settings();
    if ( ! empty( $s['enabled'] ) && ! empty( $s['from_name'] ) ) {
        return $s['from_name'];
    }
    return $name;
}
