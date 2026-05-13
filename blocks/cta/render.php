<?php
/**
 * Render: braftonium/cta
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url     = $attributes['backgroundImageUrl'] ?? '';
$bg_alt     = $attributes['backgroundImageAlt'] ?? '';
$bg_pos     = $attributes['backgroundPosition'] ?? array( 'top' => 0, 'left' => 0, 'right' => 0, 'bottom' => 0 );
$full_width = ! empty( $attributes['fullWidth'] );

$classes = 'braftonium-cta' . ( $full_width ? ' full-width' : '' );

$bg_style = '';
if ( $bg_url ) {
    $bg_style = sprintf(
        'top:%dpx;left:%dpx;right:%dpx;bottom:%dpx;',
        (int) ( $bg_pos['top'] ?? 0 ),
        (int) ( $bg_pos['left'] ?? 0 ),
        (int) ( $bg_pos['right'] ?? 0 ),
        (int) ( $bg_pos['bottom'] ?? 0 )
    );
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => $classes,
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" style="<?php echo esc_attr( $bg_style ); ?>" />
    <?php endif; ?>
    <div class="cta-content">
        <?php echo $content; ?>
    </div>
</div>
