<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register submenu page for custom posts and taxonomies.
 */
add_action( 'admin_menu', 'braftonium_register_custom_posts_page' );
function braftonium_register_custom_posts_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'Custom Posts & Taxonomies', 'braftonium' ),
        __( 'Custom Posts & Taxonomies', 'braftonium' ),
        'manage_options',
        'custom-posts',
        'braftonium_render_custom_posts_page'
    );
}

/**
 * Normalize stored taxonomies into a list of array( 'name' => ..., 'slug' => ... ).
 *
 * Older versions stored taxonomies as a flat list of slug strings; convert those
 * on the fly so existing configurations keep working.
 *
 * @param mixed $raw Stored taxonomies option value.
 * @return array<int, array{name:string, slug:string}>
 */
function braftonium_normalize_taxonomies( $raw ) {
    $taxonomies = array();

    if ( ! is_array( $raw ) ) {
        return $taxonomies;
    }

    foreach ( $raw as $item ) {
        if ( is_array( $item ) ) {
            $slug = isset( $item['slug'] ) ? sanitize_key( $item['slug'] ) : '';
            $name = isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : '';
        } else {
            // Legacy format: a bare slug string.
            $slug = sanitize_key( (string) $item );
            $name = '';
        }

        if ( '' === $slug ) {
            continue;
        }

        if ( '' === $name ) {
            $name = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
        }

        $taxonomies[] = array(
            'name' => $name,
            'slug' => $slug,
        );
    }

    return $taxonomies;
}

add_action( 'admin_post_braftonium_save_custom_posts', 'braftonium_save_custom_posts' );
function braftonium_save_custom_posts() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }

    check_admin_referer( 'braftonium_custom_posts_save' );

    // ----- Taxonomies (name + slug rows) -----
    $custom_taxonomies = array();
    $tax_names         = isset( $_POST['tax_name'] ) ? (array) wp_unslash( $_POST['tax_name'] ) : array();
    $tax_slugs         = isset( $_POST['tax_slug'] ) ? (array) wp_unslash( $_POST['tax_slug'] ) : array();
    $seen_tax_slugs    = array();

    foreach ( $tax_slugs as $index => $raw_slug ) {
        $slug = sanitize_key( trim( (string) $raw_slug ) );
        $name = isset( $tax_names[ $index ] ) ? sanitize_text_field( $tax_names[ $index ] ) : '';

        // Fall back to deriving a slug from the name if only a name was entered.
        if ( '' === $slug && '' !== $name ) {
            $slug = sanitize_key( str_replace( ' ', '_', $name ) );
        }

        if ( '' === $slug ) {
            continue;
        }

        // Categories and Tags are always available as built-ins; don't duplicate them.
        if ( in_array( $slug, array( 'category', 'post_tag' ), true ) ) {
            continue;
        }

        if ( isset( $seen_tax_slugs[ $slug ] ) ) {
            continue;
        }
        $seen_tax_slugs[ $slug ] = true;

        if ( '' === $name ) {
            $name = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
        }

        $custom_taxonomies[] = array(
            'name' => $name,
            'slug' => $slug,
        );
    }

    // ----- Custom post types (title + selected taxonomy slugs) -----
    $custom_post_types  = array();
    $titles             = isset( $_POST['post_type_title'] ) ? (array) wp_unslash( $_POST['post_type_title'] ) : array();
    $selected_taxonomies = isset( $_POST['post_type_options'] ) ? (array) wp_unslash( $_POST['post_type_options'] ) : array();

    foreach ( $titles as $index => $title ) {
        $clean_title = sanitize_text_field( $title );
        if ( '' === $clean_title ) {
            continue;
        }

        $options     = array();
        $options_raw = isset( $selected_taxonomies[ $index ] ) ? (array) $selected_taxonomies[ $index ] : array();

        foreach ( $options_raw as $taxonomy ) {
            $taxonomy = sanitize_key( trim( (string) $taxonomy ) );
            if ( '' !== $taxonomy ) {
                $options[] = $taxonomy;
            }
        }
        $options = array_values( array_unique( $options ) );

        $custom_post_types[] = array(
            'post_type_title'   => $clean_title,
            'post_type_options' => $options,
        );
    }

    update_option( 'braftonium_custom_post_types_taxonomies', $custom_taxonomies );
    update_option( 'braftonium_custom_post_types_new', $custom_post_types );

    // Flush only when configuration changed.
    update_option( 'braftonium_custom_posts_needs_flush', 1 );

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'custom-posts',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}

