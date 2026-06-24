<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Scripts & Styles injector.
 *
 * Each rule has ONE method:
 *   - inline_js   : prints <script>…</script> in the header/footer
 *   - inline_css  : prints <style>…</style> in the header/footer
 *   - enqueue     : enqueues an external URL. URLs ending in .css load as a
 *                   stylesheet; anything else loads as a script with the chosen
 *                   load strategy (normal | async | defer).
 */

/* -------------------------------------------------------------------------
 * Constants / helpers
 * ---------------------------------------------------------------------- */

function braftonium_injection_methods() {
    return array( 'inline_js', 'inline_css', 'enqueue' );
}

function braftonium_injection_load_strategies() {
    return array( 'normal', 'async', 'defer' );
}

/**
 * Injection locations -> hooks:
 *   header     -> wp_head
 *   body_start -> wp_body_open
 *   body_end   -> wp_footer
 *   footer     -> wp_footer (legacy alias of body_end)
 */
function braftonium_injection_locations() {
    return array( 'header', 'body_start', 'body_end', 'footer' );
}

function braftonium_injection_location_labels() {
    return array(
        'header'     => __( 'Header', 'braftonium' ),
        'body_start' => __( 'Body (start)', 'braftonium' ),
        'body_end'   => __( 'Body (end)', 'braftonium' ),
        'footer'     => __( 'Footer', 'braftonium' ),
    );
}

/**
 * Normalise a stored rule to the current shape, mapping legacy method names
 * (css, js, stylesheet, js_script, js_script_async, js_script_defer) onto the
 * new method + load_strategy pair so old saved rules keep working.
 */
function braftonium_normalize_injection_rule( $rule ) {
    $rule = is_array( $rule ) ? $rule : array();

    $method = isset( $rule['inject_method'] ) ? (string) $rule['inject_method'] : '';
    $load   = isset( $rule['load_strategy'] ) ? (string) $rule['load_strategy'] : '';

    switch ( $method ) {
        case 'inline_js':
        case 'inline_css':
        case 'enqueue':
            break;
        case 'css':
            $method = 'inline_css';
            break;
        case 'js':
            $method = 'inline_js';
            break;
        case 'stylesheet':
        case 'js_script':
            $method = 'enqueue';
            $load   = $load ? $load : 'normal';
            break;
        case 'js_script_async':
            $method = 'enqueue';
            $load   = $load ? $load : 'async';
            break;
        case 'js_script_defer':
            $method = 'enqueue';
            $load   = $load ? $load : 'defer';
            break;
        default:
            $method = 'inline_css';
    }

    if ( ! in_array( $load, braftonium_injection_load_strategies(), true ) ) {
        $load = 'normal';
    }

    $location = isset( $rule['location'] ) ? (string) $rule['location'] : 'header';
    if ( ! in_array( $location, braftonium_injection_locations(), true ) ) {
        $location = 'header';
    }

    return array(
        'location'      => $location,
        'inject_method' => $method,
        'load_strategy' => $load,
        'html_disable'  => ( isset( $rule['html_disable'] ) && 'disable' === $rule['html_disable'] ) ? 'disable' : '',
        'script_id'     => isset( $rule['script_id'] ) ? (string) $rule['script_id'] : '',
        'html_value'    => isset( $rule['html_value'] ) ? (string) $rule['html_value'] : '',
        'url_value'     => isset( $rule['url_value'] ) ? (string) $rule['url_value'] : '',
    );
}

/**
 * True when a URL points at a stylesheet (path ends in .css, query ignored).
 */
function braftonium_url_is_css( $url ) {
    $path = wp_parse_url( $url, PHP_URL_PATH );
    return is_string( $path ) && preg_match( '/\.css$/i', $path );
}

/* -------------------------------------------------------------------------
 * Admin: Scripts & Styles page (global rules)
 * ---------------------------------------------------------------------- */

add_action( 'admin_menu', 'braftonium_register_injector_page' );
function braftonium_register_injector_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'Scripts & Styles', 'braftonium' ),
        __( 'Scripts & Styles', 'braftonium' ),
        'manage_options',
        'braftonium-injector',
        'braftonium_render_injector_page'
    );
}

