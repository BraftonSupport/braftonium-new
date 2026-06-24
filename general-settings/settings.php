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
        'debug_on'          => ! empty( $_POST['debug_on'] ),
        'admin_override'    => isset( $_POST['admin_override'] ) ? sanitize_email( wp_unslash( $_POST['admin_override'] ) ) : '',
        'google_api_key'    => isset( $_POST['google_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['google_api_key'] ) ) : '',
        'fallback_image_id' => isset( $_POST['fallback_image_id'] ) ? absint( $_POST['fallback_image_id'] ) : 0,
        'revisions_enabled' => ! empty( $_POST['revisions_enabled'] ),
    );

    // Feature toggles: a checkbox only posts when checked, so an unchecked box
    // becomes "disabled" for that feature.
    $posted_features = isset( $_POST['features'] ) ? (array) wp_unslash( $_POST['features'] ) : array();
    $features        = array();
    foreach ( array_keys( braftonium_feature_registry() ) as $feature_key ) {
        $features[ $feature_key ] = ! empty( $posted_features[ $feature_key ] );
    }
    $settings['features'] = $features;

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

    $settings          = get_option( 'braftonium_general_settings', array() );
    $debug_on          = ! empty( $settings['debug_on'] );
    $admin_override    = isset( $settings['admin_override'] ) ? $settings['admin_override'] : '';
    $google_api_key    = isset( $settings['google_api_key'] ) ? $settings['google_api_key'] : '';
    $fallback_image_id = isset( $settings['fallback_image_id'] ) ? absint( $settings['fallback_image_id'] ) : 0;
    $fallback_image_url = $fallback_image_id ? wp_get_attachment_image_url( $fallback_image_id, 'medium' ) : '';
    // Default ON when never saved.
    $revisions_enabled = ! array_key_exists( 'revisions_enabled', $settings ) || ! empty( $settings['revisions_enabled'] );
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
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Revisions', 'braftonium' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="revisions_enabled" value="1" <?php checked( $revisions_enabled ); ?> />
                                <?php esc_html_e( 'Enable post revisions for posts, pages and all custom post types.', 'braftonium' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Default Featured Image', 'braftonium' ); ?></th>
                        <td>
                            <input type="hidden" id="braftonium-fallback-image-id" name="fallback_image_id" value="<?php echo esc_attr( (string) $fallback_image_id ); ?>" />
                            <div id="braftonium-fallback-image-preview" style="margin-bottom:8px;">
                                <?php if ( $fallback_image_url ) : ?>
                                    <img src="<?php echo esc_url( $fallback_image_url ); ?>" alt="" style="max-width:200px;height:auto;display:block;border:1px solid #ddd;" />
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button" id="braftonium-fallback-image-select"><?php esc_html_e( 'Select Image', 'braftonium' ); ?></button>
                            <button type="button" class="button" id="braftonium-fallback-image-remove" style="<?php echo $fallback_image_id ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button>
                            <p class="description"><?php esc_html_e( 'Used for posts with no featured image, on listings and single posts.', 'braftonium' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2><?php esc_html_e( 'Features', 'braftonium' ); ?></h2>
            <p><?php esc_html_e( 'Turn whole plugin features on or off. Disabled features stop loading entirely (including their admin pages).', 'braftonium' ); ?></p>
            <table class="form-table" role="presentation">
                <tbody>
                    <?php foreach ( braftonium_feature_registry() as $feature_key => $feature ) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html( $feature['label'] ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="features[<?php echo esc_attr( $feature_key ); ?>]" value="1" <?php checked( braftonium_feature_enabled( $feature_key ) ); ?> />
                                    <?php echo esc_html( $feature['description'] ); ?>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    (function () {
        var frame;
        var idField = document.getElementById('braftonium-fallback-image-id');
        var preview = document.getElementById('braftonium-fallback-image-preview');
        var selectBtn = document.getElementById('braftonium-fallback-image-select');
        var removeBtn = document.getElementById('braftonium-fallback-image-remove');
        if (!selectBtn) return;

        selectBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: '<?php echo esc_js( __( 'Select Default Featured Image', 'braftonium' ) ); ?>', multiple: false, library: { type: 'image' } });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                idField.value = att.id;
                var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
                preview.innerHTML = '<img src="' + url + '" alt="" style="max-width:200px;height:auto;display:block;border:1px solid #ddd;" />';
                removeBtn.style.display = '';
            });
            frame.open();
        });

        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            idField.value = '';
            preview.innerHTML = '';
            removeBtn.style.display = 'none';
        });
    })();
    </script>
    <?php
}