function braftonium_render_custom_posts_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $custom_taxonomies = braftonium_normalize_taxonomies( get_option( 'braftonium_custom_post_types_taxonomies', array() ) );
    $custom_post_types = get_option( 'braftonium_custom_post_types_new', array() );
    if ( ! is_array( $custom_post_types ) ) {
        $custom_post_types = array();
    }

    // Built-in taxonomies that are always available to attach.
    $builtin_taxonomies = array(
        array(
            'name' => __( 'Categories', 'braftonium' ),
            'slug' => 'category',
        ),
        array(
            'name' => __( 'Tags', 'braftonium' ),
            'slug' => 'post_tag',
        ),
    );
    ?>
    <div class="wrap braftonium-custom-posts">
        <h1><?php esc_html_e( 'Custom Posts & Taxonomies', 'braftonium' ); ?></h1>
        <?php if ( isset( $_GET['updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_custom_posts" />
            <?php wp_nonce_field( 'braftonium_custom_posts_save' ); ?>

            <h2><?php esc_html_e( 'Taxonomies', 'braftonium' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Add a taxonomy by giving it a name and a slug. The slug is auto-filled from the name but can be edited. Categories and Tags are always available.', 'braftonium' ); ?></p>

            <table class="widefat striped braftonium-tax-table" id="braftonium-tax-table">
                <thead>
                    <tr>
                        <th style="width:40%;"><?php esc_html_e( 'Taxonomy Name', 'braftonium' ); ?></th>
                        <th style="width:40%;"><?php esc_html_e( 'Taxonomy Slug', 'braftonium' ); ?></th>
                        <th style="width:20%;"><?php esc_html_e( 'Actions', 'braftonium' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $custom_taxonomies ) ) : ?>
                        <tr class="braftonium-tax-row">
                            <td><input type="text" name="tax_name[]" class="regular-text braftonium-tax-name" placeholder="<?php esc_attr_e( 'e.g. Genre', 'braftonium' ); ?>" /></td>
                            <td><input type="text" name="tax_slug[]" class="regular-text braftonium-tax-slug" placeholder="<?php esc_attr_e( 'e.g. genre', 'braftonium' ); ?>" /></td>
                            <td><button type="button" class="button braftonium-remove-row"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $custom_taxonomies as $tax ) : ?>
                            <tr class="braftonium-tax-row">
                                <td><input type="text" name="tax_name[]" class="regular-text braftonium-tax-name" value="<?php echo esc_attr( $tax['name'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Genre', 'braftonium' ); ?>" /></td>
                                <td><input type="text" name="tax_slug[]" class="regular-text braftonium-tax-slug" value="<?php echo esc_attr( $tax['slug'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. genre', 'braftonium' ); ?>" /></td>
                                <td><button type="button" class="button braftonium-remove-row"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="braftonium-add-tax-row"><?php esc_html_e( 'Add Taxonomy', 'braftonium' ); ?></button></p>

            <h2><?php esc_html_e( 'Custom Post Types', 'braftonium' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Give each post type a title, then tick the taxonomies it should use. The list is built from the taxonomies above.', 'braftonium' ); ?></p>

            <table class="widefat striped braftonium-cpt-table" id="braftonium-cpt-table">
                <thead>
                    <tr>
                        <th style="width:30%;"><?php esc_html_e( 'Title', 'braftonium' ); ?></th>
                        <th style="width:55%;"><?php esc_html_e( 'Taxonomies', 'braftonium' ); ?></th>
                        <th style="width:15%;"><?php esc_html_e( 'Actions', 'braftonium' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cpt_index = 0;
                    if ( empty( $custom_post_types ) ) :
                        ?>
                        <tr class="braftonium-cpt-row" data-cpt-index="0">
                            <td><input type="text" name="post_type_title[0]" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Book', 'braftonium' ); ?>" /></td>
                            <td class="braftonium-cpt-taxonomies" data-selected="[]"></td>
                            <td><button type="button" class="button braftonium-remove-row"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $custom_post_types as $row ) : ?>
                            <?php $selected = array_values( (array) ( $row['post_type_options'] ?? array() ) ); ?>
                            <tr class="braftonium-cpt-row" data-cpt-index="<?php echo esc_attr( $cpt_index ); ?>">
                                <td><input type="text" name="post_type_title[<?php echo esc_attr( $cpt_index ); ?>]" class="regular-text" value="<?php echo esc_attr( $row['post_type_title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Book', 'braftonium' ); ?>" /></td>
                                <td class="braftonium-cpt-taxonomies" data-selected="<?php echo esc_attr( wp_json_encode( $selected ) ); ?>"></td>
                                <td><button type="button" class="button braftonium-remove-row"><?php esc_html_e( 'Remove', 'braftonium' ); ?></button></td>
                            </tr>
                            <?php ++$cpt_index; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="braftonium-add-cpt-row"><?php esc_html_e( 'Add Post Type', 'braftonium' ); ?></button></p>

            <?php submit_button(); ?>
        </form>
    </div>

    <style>
        .braftonium-custom-posts .braftonium-cpt-taxonomies label {
            display: inline-flex;
            align-items: center;
            margin: 0 14px 6px 0;
            white-space: nowrap;
        }
        .braftonium-custom-posts .braftonium-cpt-taxonomies label input {
            margin-right: 5px;
        }
        .braftonium-custom-posts .braftonium-cpt-no-tax {
            color: #777;
            font-style: italic;
        }
        .braftonium-custom-posts .braftonium-tax-builtin {
            color: #555;
        }
    </style>

    <script>
    (function () {
        var builtinTaxonomies = <?php echo wp_json_encode( $builtin_taxonomies ); ?>;
        var cptIndexCounter = <?php echo (int) ( empty( $custom_post_types ) ? 1 : $cpt_index ); ?>;

        var taxTable = document.getElementById('braftonium-tax-table');
        var cptTable = document.getElementById('braftonium-cpt-table');

        function slugify(value) {
            return value
                .toLowerCase()
                .replace(/[^a-z0-9 _-]/g, '')
                .trim()
                .replace(/[\s-]+/g, '_');
        }

        // Build the list of available taxonomies from the taxonomy table + built-ins.
        function getAvailableTaxonomies() {
            var list = builtinTaxonomies.map(function (t) {
                return { name: t.name, slug: t.slug, builtin: true };
            });
            var seen = {};
            list.forEach(function (t) { seen[t.slug] = true; });

            taxTable.querySelectorAll('.braftonium-tax-row').forEach(function (row) {
                var nameInput = row.querySelector('.braftonium-tax-name');
                var slugInput = row.querySelector('.braftonium-tax-slug');
                if (!slugInput) return;
                var slug = slugify(slugInput.value || (nameInput ? nameInput.value : ''));
                if (!slug || seen[slug]) return;
                seen[slug] = true;
                var name = nameInput && nameInput.value.trim() ? nameInput.value.trim() : slug;
                list.push({ name: name, slug: slug, builtin: false });
            });

            return list;
        }

        // Render the taxonomy checkboxes for a single CPT row, preserving selections.
        function renderCptRow(cell, available) {
            var cptIndex = cell.closest('.braftonium-cpt-row').getAttribute('data-cpt-index');

            // Determine which slugs are currently selected (DOM first, then saved data).
            var selected = {};
            var existing = cell.querySelectorAll('input[type="checkbox"]:checked');
            if (existing.length) {
                existing.forEach(function (cb) { selected[cb.value] = true; });
            } else if (!cell.getAttribute('data-rendered')) {
                try {
                    JSON.parse(cell.getAttribute('data-selected') || '[]').forEach(function (s) {
                        selected[s] = true;
                    });
                } catch (e) {}
            } else {
                existing = cell.querySelectorAll('input[type="checkbox"]');
                existing.forEach(function (cb) { if (cb.checked) selected[cb.value] = true; });
            }

            cell.innerHTML = '';
            cell.setAttribute('data-rendered', '1');

            if (!available.length) {
                var empty = document.createElement('span');
                empty.className = 'braftonium-cpt-no-tax';
                empty.textContent = '<?php echo esc_js( __( 'No taxonomies available. Add one above.', 'braftonium' ) ); ?>';
                cell.appendChild(empty);
                return;
            }

            available.forEach(function (tax) {
                var label = document.createElement('label');
                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.name = 'post_type_options[' + cptIndex + '][]';
                cb.value = tax.slug;
                if (selected[tax.slug]) cb.checked = true;
                label.appendChild(cb);

                var text = document.createElement('span');
                text.textContent = tax.name + ' (' + tax.slug + ')';
                if (tax.builtin) text.className = 'braftonium-tax-builtin';
                label.appendChild(text);

                cell.appendChild(label);
            });
        }

        function refreshAllCptRows() {
            var available = getAvailableTaxonomies();
            cptTable.querySelectorAll('.braftonium-cpt-taxonomies').forEach(function (cell) {
                renderCptRow(cell, available);
            });
        }

        // ----- Taxonomy table interactions -----
        function addTaxRow() {
            var tbody = taxTable.querySelector('tbody');
            var row = document.createElement('tr');
            row.className = 'braftonium-tax-row';
            row.innerHTML =
                '<td><input type="text" name="tax_name[]" class="regular-text braftonium-tax-name" placeholder="<?php echo esc_js( __( 'e.g. Genre', 'braftonium' ) ); ?>" /></td>' +
                '<td><input type="text" name="tax_slug[]" class="regular-text braftonium-tax-slug" placeholder="<?php echo esc_js( __( 'e.g. genre', 'braftonium' ) ); ?>" /></td>' +
                '<td><button type="button" class="button braftonium-remove-row"><?php echo esc_js( __( 'Remove', 'braftonium' ) ); ?></button></td>';
            tbody.appendChild(row);
        }

        taxTable.addEventListener('input', function (e) {
            if (e.target.classList.contains('braftonium-tax-name')) {
                // Auto-fill slug from name while the slug hasn't been hand-edited.
                var row = e.target.closest('.braftonium-tax-row');
                var slugInput = row.querySelector('.braftonium-tax-slug');
                if (slugInput && !slugInput.dataset.touched) {
                    slugInput.value = slugify(e.target.value);
                }
            }
            if (e.target.classList.contains('braftonium-tax-slug')) {
                e.target.dataset.touched = '1';
            }
            refreshAllCptRows();
        });

        // ----- CPT table interactions -----
        function addCptRow() {
            var tbody = cptTable.querySelector('tbody');
            var index = cptIndexCounter++;
            var row = document.createElement('tr');
            row.className = 'braftonium-cpt-row';
            row.setAttribute('data-cpt-index', index);
            row.innerHTML =
                '<td><input type="text" name="post_type_title[' + index + ']" class="regular-text" placeholder="<?php echo esc_js( __( 'e.g. Book', 'braftonium' ) ); ?>" /></td>' +
                '<td class="braftonium-cpt-taxonomies" data-selected="[]"></td>' +
                '<td><button type="button" class="button braftonium-remove-row"><?php echo esc_js( __( 'Remove', 'braftonium' ) ); ?></button></td>';
            tbody.appendChild(row);
            renderCptRow(row.querySelector('.braftonium-cpt-taxonomies'), getAvailableTaxonomies());
        }

        // ----- Shared row removal -----
        document.addEventListener('click', function (e) {
            if (!e.target.classList.contains('braftonium-remove-row')) return;
            var row = e.target.closest('tr');
            var inTaxTable = taxTable.contains(row);
            row.parentNode.removeChild(row);
            if (inTaxTable) refreshAllCptRows();
        });

        document.getElementById('braftonium-add-tax-row').addEventListener('click', addTaxRow);
        document.getElementById('braftonium-add-cpt-row').addEventListener('click', addCptRow);

        // Initial render.
        refreshAllCptRows();
    })();
    </script>
    <?php
}

/**
 * If any custom post type exists, loop through and register it.
 */
add_action( 'init', 'create_custom_post_types' );
function create_custom_post_types() {
    $custom_post_types = get_option( 'braftonium_custom_post_types_new', array() );

    if ( ! is_array( $custom_post_types ) || empty( $custom_post_types ) ) {
        return;
    }

    // Map taxonomy slug => display name from the saved taxonomy definitions.
    $taxonomy_names = array();
    foreach ( braftonium_normalize_taxonomies( get_option( 'braftonium_custom_post_types_taxonomies', array() ) ) as $tax ) {
        $taxonomy_names[ $tax['slug'] ] = $tax['name'];
    }

    // Collect which post types each custom taxonomy should be attached to, so a
    // taxonomy shared across several post types is registered once for all of them.
    $taxonomy_post_types = array();

    foreach ( $custom_post_types as $custom_post_type_item ) {
        $custom_post_type = isset( $custom_post_type_item['post_type_title'] ) ? $custom_post_type_item['post_type_title'] : '';
        if ( '' === $custom_post_type ) {
            continue;
        }

        $custom_post_slug = sanitize_html_class( strtolower( str_replace( ' ', '-', $custom_post_type ) ) );

        // WordPress post type keys are limited to 20 characters; skip invalid ones
        // rather than letting register_post_type() fail silently.
        if ( '' === $custom_post_slug || strlen( $custom_post_slug ) > 20 ) {
            continue;
        }

        $custom_post_santype = ucwords( str_replace( '-', ' ', $custom_post_slug ) );
        $custom_post_santype = ucwords( str_replace( '_', ' ', $custom_post_santype ) );

        $posttypes_labels = array(
            'name'          => $custom_post_santype,
            'singular_name' => $custom_post_santype,
            'menu_name'     => $custom_post_santype,
            'add_new_item'  => __( 'Add New ', 'braftonium' ) . ' ' . $custom_post_santype,
        );

        $posttypes_args = array(
            'labels'             => $posttypes_labels,
            'menu_icon'          => 'dashicons-star-filled',
            'public'             => true,
            'capability_type'    => 'page',
            'has_archive'        => true,
            'show_in_rest'       => true,
            'rewrite'            => array(
                'with_front' => false,
            ),
            'show_in_nav_menus'  => true,
            'hierarchical'       => true,
            'publicly_queryable' => true,
            'supports'           => array( 'title', 'excerpt', 'editor', 'thumbnail', 'revisions' ),
        );

        $taxonomies        = array();
        $custom_taxonomies = array();
        $options           = isset( $custom_post_type_item['post_type_options'] ) ? (array) $custom_post_type_item['post_type_options'] : array();

        foreach ( $options as $option ) {
            $option = sanitize_key( $option );
            if ( 'category' === $option || 'post_tag' === $option ) {
                $taxonomies[] = $option;
            } elseif ( '' !== $option ) {
                $custom_taxonomies[] = $option;
            }
        }

        if ( ! empty( $taxonomies ) ) {
            $posttypes_args['taxonomies'] = array_values( array_unique( $taxonomies ) );
        }

        $posttypes_args = apply_filters( 'braftonium_modify_custom_post_type', $posttypes_args, $custom_post_type );
        register_post_type( $custom_post_slug, $posttypes_args );

        foreach ( array_unique( $custom_taxonomies ) as $tax ) {
            $tax_slug = strtolower( $tax );

            // Taxonomy keys are limited to 32 characters.
            if ( '' === $tax_slug || strlen( $tax_slug ) > 32 ) {
                continue;
            }

            if ( ! isset( $taxonomy_post_types[ $tax_slug ] ) ) {
                $taxonomy_post_types[ $tax_slug ] = array();
            }
            $taxonomy_post_types[ $tax_slug ][] = $custom_post_slug;
        }
    }

    // Register each custom taxonomy once, attached to every post type that uses it.
    foreach ( $taxonomy_post_types as $tax_slug => $object_types ) {
        $tax_name = isset( $taxonomy_names[ $tax_slug ] ) && '' !== $taxonomy_names[ $tax_slug ]
            ? $taxonomy_names[ $tax_slug ]
            : ucfirst( str_replace( '_', ' ', $tax_slug ) );

        $tax_name_plural = $tax_name . 's';
        $labels          = array(
            'name'              => $tax_name_plural,
            'singular_name'     => $tax_name,
            /* translators: %s: taxonomy singular name. */
            'search_items'      => sprintf( __( 'Search %s', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy plural name. */
            'all_items'         => sprintf( __( 'All %s', 'braftonium' ), $tax_name_plural ),
            /* translators: %s: taxonomy singular name. */
            'parent_item'       => sprintf( __( 'Parent %s', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy singular name. */
            'parent_item_colon' => sprintf( __( 'Parent %s:', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy singular name. */
            'edit_item'         => sprintf( __( 'Edit %s', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy singular name. */
            'update_item'       => sprintf( __( 'Update %s', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy singular name. */
            'add_new_item'      => sprintf( __( 'Add New %s', 'braftonium' ), $tax_name ),
            /* translators: %s: taxonomy singular name. */
            'new_item_name'     => sprintf( __( 'New %s Name', 'braftonium' ), $tax_name ),
            'menu_name'         => $tax_name_plural,
        );

        register_taxonomy(
            $tax_slug,
            array_values( array_unique( $object_types ) ),
            apply_filters(
                'braftonium_taxonomy_filter',
                array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => array( 'slug' => $tax_slug ),
                )
            )
        );
    }

    if ( get_option( 'braftonium_custom_posts_needs_flush' ) ) {
        flush_rewrite_rules( false );
        delete_option( 'braftonium_custom_posts_needs_flush' );
    }
}
?>
