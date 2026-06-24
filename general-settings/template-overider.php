<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'braftonium_register_template_swopper_page' );
function braftonium_register_template_swopper_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'Template Swopper', 'braftonium' ),
        __( 'Template Swopper', 'braftonium' ),
        'manage_options',
        'template-swopper',
        'braftonium_render_template_swopper_page'
    );
}

/**
 * Normalize a stored rule into the current shape.
 *
 * Current shape:
 *   array(
 *     'type'          => 'page'|'post'|'cpt',
 *     'target'        => 'all' | (string) post ID,
 *     'audience'      => 'current'|'admins'|'all',
 *     'audience_user' => (int) user ID (only meaningful for 'current'),
 *     'template'      => '/themes/...php',
 *     'disable'       => 'disable'|'',
 *   )
 *
 * Older rules (target string + CSV audience, no 'type') are migrated and
 * disabled so they don't fire with a mismatched target until reconfigured.
 *
 * @param array $rule Stored rule.
 * @return array Normalized rule (adds '_migrated' => true for legacy rules).
 */
function braftonium_normalize_swop_rule( $rule ) {
    if ( ! is_array( $rule ) ) {
        $rule = array();
    }

    // New-style rule.
    if ( isset( $rule['type'] ) ) {
        $type = in_array( $rule['type'], array( 'page', 'post', 'cpt' ), true ) ? $rule['type'] : 'page';

        $target = isset( $rule['target'] ) ? (string) $rule['target'] : 'all';
        if ( 'all' !== $target ) {
            $target = (string) absint( $target );
            if ( '0' === $target ) {
                $target = 'all';
            }
        }

        $audience = isset( $rule['audience'] ) && in_array( $rule['audience'], array( 'current', 'admins', 'all' ), true )
            ? $rule['audience']
            : 'all';

        return array(
            'type'          => $type,
            'target'        => $target,
            'audience'      => $audience,
            'audience_user' => isset( $rule['audience_user'] ) ? absint( $rule['audience_user'] ) : 0,
            'template'      => isset( $rule['template'] ) ? (string) $rule['template'] : '',
            'disable'       => ( isset( $rule['disable'] ) && 'disable' === $rule['disable'] ) ? 'disable' : '',
        );
    }

    // Legacy rule migration.
    $legacy_audience_raw = isset( $rule['template_override_audience'] ) ? (string) $rule['template_override_audience'] : '';
    $audience            = ( false !== stripos( $legacy_audience_raw, 'all' ) ) ? 'all' : 'current';

    return array(
        'type'          => 'page',
        'target'        => 'all',
        'audience'      => $audience,
        'audience_user' => get_current_user_id(),
        'template'      => isset( $rule['template_override_template'] ) ? (string) $rule['template_override_template'] : '',
        'disable'       => 'disable',
        '_migrated'     => true,
    );
}

/**
 * Build a list of selectable targets for each content type for the editor UI.
 *
 * @return array{page:array,post:array,cpt:array,truncated:bool}
 */
function braftonium_get_swop_targets() {
    $limit       = 500;
    $truncated   = false;
    $build_items = static function ( $post_types, $with_label ) use ( $limit, &$truncated ) {
        $items = get_posts(
            array(
                'post_type'      => $post_types,
                'post_status'    => array( 'publish', 'private', 'draft', 'pending' ),
                'posts_per_page' => $limit + 1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'suppress_filters' => true,
            )
        );

        if ( count( $items ) > $limit ) {
            $truncated = true;
            $items     = array_slice( $items, 0, $limit );
        }

        $out = array();
        foreach ( $items as $item ) {
            $entry = array(
                'id'    => $item->ID,
                'title' => $item->post_title !== '' ? $item->post_title : sprintf( '#%d', $item->ID ),
            );
            if ( $with_label ) {
                $pto            = get_post_type_object( $item->post_type );
                $entry['label'] = $pto ? $pto->labels->singular_name : $item->post_type;
            }
            $out[] = $entry;
        }
        return $out;
    };

    // All public custom (non-built-in) post types.
    $cpt_types = get_post_types(
        array(
            'public'   => true,
            '_builtin' => false,
        ),
        'names'
    );

    return array(
        'page'      => $build_items( 'page', false ),
        'post'      => $build_items( 'post', false ),
        'cpt'       => ! empty( $cpt_types ) ? $build_items( array_values( $cpt_types ), true ) : array(),
        'truncated' => $truncated,
    );
}