add_action( 'admin_post_braftonium_save_injector', 'braftonium_save_injector' );
function braftonium_save_injector() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }

    check_admin_referer( 'braftonium_injector_save' );

    $rules = braftonium_sanitize_injection_rules_from_post( $_POST );

    // Inline JS/CSS is raw output; only users who can post unfiltered HTML may
    // persist inline rules.
    if ( ! current_user_can( 'unfiltered_html' ) ) {
        $rules = braftonium_strip_inline_injection_rules( $rules );
    }

    update_option( 'braftonium_injector', $rules );

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'braftonium-injector',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}

function braftonium_render_injector_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $rules = get_option( 'braftonium_injector', array() );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Scripts & Styles', 'braftonium' ); ?></h1>
        <?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_injector" />
            <?php wp_nonce_field( 'braftonium_injector_save' ); ?>

            <p><?php esc_html_e( 'Inject inline JS/CSS or enqueue an external script/stylesheet by URL.', 'braftonium' ); ?></p>
            <?php braftonium_render_injection_table( $rules, 'global' ); ?>

            <p><button type="button" class="button" data-add-injection-row="braftonium-injection-table-global"><?php esc_html_e( 'Add Rule', 'braftonium' ); ?></button></p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    braftonium_render_injection_table_script();
}

/* -------------------------------------------------------------------------
 * Admin: per-post/page meta box (local rules)
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes', 'braftonium_register_injector_metabox' );
function braftonium_register_injector_metabox() {
    foreach ( array( 'post', 'page' ) as $post_type ) {
        add_meta_box(
            'braftonium-local-injector',
            __( 'Braftonium Scripts & Styles', 'braftonium' ),
            'braftonium_render_injector_metabox',
            $post_type,
            'normal',
            'default'
        );
    }
}

function braftonium_render_injector_metabox( $post ) {
    wp_nonce_field( 'braftonium_local_injector_save', 'braftonium_local_injector_nonce' );
    $rules = get_post_meta( $post->ID, '_braftonium_injector', true );
    if ( ! is_array( $rules ) ) {
        $rules = array();
    }

    echo '<p>' . esc_html__( 'These rules only apply to this post/page.', 'braftonium' ) . '</p>';
    braftonium_render_injection_table( $rules, 'local' );
    echo '<p><button type="button" class="button" data-add-injection-row="braftonium-injection-table-local">' . esc_html__( 'Add Rule', 'braftonium' ) . '</button></p>';
    braftonium_render_injection_table_script();
}

add_action( 'save_post', 'braftonium_save_local_injector' );
function braftonium_save_local_injector( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['braftonium_local_injector_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['braftonium_local_injector_nonce'] ) ), 'braftonium_local_injector_save' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $rules = braftonium_sanitize_injection_rules_from_post( $_POST, true );

    if ( ! current_user_can( 'unfiltered_html' ) ) {
        $rules = braftonium_strip_inline_injection_rules( $rules );
    }

    update_post_meta( $post_id, '_braftonium_injector', $rules );
}

/**
 * Drop inline JS/CSS rules so users without unfiltered_html can't persist
 * unescaped inline markup.
 */
function braftonium_strip_inline_injection_rules( $rules ) {
    if ( ! is_array( $rules ) ) {
        return array();
    }

    return array_values( array_filter( $rules, static function ( $rule ) {
        $method = isset( $rule['inject_method'] ) ? $rule['inject_method'] : '';
        return 'inline_js' !== $method && 'inline_css' !== $method;
    } ) );
}

/* -------------------------------------------------------------------------
 * Sanitisation
 * ---------------------------------------------------------------------- */

