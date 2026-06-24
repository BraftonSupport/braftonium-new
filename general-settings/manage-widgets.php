<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'braftonium_register_widgets_page' );
function braftonium_register_widgets_page() {
    add_submenu_page(
        'braftonium-settings',
        __( 'Widgets Areas', 'braftonium' ),
        __( 'Widgets Areas', 'braftonium' ),
        'manage_options',
        'braftonium-manage-widgets',
        'braftonium_render_widgets_page'
    );
}

add_action( 'admin_post_braftonium_save_widgets', 'braftonium_save_widgets' );
function braftonium_save_widgets() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'braftonium' ) );
    }

    check_admin_referer( 'braftonium_widgets_save' );

    $names        = isset( $_POST['widget_name'] ) ? (array) wp_unslash( $_POST['widget_name'] ) : array();
    $classes      = isset( $_POST['widget_class'] ) ? (array) wp_unslash( $_POST['widget_class'] ) : array();
    $ids          = isset( $_POST['widget_id'] ) ? (array) wp_unslash( $_POST['widget_id'] ) : array();
    $descriptions = isset( $_POST['widget_description'] ) ? (array) wp_unslash( $_POST['widget_description'] ) : array();
    $widgets      = array();

    foreach ( $names as $index => $name ) {
        $clean_name = sanitize_text_field( $name );
        if ( '' === $clean_name ) {
            continue;
        }

        $widgets[] = array(
            'widget_name'        => $clean_name,
            'widget_class'       => isset( $classes[ $index ] ) ? sanitize_html_class( $classes[ $index ] ) : '',
            'widget_id'          => isset( $ids[ $index ] ) ? sanitize_html_class( $ids[ $index ] ) : '',
            'widget_description' => isset( $descriptions[ $index ] ) ? sanitize_textarea_field( $descriptions[ $index ] ) : '',
        );
    }

    update_option( 'braftonium_manage_widgets', $widgets );

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'braftonium-manage-widgets',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );
    exit;
}

function braftonium_render_widgets_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $widgets = get_option( 'braftonium_manage_widgets', array() );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Widgets Areas', 'braftonium' ); ?></h1>
        <?php if ( isset( $_GET['updated'] ) ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'braftonium' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="braftonium_save_widgets" />
            <?php wp_nonce_field( 'braftonium_widgets_save' ); ?>

            <p><?php esc_html_e( 'Create widget areas here, then manage widget content in the default Widgets screen.', 'braftonium' ); ?></p>

            <table class="widefat striped" id="braftonium-widget-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'braftonium' ); ?></th>
                        <th><?php esc_html_e( 'Class', 'braftonium' ); ?></th>
                        <th><?php esc_html_e( 'ID', 'braftonium' ); ?></th>
                        <th><?php esc_html_e( 'Description', 'braftonium' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $widgets ) ) : ?>
                        <tr>
                            <td><input type="text" name="widget_name[]" class="regular-text" /></td>
                            <td><input type="text" name="widget_class[]" class="regular-text" /></td>
                            <td><input type="text" name="widget_id[]" class="regular-text" /></td>
                            <td><textarea name="widget_description[]" rows="2" class="large-text"></textarea></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $widgets as $widget ) : ?>
                            <tr>
                                <td><input type="text" name="widget_name[]" class="regular-text" value="<?php echo esc_attr( $widget['widget_name'] ?? '' ); ?>" /></td>
                                <td><input type="text" name="widget_class[]" class="regular-text" value="<?php echo esc_attr( $widget['widget_class'] ?? '' ); ?>" /></td>
                                <td><input type="text" name="widget_id[]" class="regular-text" value="<?php echo esc_attr( $widget['widget_id'] ?? '' ); ?>" /></td>
                                <td><textarea name="widget_description[]" rows="2" class="large-text"><?php echo esc_textarea( $widget['widget_description'] ?? '' ); ?></textarea></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <p><button type="button" class="button" id="braftonium-add-widget-row"><?php esc_html_e( 'Add Row', 'braftonium' ); ?></button></p>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    (function () {
        var addBtn = document.getElementById('braftonium-add-widget-row');
        var table = document.getElementById('braftonium-widget-table');
        if (!addBtn || !table) return;
        addBtn.addEventListener('click', function () {
            var row = document.createElement('tr');
            row.innerHTML = '<td><input type="text" name="widget_name[]" class="regular-text" /></td><td><input type="text" name="widget_class[]" class="regular-text" /></td><td><input type="text" name="widget_id[]" class="regular-text" /></td><td><textarea name="widget_description[]" rows="2" class="large-text"></textarea></td>';
            table.querySelector('tbody').appendChild(row);
        });
    })();
    </script>
    <?php
}

function createWidgetAreas() {
    $widgets = get_option( 'braftonium_manage_widgets', array() );
    if ( ! is_array( $widgets ) || empty( $widgets ) ) {
        return;
    }

    foreach ( $widgets as $widget ) {
        $name = isset( $widget['widget_name'] ) ? $widget['widget_name'] : '';
        if ( '' === $name ) {
            continue;
        }

        $id          = ! empty( $widget['widget_id'] ) ? $widget['widget_id'] : 'braftonium-widget-' . str_replace( '_', '-', str_replace( ' ', '-', strtolower( $name ) ) );
        $class       = ! empty( $widget['widget_class'] ) ? $widget['widget_class'] : 'braftonium-widget-' . $id;
        $description = ! empty( $widget['widget_description'] ) ? $widget['widget_description'] : 'Braftonium widget';

        register_sidebar(
            array(
                'name'          => ucfirst( $name ),
                'id'            => $id,
                'description'   => $description,
                'before_widget' => '<div class="' . esc_attr( $class ) . '">',
                'after_widget'  => '</div>',
            )
        );
    }
}
add_action( 'widgets_init', 'createWidgetAreas' );
?>