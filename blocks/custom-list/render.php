<?php
/**
 * Render: braftonium/custom-list
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url = $attributes['backgroundImageUrl'] ?? '';
$bg_alt = $attributes['backgroundImageAlt'] ?? '';
$bg_pos = $attributes['backgroundPosition'] ?? array( 'top' => 0, 'left' => 0 );

$bg_style = '';
if ( $bg_url ) {
    $bg_style = sprintf(
        'top:%dpx;left:%dpx;',
        (int) ( $bg_pos['top'] ?? 0 ),
        (int) ( $bg_pos['left'] ?? 0 )
    );
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-custom-list',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" style="<?php echo esc_attr( $bg_style ); ?>" />
    <?php endif; ?>
    <div class="custom-list-content">
        <?php echo $content; ?>
    </div>
</div>