function braftonium_sanitize_injection_rules_from_post( $source, $is_local = false ) {
    $prefix = $is_local ? 'local_' : '';

    $locations = isset( $source[ $prefix . 'location' ] ) ? (array) wp_unslash( $source[ $prefix . 'location' ] ) : array();
    $methods   = isset( $source[ $prefix . 'inject_method' ] ) ? (array) wp_unslash( $source[ $prefix . 'inject_method' ] ) : array();
    $loads     = isset( $source[ $prefix . 'load_strategy' ] ) ? (array) wp_unslash( $source[ $prefix . 'load_strategy' ] ) : array();
    $ids       = isset( $source[ $prefix . 'script_id' ] ) ? (array) wp_unslash( $source[ $prefix . 'script_id' ] ) : array();
    $urls      = isset( $source[ $prefix . 'url_value' ] ) ? (array) wp_unslash( $source[ $prefix . 'url_value' ] ) : array();
    $html      = isset( $source[ $prefix . 'html_value' ] ) ? (array) wp_unslash( $source[ $prefix . 'html_value' ] ) : array();
    $disabled  = isset( $source[ $prefix . 'html_disable' ] ) ? (array) wp_unslash( $source[ $prefix . 'html_disable' ] ) : array();

    $allowed_methods   = braftonium_injection_methods();
    $allowed_loads     = braftonium_injection_load_strategies();
    $allowed_locations = braftonium_injection_locations();

    $rules = array();
    foreach ( $methods as $index => $method ) {
        $method = sanitize_key( $method );
        if ( ! in_array( $method, $allowed_methods, true ) ) {
            continue;
        }

        $location = isset( $locations[ $index ] ) ? sanitize_key( $locations[ $index ] ) : 'header';
        if ( ! in_array( $location, $allowed_locations, true ) ) {
            $location = 'header';
        }

        $load = isset( $loads[ $index ] ) ? sanitize_key( $loads[ $index ] ) : 'normal';
        if ( ! in_array( $load, $allowed_loads, true ) ) {
            $load = 'normal';
        }

        $id = isset( $ids[ $index ] ) ? sanitize_key( $ids[ $index ] ) : '';
        if ( '' === $id ) {
            $id = 'braftonium-rule-' . $index;
        }

        $url_value  = isset( $urls[ $index ] ) ? esc_url_raw( $urls[ $index ] ) : '';
        $html_value = isset( $html[ $index ] ) ? (string) $html[ $index ] : '';

        $rules[] = array(
            'location'      => $location,
            'inject_method' => $method,
            'load_strategy' => $load,
            'html_disable'  => ! empty( $disabled[ $index ] ) ? 'disable' : '',
            'script_id'     => $id,
            'html_value'    => $html_value,
            'url_value'     => $url_value,
        );
    }

    return $rules;
}

/* -------------------------------------------------------------------------
 * Admin: rule table markup
 * ---------------------------------------------------------------------- */

function braftonium_render_injection_table( $rules, $scope = 'global' ) {
    $prefix   = 'local' === $scope ? 'local_' : '';
    $table_id = 'braftonium-injection-table-' . $scope;

    if ( empty( $rules ) ) {
        $rules = array(
            array(
                'location'      => 'header',
                'inject_method' => 'inline_css',
                'load_strategy' => 'normal',
                'html_disable'  => '',
                'script_id'     => '',
                'html_value'    => '',
                'url_value'     => '',
            ),
        );
    }
    ?>
    <table class="widefat striped braftonium-injection-table" id="<?php echo esc_attr( $table_id ); ?>">
        <thead>
            <tr>
                <th style="width:90px;"><?php esc_html_e( 'Location', 'braftonium' ); ?></th>
                <th style="width:120px;"><?php esc_html_e( 'Method', 'braftonium' ); ?></th>
                <th style="width:100px;"><?php esc_html_e( 'Load', 'braftonium' ); ?></th>
                <th style="width:140px;"><?php esc_html_e( 'ID', 'braftonium' ); ?></th>
                <th><?php esc_html_e( 'URL / Inline JS/CSS', 'braftonium' ); ?></th>
                <th style="width:60px;"><?php esc_html_e( 'Disable', 'braftonium' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $rules as $index => $rule ) {
                $rule = braftonium_normalize_injection_rule( $rule );
                braftonium_render_injection_row( $prefix, $index, $rule );
            }
            ?>
        </tbody>
    </table>
    <?php
}

/**
 * One rule row. Conditional fields (Load, URL, Inline) stay in place but their
 * inputs hide/show by method via JS so the table columns never shift.
 */
