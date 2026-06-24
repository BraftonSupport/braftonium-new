<?php
// Note - Everything here will be in the backend "Braftonium" options pages.

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register top-level Braftonium menu and General Settings page.
 */
add_action( 'admin_menu', 'braftonium_register_general_settings_page' );
function braftonium_register_general_settings_page() {
    add_menu_page(
        __( 'General Settings', 'braftonium' ),
        __( 'Braftonium', 'braftonium' ),
        'manage_options',
        'braftonium-settings',
        'braftonium_render_general_settings_page',
        'dashicons-admin-generic',
        62
    );
}

/**
 * Save General Settings values.
 */
add_action( 'admin_post_braftonium_save_general_settings', 'braftonium_save_general_settings' );
function braftonium_save_general_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }

    check_admin_referer( 'braftonium_general_settings_save' );

    $settings = array(
        'debug_on'       => ! empty( $_POST['debug_on'] ),
        'admin_override' => isset( $_POST['admin_override'] ) ? sanitize_email( wp_unslash( $_POST['admin_override'] ) ) : '',
        'google_api_key' => isset( $_POST['google_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['google_api_key'] ) ) : '',
    );

    update_option( 'braftonium_general_settings', $settings );

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'braftonium-settings',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}

/**
 * Render the General Settings admin page.
 */
function braftonium_render_general_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings       = get_option( 'braftonium_general_settings', array() );
    $debug_on       = ! empty( $settings['debug_on'] );
    $admin_override = isset( $settings['admin_override'] ) ? $settings['admin_override'] : '';
    $google_api_key = isset( $settings['google_api_key'] ) ? $settings['google_api_key'] : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Braftonium Settings', 'braftonium' ); ?></h1>
        <?php if ( isset( $_GET['updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_general_settings" />
            <?php wp_nonce_field( 'braftonium_general_settings_save' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Turn Debug On', 'braftonium' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="debug_on" value="1" <?php checked( $debug_on ); ?> />
                                <?php esc_html_e( 'Enable debug for administrators only.', 'braftonium' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-admin-override"><?php esc_html_e( 'Admin Override', 'braftonium' ); ?></label></th>
                        <td>
                            <input
                                type="email"
                                id="braftonium-admin-override"
                                name="admin_override"
                                class="regular-text"
                                value="<?php echo esc_attr( $admin_override ); ?>"
                            />
                            <p class="description"><?php esc_html_e( 'Set the main administrator email without confirmation.', 'braftonium' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="braftonium-google-api-key"><?php esc_html_e( 'Google API Key', 'braftonium' ); ?></label></th>
                        <td>
                            <input
                                type="text"
                                id="braftonium-google-api-key"
                                name="google_api_key"
                                class="regular-text"
                                value="<?php echo esc_attr( $google_api_key ); ?>"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Apply runtime behavior from saved settings.
 */
add_action( 'init', 'braftonium_apply_runtime_general_settings', 1 );
function braftonium_apply_runtime_general_settings() {
    $settings = get_option( 'braftonium_general_settings', array() );

    if ( ! empty( $settings['debug_on'] ) && current_user_can( 'manage_options' ) ) {
        error_reporting( E_ALL );
        ini_set( 'display_errors', 1 );
    }

    if ( ! empty( $settings['admin_override'] ) && is_email( $settings['admin_override'] ) ) {
        $current_admin_email = get_option( 'admin_email' );
        $override_email      = $settings['admin_override'];

        if ( $current_admin_email !== $override_email ) {
            update_option( 'admin_email', $override_email );
            update_option( 'new_admin_email', $override_email );
        }
    }
}

// Each file is kind of self explanatory; open the specific php file for more info.
include __DIR__ . '/custom-posts.php';
include __DIR__ . '/template-overider.php';
include __DIR__ . '/manage-widgets.php';
include __DIR__ . '/inject-scripts-styles.php';
?>