/**
 * Load the media library on the settings page (for the fallback image picker).
 */
add_action( 'admin_enqueue_scripts', 'braftonium_general_settings_enqueue' );
function braftonium_general_settings_enqueue( $hook ) {
    if ( 'toplevel_page_braftonium-settings' === $hook ) {
        wp_enqueue_media();
    }
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

// Feature modules, each gated by its feature flag (main settings page → Features).
/**
 * Ensure revisions are supported for posts, pages and all custom post types.
 *
 * Runs late on init (after custom post types register) so newly-registered
 * types are covered too. Defaults ON when the setting was never saved.
 */
add_action( 'init', 'braftonium_apply_revisions_support', 100 );
function braftonium_apply_revisions_support() {
    $settings = get_option( 'braftonium_general_settings', array() );
    $enabled  = ! array_key_exists( 'revisions_enabled', $settings ) || ! empty( $settings['revisions_enabled'] );
    if ( ! $enabled ) {
        return;
    }

    add_post_type_support( 'post', 'revisions' );
    add_post_type_support( 'page', 'revisions' );

    foreach ( get_post_types( array( 'public' => true ), 'names' ) as $post_type ) {
        if ( 'attachment' === $post_type ) {
            continue;
        }
        add_post_type_support( $post_type, 'revisions' );
    }
}

/**
 * Default featured image fallback.
 *
 * When a post has no real featured image, return the configured fallback
 * attachment ID for the _thumbnail_id meta. Because this populates
 * _thumbnail_id, has_post_thumbnail() / the_post_thumbnail() work everywhere
 * (archives, the blog, single posts, related-post loops, blocks) without theme
 * changes. Front-end only — never alters the editor / REST / AJAX so authors
 * still see a truly empty featured image when none is set.
 */
add_filter( 'get_post_metadata', 'braftonium_fallback_featured_image', 10, 4 );
function braftonium_fallback_featured_image( $value, $object_id, $meta_key, $single ) {
    static $running = false;

    if ( '_thumbnail_id' !== $meta_key || $running ) {
        return $value;
    }

    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return $value;
    }

    $settings = get_option( 'braftonium_general_settings', array() );
    $fallback = isset( $settings['fallback_image_id'] ) ? absint( $settings['fallback_image_id'] ) : 0;
    if ( ! $fallback ) {
        return $value;
    }

    if ( 'post' !== get_post_type( $object_id ) ) {
        return $value;
    }

    // metadata_exists() re-enters this same filter — guard against recursion.
    $running = true;
    $has_real = metadata_exists( 'post', $object_id, '_thumbnail_id' );
    $running  = false;

    if ( $has_real ) {
        return $value;
    }

    return $single ? (string) $fallback : array( $fallback );
}

// Each file is kind of self explanatory; open the specific php file for more info.
if ( braftonium_feature_enabled( 'custom_posts' ) ) {
    include __DIR__ . '/custom-posts.php';
}
if ( braftonium_feature_enabled( 'template_overrider' ) ) {
    include __DIR__ . '/template-overider.php';
}
if ( braftonium_feature_enabled( 'widgets' ) ) {
    include __DIR__ . '/manage-widgets.php';
}
if ( braftonium_feature_enabled( 'inject_scripts' ) ) {
    include __DIR__ . '/inject-scripts-styles.php';
}
if ( braftonium_feature_enabled( 'smtp' ) ) {
    include __DIR__ . '/smtp.php';
}
if ( braftonium_feature_enabled( 'dev_tools' ) ) {
    include __DIR__ . '/dev-tools.php';
}
?>