function braftonium_render_injection_row( $prefix, $index, $rule ) {
    $method = $rule['inject_method'];
    $is_enq = ( 'enqueue' === $method );
    $is_inl = ( 'inline_js' === $method || 'inline_css' === $method );
    $hide   = ' style="display:none;"';
    ?>
    <tr>
        <td>
            <select name="<?php echo esc_attr( $prefix ); ?>location[]">
                <?php foreach ( braftonium_injection_location_labels() as $loc_value => $loc_label ) : ?>
                    <option value="<?php echo esc_attr( $loc_value ); ?>" <?php selected( $rule['location'], $loc_value ); ?>><?php echo esc_html( $loc_label ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="<?php echo esc_attr( $prefix ); ?>inject_method[]" data-injection-method>
                <option value="inline_js" <?php selected( $method, 'inline_js' ); ?>><?php esc_html_e( 'Inline JS', 'braftonium' ); ?></option>
                <option value="inline_css" <?php selected( $method, 'inline_css' ); ?>><?php esc_html_e( 'Inline CSS', 'braftonium' ); ?></option>
                <option value="enqueue" <?php selected( $method, 'enqueue' ); ?>><?php esc_html_e( 'Enqueue', 'braftonium' ); ?></option>
            </select>
        </td>
        <td>
            <select name="<?php echo esc_attr( $prefix ); ?>load_strategy[]" data-field="load"<?php echo $is_enq ? '' : $hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                <option value="normal" <?php selected( $rule['load_strategy'], 'normal' ); ?>><?php esc_html_e( 'Normal', 'braftonium' ); ?></option>
                <option value="async" <?php selected( $rule['load_strategy'], 'async' ); ?>><?php esc_html_e( 'Async', 'braftonium' ); ?></option>
                <option value="defer" <?php selected( $rule['load_strategy'], 'defer' ); ?>><?php esc_html_e( 'Defer', 'braftonium' ); ?></option>
            </select>
            <span class="description" data-field="load-na"<?php echo $is_enq ? $hide : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php esc_html_e( 'N/A', 'braftonium' ); ?></span>
        </td>
        <td>
            <input type="text" style="width:130px;" name="<?php echo esc_attr( $prefix ); ?>script_id[]" value="<?php echo esc_attr( $rule['script_id'] ); ?>" placeholder="<?php esc_attr_e( 'optional', 'braftonium' ); ?>" />
        </td>
        <td>
            <input type="url" class="large-text code" name="<?php echo esc_attr( $prefix ); ?>url_value[]" value="<?php echo esc_attr( $rule['url_value'] ); ?>" placeholder="https://example.com/asset.js" data-field="url"<?php echo $is_enq ? '' : $hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
            <textarea rows="2" class="large-text code" name="<?php echo esc_attr( $prefix ); ?>html_value[]" data-field="inline"<?php echo $is_inl ? '' : $hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $rule['html_value'] ); ?></textarea>
        </td>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>html_disable[<?php echo esc_attr( (string) $index ); ?>]" value="1" <?php checked( $rule['html_disable'], 'disable' ); ?> />
        </td>
    </tr>
    <?php
}

function braftonium_render_injection_table_script() {
    static $printed = false;
    if ( $printed ) {
        return;
    }
    $printed = true;
    ?>
    <script>
    (function () {
        function rowMarkup(prefix, index) {
            return '<tr>' +
                '<td><select name="' + prefix + 'location[]"><option value="header">Header</option><option value="body_start">Body (start)</option><option value="body_end">Body (end)</option><option value="footer">Footer</option></select></td>' +
                '<td><select name="' + prefix + 'inject_method[]" data-injection-method><option value="inline_js">Inline JS</option><option value="inline_css">Inline CSS</option><option value="enqueue">Enqueue</option></select></td>' +
                '<td><select name="' + prefix + 'load_strategy[]" data-field="load" style="display:none;"><option value="normal">Normal</option><option value="async">Async</option><option value="defer">Defer</option></select><span class="description" data-field="load-na">N/A</span></td>' +
                '<td><input type="text" style="width:130px;" name="' + prefix + 'script_id[]" placeholder="optional" /></td>' +
                '<td>' +
                    '<input type="url" class="large-text code" name="' + prefix + 'url_value[]" placeholder="https://example.com/asset.js" data-field="url" style="display:none;" />' +
                    '<textarea rows="2" class="large-text code" name="' + prefix + 'html_value[]" data-field="inline"></textarea>' +
                '</td>' +
                '<td><input type="checkbox" name="' + prefix + 'html_disable[' + index + ']" value="1" /></td>' +
                '</tr>';
        }

        // Show only the fields relevant to the selected method.
        function applyVisibility(row) {
            var method = row.querySelector('[data-injection-method]');
            if (!method) return;
            var value = method.value;
            var isEnqueue = value === 'enqueue';
            var isInline = value === 'inline_js' || value === 'inline_css';

            row.querySelectorAll('[data-field="load"]').forEach(function (el) { el.style.display = isEnqueue ? '' : 'none'; });
            row.querySelectorAll('[data-field="load-na"]').forEach(function (el) { el.style.display = isEnqueue ? 'none' : ''; });
            row.querySelectorAll('[data-field="url"]').forEach(function (el) { el.style.display = isEnqueue ? '' : 'none'; });
            row.querySelectorAll('[data-field="inline"]').forEach(function (el) { el.style.display = isInline ? '' : 'none'; });
        }

        document.addEventListener('change', function (event) {
            if (event.target.matches('[data-injection-method]')) {
                applyVisibility(event.target.closest('tr'));
            }
        });

        document.addEventListener('click', function (event) {
            var btn = event.target.closest('[data-add-injection-row]');
            if (!btn) return;

            var table = document.getElementById(btn.getAttribute('data-add-injection-row'));
            if (!table) return;

            var prefix = table.id.indexOf('local') !== -1 ? 'local_' : '';
            var rowCount = table.querySelectorAll('tbody tr').length;
            var tmp = document.createElement('tbody');
            tmp.innerHTML = rowMarkup(prefix, rowCount);
            var row = tmp.firstChild;
            table.querySelector('tbody').appendChild(row);
            applyVisibility(row);
        });

        // Initial state for server-rendered rows.
        document.querySelectorAll('.braftonium-injection-table tbody tr').forEach(applyVisibility);
    })();
    </script>
    <?php
}