add_action( 'admin_post_braftonium_save_template_swopper', 'braftonium_save_template_swopper' );
function braftonium_save_template_swopper() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }

    check_admin_referer( 'braftonium_template_swopper_save' );

    $types     = isset( $_POST['template_override_type'] ) ? (array) wp_unslash( $_POST['template_override_type'] ) : array();
    $targets   = isset( $_POST['template_override_target'] ) ? (array) wp_unslash( $_POST['template_override_target'] ) : array();
    $audiences = isset( $_POST['template_override_audience'] ) ? (array) wp_unslash( $_POST['template_override_audience'] ) : array();
    $templates = isset( $_POST['template_override_template'] ) ? (array) wp_unslash( $_POST['template_override_template'] ) : array();
    $disabled  = isset( $_POST['template_override_disable'] ) ? (array) wp_unslash( $_POST['template_override_disable'] ) : array();

    $current_user_id = get_current_user_id();
    $rules           = array();

    foreach ( $types as $index => $type ) {
        $type = sanitize_key( $type );
        if ( ! in_array( $type, array( 'page', 'post', 'cpt' ), true ) ) {
            continue;
        }

        $audience = isset( $audiences[ $index ] ) ? sanitize_key( $audiences[ $index ] ) : '';
        if ( ! in_array( $audience, array( 'current', 'admins', 'all' ), true ) ) {
            continue;
        }

        $template = isset( $templates[ $index ] ) ? sanitize_text_field( $templates[ $index ] ) : '';
        if ( '' === $template ) {
            continue;
        }

        $target = isset( $targets[ $index ] ) ? sanitize_text_field( $targets[ $index ] ) : 'all';
        if ( 'all' !== $target ) {
            $target = (string) absint( $target );
            if ( '0' === $target ) {
                $target = 'all';
            }
        }

        $rules[] = array(
            'type'          => $type,
            'target'        => $target,
            'audience'      => $audience,
            'audience_user' => 'current' === $audience ? $current_user_id : 0,
            'template'      => $template,
            'disable'       => ! empty( $disabled[ $index ] ) ? 'disable' : '',
        );
    }

    update_option( 'braftonium_template_page', $rules );

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'template-swopper',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}

