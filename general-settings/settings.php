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
        'admin_override'    => isset( $_POST['admin_override'] ) ? sanitize_email( wp_unslash( $_POST['admin_override'] ) ) : '',
        'google_api_key'    => isset( $_POST['google_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['google_api_key'] ) ) : '',
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
    $settings['microstyles'] = braftonium_sanitize_microstyles_from_post( $_POST );

    update_option( 'braftonium_general_settings', $settings );
    braftonium_legacy_acf_update_option( 'admin-override', $settings['admin_override'] );
    braftonium_legacy_acf_update_option( 'google-api-key', $settings['google_api_key'] );

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

function braftonium_sanitize_microstyles_from_post( $source ) {
    $labels      = isset( $source['microstyle_label'] ) ? (array) wp_unslash( $source['microstyle_label'] ) : array();
    $classes     = isset( $source['microstyle_class'] ) ? (array) wp_unslash( $source['microstyle_class'] ) : array();
    $block_types = isset( $source['microstyle_blocks'] ) ? (array) wp_unslash( $source['microstyle_blocks'] ) : array();
    $css_values  = isset( $source['microstyle_css'] ) ? (array) wp_unslash( $source['microstyle_css'] ) : array();
    $enabled     = isset( $source['microstyle_enabled'] ) ? (array) wp_unslash( $source['microstyle_enabled'] ) : array();

    $microstyles = array();
    foreach ( $labels as $index => $label ) {
        $label = sanitize_text_field( $label );
        $class = isset( $classes[ $index ] ) ? sanitize_html_class( $classes[ $index ] ) : '';

        if ( '' === $label || '' === $class ) {
            continue;
        }

        $blocks = array();
        $raw_blocks = isset( $block_types[ $index ] ) ? $block_types[ $index ] : array();
        if ( is_string( $raw_blocks ) ) {
            // Backward compatibility for the previous comma-separated input.
            $raw_blocks = explode( ',', $raw_blocks );
        }
        $raw_blocks = (array) $raw_blocks;

        if ( in_array( '__all', $raw_blocks, true ) ) {
            $raw_blocks = array();
        }

        $registered_blocks = array_keys( braftonium_microstyle_block_type_options() );
        foreach ( $raw_blocks as $block_type ) {
            $block_type = trim( sanitize_text_field( $block_type ) );
            if (
                '' !== $block_type
                && preg_match( '#^[a-z0-9-]+/[a-z0-9-]+$#', $block_type )
                && in_array( $block_type, $registered_blocks, true )
            ) {
                $blocks[] = $block_type;
            }
        }

        $css = isset( $css_values[ $index ] ) ? trim( (string) $css_values[ $index ] ) : '';
        $css = str_replace( array( '{', '}' ), '', $css );
        if ( ! current_user_can( 'unfiltered_html' ) ) {
            $css = '';
        }

        $microstyles[] = array(
            'label'       => $label,
            'value'       => $class,
            'block_types' => array_values( array_unique( $blocks ) ),
            'css'         => $css,
            'enabled'     => ! empty( $enabled[ $index ] ),
        );
    }

    return $microstyles;
}

function braftonium_get_microstyles() {
    $settings = braftonium_get_general_settings();
    $styles   = isset( $settings['microstyles'] ) && is_array( $settings['microstyles'] ) ? $settings['microstyles'] : array();

    return array_values( array_filter( $styles, 'is_array' ) );
}

function braftonium_microstyle_block_type_options() {
    if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
        return array();
    }

    $registry = WP_Block_Type_Registry::get_instance();
    $options  = array();

    foreach ( $registry->get_all_registered() as $name => $block_type ) {
        if ( 'core/missing' === $name ) {
            continue;
        }

        $title = isset( $block_type->title ) && '' !== $block_type->title ? $block_type->title : $name;
        $options[ $name ] = sprintf( '%s (%s)', $title, $name );
    }

    asort( $options, SORT_NATURAL | SORT_FLAG_CASE );

    return $options;
}

function braftonium_render_microstyle_block_type_select( $index, $selected ) {
    $selected = is_array( $selected ) ? $selected : array();
    $is_all   = empty( $selected );
    ?>
    <select class="large-text braftonium-microstyle-block-select" name="microstyle_blocks[<?php echo esc_attr( (string) $index ); ?>][]" multiple size="5">
        <option value="__all" data-all-option="1" <?php selected( $is_all ); ?>><?php esc_html_e( 'All blocks', 'braftonium' ); ?></option>
        <?php foreach ( braftonium_microstyle_block_type_options() as $block_name => $block_label ) : ?>
            <option value="<?php echo esc_attr( $block_name ); ?>" <?php selected( in_array( $block_name, $selected, true ) ); ?>><?php echo esc_html( $block_label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

add_filter( 'braftonium_class_list', 'braftonium_add_managed_microstyles', 20, 2 );
function braftonium_add_managed_microstyles( $class_list, $block_type ) {
    if ( ! is_array( $class_list ) ) {
        $class_list = array();
    }

    foreach ( braftonium_get_microstyles() as $style ) {
        if ( empty( $style['enabled'] ) || empty( $style['label'] ) || empty( $style['value'] ) ) {
            continue;
        }

        $block_types = isset( $style['block_types'] ) && is_array( $style['block_types'] ) ? $style['block_types'] : array();
        if ( ! empty( $block_types ) && ! in_array( $block_type, $block_types, true ) ) {
            continue;
        }

        $class_list[] = array(
            'label' => $style['label'],
            'value' => $style['value'],
        );
    }

    return $class_list;
}

function braftonium_microstyles_css() {
    $css = '';
    foreach ( braftonium_get_microstyles() as $style ) {
        if ( empty( $style['enabled'] ) || empty( $style['value'] ) || empty( $style['css'] ) ) {
            continue;
        }

        $css .= '.' . sanitize_html_class( $style['value'] ) . '{' . $style['css'] . '}';
    }

    return $css;
}

add_action( 'wp_enqueue_scripts', 'braftonium_enqueue_microstyles_css' );
add_action( 'enqueue_block_editor_assets', 'braftonium_enqueue_microstyles_css' );
function braftonium_enqueue_microstyles_css() {
    $css = braftonium_microstyles_css();
    if ( '' === $css ) {
        return;
    }

    wp_register_style( 'braftonium-managed-microstyles', false, array(), '1.0' );
    wp_enqueue_style( 'braftonium-managed-microstyles' );
    wp_add_inline_style( 'braftonium-managed-microstyles', $css );
}

/**
 * Render the General Settings admin page.
 */
function braftonium_render_general_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings          = braftonium_get_general_settings();
    $admin_override    = isset( $settings['admin_override'] ) ? $settings['admin_override'] : '';
    $google_api_key    = isset( $settings['google_api_key'] ) ? $settings['google_api_key'] : '';
    $microstyles       = braftonium_get_microstyles();
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
                            <p class="description"><?php esc_html_e( 'Used by legacy ACF Google Map blocks.', 'braftonium' ); ?></p>
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

            <h2><?php esc_html_e( 'MicroStyles', 'braftonium' ); ?></h2>
            <p><?php esc_html_e( 'Create class options that appear in the Braftonium Microstyles control in the block editor.', 'braftonium' ); ?></p>
            <table class="widefat striped" id="braftonium-microstyles-table">
                <thead>
                    <tr>
                        <th style="width:18%;"><?php esc_html_e( 'Label', 'braftonium' ); ?></th>
                        <th style="width:16%;"><?php esc_html_e( 'Class', 'braftonium' ); ?></th>
                        <th style="width:24%;"><?php esc_html_e( 'Block Types', 'braftonium' ); ?></th>
                        <th><?php esc_html_e( 'CSS Declarations', 'braftonium' ); ?></th>
                        <th style="width:80px;"><?php esc_html_e( 'Enabled', 'braftonium' ); ?></th>
                        <th style="width:80px;"><?php esc_html_e( 'Actions', 'braftonium' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( empty( $microstyles ) ) {
                        $microstyles = array(
                            array(
                                'label'       => '',
                                'value'       => '',
                                'block_types' => array(),
                                'css'         => '',
                                'enabled'     => true,
                            ),
                        );
                    }
                    foreach ( $microstyles as $index => $style ) :
                        $blocks = isset( $style['block_types'] ) && is_array( $style['block_types'] ) ? $style['block_types'] : array();
                        ?>
                        <tr>
                            <td><input type="text" class="regular-text" name="microstyle_label[<?php echo esc_attr( (string) $index ); ?>]" value="<?php echo esc_attr( $style['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Card Shadow', 'braftonium' ); ?>" /></td>
                            <td><input type="text" class="regular-text" name="microstyle_class[<?php echo esc_attr( (string) $index ); ?>]" value="<?php echo esc_attr( $style['value'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'card-shadow', 'braftonium' ); ?>" /></td>
                            <td><?php braftonium_render_microstyle_block_type_select( $index, $blocks ); ?></td>
                            <td><textarea rows="2" class="large-text code" name="microstyle_css[<?php echo esc_attr( (string) $index ); ?>]" placeholder="<?php esc_attr_e( 'box-shadow: 0 8px 24px rgba(0,0,0,.16);', 'braftonium' ); ?>"><?php echo esc_textarea( $style['css'] ?? '' ); ?></textarea></td>
                            <td><input type="checkbox" name="microstyle_enabled[<?php echo esc_attr( (string) $index ); ?>]" value="1" <?php checked( ! empty( $style['enabled'] ) ); ?> /></td>
                            <td><button type="button" class="button braftonium-remove-microstyle"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="description"><?php esc_html_e( 'Choose All to show the MicroStyle on every block, or select one or more registered block types. CSS should be declarations only, without a selector or braces.', 'braftonium' ); ?></p>
            <p><button type="button" class="button" id="braftonium-add-microstyle"><?php esc_html_e( 'Add MicroStyle', 'braftonium' ); ?></button></p>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    (function () {
        var table = document.getElementById('braftonium-microstyles-table');
        var addButton = document.getElementById('braftonium-add-microstyle');
        var blockTypeOptions = <?php echo wp_json_encode( braftonium_microstyle_block_type_options() ); ?>;
        if (!table || !addButton) return;

        function blockTypeSelectMarkup(index) {
            var html = '<select class="large-text braftonium-microstyle-block-select" name="microstyle_blocks[' + index + '][]" multiple size="5">';
            html += '<option value="__all" data-all-option="1" selected><?php echo esc_js( __( 'All blocks', 'braftonium' ) ); ?></option>';
            Object.keys(blockTypeOptions).forEach(function (blockName) {
                html += '<option value="' + escapeHtml(blockName) + '">' + escapeHtml(blockTypeOptions[blockName]) + '</option>';
            });
            html += '</select>';
            return html;
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function rowMarkup(index) {
            return '<tr>' +
                '<td><input type="text" class="regular-text" name="microstyle_label[' + index + ']" placeholder="<?php echo esc_js( __( 'Card Shadow', 'braftonium' ) ); ?>" /></td>' +
                '<td><input type="text" class="regular-text" name="microstyle_class[' + index + ']" placeholder="<?php echo esc_js( __( 'card-shadow', 'braftonium' ) ); ?>" /></td>' +
                '<td>' + blockTypeSelectMarkup(index) + '</td>' +
                '<td><textarea rows="2" class="large-text code" name="microstyle_css[' + index + ']" placeholder="<?php echo esc_js( __( 'box-shadow: 0 8px 24px rgba(0,0,0,.16);', 'braftonium' ) ); ?>"></textarea></td>' +
                '<td><input type="checkbox" name="microstyle_enabled[' + index + ']" value="1" checked /></td>' +
                '<td><button type="button" class="button braftonium-remove-microstyle"><?php echo esc_js( __( 'Remove', 'braftonium' ) ); ?></button></td>' +
                '</tr>';
        }

        addButton.addEventListener('click', function () {
            var index = Date.now();
            var tmp = document.createElement('tbody');
            tmp.innerHTML = rowMarkup(index);
            table.querySelector('tbody').appendChild(tmp.firstChild);
        });

        table.addEventListener('click', function (event) {
            if (!event.target.classList.contains('braftonium-remove-microstyle')) return;
            var row = event.target.closest('tr');
            if (row) row.parentNode.removeChild(row);
        });

        table.addEventListener('mousedown', function (event) {
            if (!event.target.matches('.braftonium-microstyle-block-select option')) return;
            event.target.parentNode.dataset.clickedValue = event.target.value;
        });

        table.addEventListener('change', function (event) {
            if (!event.target.classList.contains('braftonium-microstyle-block-select')) return;

            var select = event.target;
            var selected = Array.from(select.selectedOptions).map(function (option) { return option.value; });
            if (selected.indexOf('__all') === -1 || selected.length === 1) return;

            var clickedValue = select.dataset.clickedValue || '';
            var keepAll = clickedValue === '__all';

            Array.from(select.options).forEach(function (option) {
                option.selected = keepAll ? option.value === '__all' : option.value !== '__all' && option.selected;
            });
        });
    })();
    </script>
    <?php
}

/**
 * Apply runtime behavior from saved settings.
 */
add_action( 'init', 'braftonium_apply_runtime_general_settings', 1 );
function braftonium_apply_runtime_general_settings() {
    $settings = braftonium_get_general_settings();

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
    $settings = braftonium_get_general_settings();
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

// Each file is kind of self explanatory; open the specific php file for more info.
if ( braftonium_feature_enabled( 'custom_posts' ) ) {
    include __DIR__ . '/custom-posts.php';
}
if ( braftonium_feature_enabled( 'inject_scripts' ) ) {
    include __DIR__ . '/inject-scripts-styles.php';
}
if ( braftonium_feature_enabled( 'dev_tools' ) ) {
    include __DIR__ . '/dev-tools.php';
}
?>