/* -------------------------------------------------------------------------
 * Front end output
 * ---------------------------------------------------------------------- */

/**
 * Build the combined (global + local) rule list, normalised.
 */
function injectionsList() {
    $injections = get_option( 'braftonium_injector', array() );
    if ( ! is_array( $injections ) ) {
        $injections = array();
    }

    if ( is_singular() ) {
        $post_id = get_queried_object_id();
        if ( $post_id ) {
            $local_injections = get_post_meta( $post_id, '_braftonium_injector', true );
            if ( is_array( $local_injections ) && ! empty( $local_injections ) ) {
                $injections = array_merge( $injections, $local_injections );
            }
        }
    }

    return array_map( 'braftonium_normalize_injection_rule', $injections );
}

function braftonium_enqueuer() {
    foreach ( injectionsList() as $rule ) {
        if ( 'disable' === $rule['html_disable'] || 'enqueue' !== $rule['inject_method'] ) {
            continue;
        }

        $id  = $rule['script_id'];
        $url = $rule['url_value'];
        if ( '' === $id || '' === $url ) {
            continue;
        }

        if ( braftonium_url_is_css( $url ) ) {
            wp_enqueue_style( $id, $url, array(), null );
            continue;
        }

        $in_footer = in_array( $rule['location'], array( 'footer', 'body_end' ), true );
        wp_enqueue_script( $id, $url, array(), null, $in_footer );

        if ( 'defer' === $rule['load_strategy'] ) {
            wp_script_add_data( $id, 'defer', true );
        } elseif ( 'async' === $rule['load_strategy'] ) {
            wp_script_add_data( $id, 'async', true );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'braftonium_enqueuer' );

add_action( 'wp_head', 'braftonium_header_injections' );
function braftonium_header_injections() {
    braftonium_output_inline_injections( array( 'header' ) );
}

add_action( 'wp_body_open', 'braftonium_body_start_injections' );
function braftonium_body_start_injections() {
    braftonium_output_inline_injections( array( 'body_start' ) );
}

add_action( 'wp_footer', 'braftonium_footer_injections' );
function braftonium_footer_injections() {
    // "Footer" is the legacy alias of "Body (end)" — both fire just before </body>.
    braftonium_output_inline_injections( array( 'body_end', 'footer' ) );
}

/**
 * Echo inline JS/CSS rules whose location is in $locations.
 */
function braftonium_output_inline_injections( $locations ) {
    foreach ( injectionsList() as $rule ) {
        if ( 'disable' === $rule['html_disable'] || ! in_array( $rule['location'], $locations, true ) ) {
            continue;
        }

        $id      = $rule['script_id'];
        $method  = $rule['inject_method'];
        $content = $rule['html_value'];

        if ( 'inline_css' === $method ) {
            echo '<style id="' . esc_attr( $id ) . '">' . $content . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } elseif ( 'inline_js' === $method ) {
            echo '<script id="' . esc_attr( $id ) . '">' . $content . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