function braftonium_render_template_swopper_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $stored_rules = get_option( 'braftonium_template_page', array() );
    if ( ! is_array( $stored_rules ) ) {
        $stored_rules = array();
    }

    $rules        = array();
    $has_migrated = false;
    foreach ( $stored_rules as $stored_rule ) {
        $rule = braftonium_normalize_swop_rule( $stored_rule );
        if ( ! empty( $rule['_migrated'] ) ) {
            $has_migrated = true;
        }
        $rules[] = $rule;
    }

    $targets    = braftonium_get_swop_targets();
    $type_options = array(
        'page' => __( 'Page', 'braftonium' ),
        'post' => __( 'Posts', 'braftonium' ),
        'cpt'  => __( 'Custom Posts', 'braftonium' ),
    );
    $audience_options = array(
        'current' => __( 'Current user only', 'braftonium' ),
        'admins'  => __( 'Admin users', 'braftonium' ),
        'all'     => __( 'All users', 'braftonium' ),
    );
    ?>
    <div class="wrap braftonium-template-swopper">
        <h1><?php esc_html_e( 'Template Swopper', 'braftonium' ); ?></h1>
        <?php if ( isset( $_GET['updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>
        <?php if ( $has_migrated ) : ?>
            <div class="notice notice-warning"><p><?php esc_html_e( 'Some existing rules were saved in an older format. They have been disabled — please re-select their content type and target, then save.', 'braftonium' ); ?></p></div>
        <?php endif; ?>
        <?php if ( ! empty( $targets['truncated'] ) ) : ?>
            <div class="notice notice-info"><p><?php esc_html_e( 'There are more items than can be listed; only the first 500 per type are shown in the target dropdowns.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_template_swopper" />
            <?php wp_nonce_field( 'braftonium_template_swopper_save' ); ?>

            <p class="description"><?php esc_html_e( 'Choose what to override, who sees the override, and the replacement template. New Template must be a path under /wp-content (for example /themes/child/page-dev.php).', 'braftonium' ); ?></p>

            <table class="widefat striped" id="braftonium-template-swopper-table">
                <thead>
                    <tr>
                        <th style="width:14%;"><?php esc_html_e( 'Content Type', 'braftonium' ); ?></th>
                        <th style="width:26%;"><?php esc_html_e( 'Target', 'braftonium' ); ?></th>
                        <th style="width:18%;"><?php esc_html_e( 'Audience', 'braftonium' ); ?></th>
                        <th style="width:30%;"><?php esc_html_e( 'New Template', 'braftonium' ); ?></th>
                        <th style="width:6%;"><?php esc_html_e( 'Disable', 'braftonium' ); ?></th>
                        <th style="width:6%;"><?php esc_html_e( 'Actions', 'braftonium' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $render_row = static function ( $index, $rule ) use ( $type_options, $audience_options ) {
                        $type     = $rule['type'] ?? 'page';
                        $target   = $rule['target'] ?? 'all';
                        $audience = $rule['audience'] ?? 'all';
                        $template = $rule['template'] ?? '';
                        $disable  = ( $rule['disable'] ?? '' ) === 'disable';
                        ?>
                        <tr class="braftonium-swop-row" data-swop-index="<?php echo esc_attr( $index ); ?>">
                            <td>
                                <select name="template_override_type[<?php echo esc_attr( $index ); ?>]" class="braftonium-swop-type">
                                    <?php foreach ( $type_options as $value => $label ) : ?>
                                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $type, $value ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="template_override_target[<?php echo esc_attr( $index ); ?>]" class="braftonium-swop-target" data-selected="<?php echo esc_attr( $target ); ?>"></select>
                            </td>
                            <td>
                                <select name="template_override_audience[<?php echo esc_attr( $index ); ?>]" class="braftonium-swop-audience">
                                    <?php foreach ( $audience_options as $value => $label ) : ?>
                                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $audience, $value ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="template_override_template[<?php echo esc_attr( $index ); ?>]" class="regular-text" value="<?php echo esc_attr( $template ); ?>" placeholder="/themes/... or /plugins/..." /></td>
                            <td style="text-align:center;"><input type="checkbox" name="template_override_disable[<?php echo esc_attr( $index ); ?>]" value="1" <?php checked( $disable ); ?> /></td>
                            <td><button type="button" class="button braftonium-remove-row"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                        </tr>
                        <?php
                    };

                    if ( empty( $rules ) ) {
                        $render_row(
                            0,
                            array(
                                'type'     => 'page',
                                'target'   => 'all',
                                'audience' => 'current',
                                'template' => '',
                                'disable'  => '',
                            )
                        );
                    } else {
                        foreach ( $rules as $index => $rule ) {
                            $render_row( $index, $rule );
                        }
                    }
                    ?>
                </tbody>
            </table>

            <p><button type="button" class="button" id="braftonium-add-template-rule"><?php esc_html_e( 'Add Rule', 'braftonium' ); ?></button></p>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    (function () {
        var swopData = <?php echo wp_json_encode(
            array(
                'page' => $targets['page'],
                'post' => $targets['post'],
                'cpt'  => $targets['cpt'],
            )
        ); ?>;
        var allLabel = '<?php echo esc_js( __( 'All', 'braftonium' ) ); ?>';
        var table = document.getElementById('braftonium-template-swopper-table');
        var rowCounter = <?php echo (int) ( empty( $rules ) ? 1 : count( $rules ) ); ?>;

        function buildTargetOptions(select, type, selectedVal) {
            var prev = selectedVal != null ? String(selectedVal) : (select.value || 'all');
            select.innerHTML = '';
            select.add(new Option(allLabel, 'all'));
            (swopData[type] || []).forEach(function (item) {
                var text = item.label ? item.label + ' — ' + item.title : item.title;
                select.add(new Option(text, String(item.id)));
            });
            select.value = prev;
            if (select.selectedIndex < 0) {
                select.value = 'all';
            }
        }

        function initRow(row) {
            var typeSelect = row.querySelector('.braftonium-swop-type');
            var targetSelect = row.querySelector('.braftonium-swop-target');
            if (!typeSelect || !targetSelect) return;

            buildTargetOptions(targetSelect, typeSelect.value, targetSelect.getAttribute('data-selected'));

            typeSelect.addEventListener('change', function () {
                buildTargetOptions(targetSelect, typeSelect.value, 'all');
            });
        }

        function addRow() {
            var index = rowCounter++;
            var tbody = table.querySelector('tbody');
            var row = document.createElement('tr');
            row.className = 'braftonium-swop-row';
            row.setAttribute('data-swop-index', index);
            row.innerHTML =
                '<td><select name="template_override_type[' + index + ']" class="braftonium-swop-type">' +
                    '<option value="page"><?php echo esc_js( $type_options['page'] ); ?></option>' +
                    '<option value="post"><?php echo esc_js( $type_options['post'] ); ?></option>' +
                    '<option value="cpt"><?php echo esc_js( $type_options['cpt'] ); ?></option>' +
                '</select></td>' +
                '<td><select name="template_override_target[' + index + ']" class="braftonium-swop-target" data-selected="all"></select></td>' +
                '<td><select name="template_override_audience[' + index + ']" class="braftonium-swop-audience">' +
                    '<option value="current"><?php echo esc_js( $audience_options['current'] ); ?></option>' +
                    '<option value="admins"><?php echo esc_js( $audience_options['admins'] ); ?></option>' +
                    '<option value="all"><?php echo esc_js( $audience_options['all'] ); ?></option>' +
                '</select></td>' +
                '<td><input type="text" name="template_override_template[' + index + ']" class="regular-text" placeholder="/themes/... or /plugins/..." /></td>' +
                '<td style="text-align:center;"><input type="checkbox" name="template_override_disable[' + index + ']" value="1" /></td>' +
                '<td><button type="button" class="button braftonium-remove-row"><?php echo esc_js( __( 'Remove', 'braftonium' ) ); ?></button></td>';
            tbody.appendChild(row);
            initRow(row);
        }

        table.addEventListener('click', function (e) {
            if (!e.target.classList.contains('braftonium-remove-row')) return;
            var row = e.target.closest('tr');
            if (row) row.parentNode.removeChild(row);
        });

        document.getElementById('braftonium-add-template-rule').addEventListener('click', addRow);

        // Initialize server-rendered rows.
        table.querySelectorAll('.braftonium-swop-row').forEach(initRow);
    })();
    </script>
    <?php
}

/**
 * Safely resolve a configured override template path to an absolute path.
 *
 * The configured value is treated as relative to WP_CONTENT_DIR. The
 * resolved path must (1) exist, (2) be a .php file, and (3) stay inside
 * WP_CONTENT_DIR after resolving symlinks and traversal sequences. This
 * prevents path traversal / arbitrary file inclusion (e.g. "../../wp-config.php").
 *
 * @param string $relative_path Path relative to wp-content (may start with /).
 * @return string Absolute path on success, or '' if invalid.
 */
function braftonium_resolve_override_template( $relative_path ) {
    $relative_path = (string) $relative_path;
    if ( '' === trim( $relative_path ) ) {
        return '';
    }

    // Only allow .php template files.
    if ( '.php' !== strtolower( substr( $relative_path, -4 ) ) ) {
        return '';
    }

    $content_dir = realpath( WP_CONTENT_DIR );
    if ( false === $content_dir ) {
        return '';
    }

    $candidate = $content_dir . '/' . ltrim( $relative_path, '/\\' );
    $resolved  = realpath( $candidate );

    if ( false === $resolved || ! is_file( $resolved ) ) {
        return '';
    }

    // Ensure the resolved file is contained within wp-content.
    $content_dir_prefix = rtrim( $content_dir, '/\\' ) . DIRECTORY_SEPARATOR;
    if ( 0 !== strpos( $resolved, $content_dir_prefix ) ) {
        return '';
    }

    return $resolved;
}

/**
 * Determine whether the current viewer is in the rule's audience.
 *
 * @param array $rule Normalized rule.
 * @return bool
 */
function braftonium_swop_audience_matches( $rule ) {
    $audience = $rule['audience'] ?? 'all';

    switch ( $audience ) {
        case 'all':
            return true;
        case 'admins':
            return current_user_can( 'manage_options' );
        case 'current':
            return is_user_logged_in() && (int) ( $rule['audience_user'] ?? 0 ) === get_current_user_id();
    }

    return false;
}

// A swopped template will have the same classes as the original template, plus dev-template.
function getOverrideOptions( $template ) {
    $stored_rules = get_option( 'braftonium_template_page', array() );
    if ( ! is_array( $stored_rules ) || empty( $stored_rules ) ) {
        return $template;
    }

    // Resolve the current request's content type and post ID.
    $current_type = '';
    $current_id   = 0;
    $queried      = get_queried_object();

    if ( $queried instanceof WP_Post && is_singular() ) {
        $current_id = (int) $queried->ID;
        $post_type  = $queried->post_type;

        if ( 'page' === $post_type ) {
            $current_type = 'page';
        } elseif ( 'post' === $post_type ) {
            $current_type = 'post';
        } else {
            $pto = get_post_type_object( $post_type );
            if ( $pto && empty( $pto->_builtin ) && ! empty( $pto->public ) ) {
                $current_type = 'cpt';
            }
        }
    }

    if ( '' === $current_type ) {
        return $template;
    }

    foreach ( $stored_rules as $stored_rule ) {
        $rule = braftonium_normalize_swop_rule( $stored_rule );

        if ( 'disable' === $rule['disable'] ) {
            continue;
        }

        if ( $rule['type'] !== $current_type ) {
            continue;
        }

        if ( 'all' !== $rule['target'] && (int) $rule['target'] !== $current_id ) {
            continue;
        }

        if ( ! braftonium_swop_audience_matches( $rule ) ) {
            continue;
        }

        $new_template_abs = braftonium_resolve_override_template( $rule['template'] );
        if ( '' === $new_template_abs ) {
            continue;
        }

        add_filter(
            'body_class',
            static function ( $classes ) {
                $classes[] = 'dev-template';
                return $classes;
            }
        );

        return $new_template_abs;
    }

    return $template;
}
add_filter( 'template_include', 'getOverrideOptions', 1001 );
?>
