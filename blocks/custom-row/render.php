<?php
/**
 * Render: braftonium/custom-row
 *
 * @var array  $attributes
 * @var string $content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url   = $attributes['backgroundImageUrl'] ?? '';
$bg_alt   = $attributes['backgroundImageAlt'] ?? '';
$bg_pos   = $attributes['backgroundPosition'] ?? array( 'top' => 0, 'left' => 0 );
$bg_color = $attributes['bgColor'] ?? array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0 );

$bg_style = '';
if ( $bg_url ) {
    $bg_style = sprintf(
        'top:%dpx;left:%dpx;',
        (int) ( $bg_pos['top'] ?? 0 ),
        (int) ( $bg_pos['left'] ?? 0 )
    );
}

// Wrapper background color (only when an alpha is set).
$wrapper_style = '';
if ( floatval( $bg_color['a'] ?? 0 ) > 0 ) {
    $wrapper_style = sprintf(
        'background-color:rgba(%d,%d,%d,%s);',
        (int) ( $bg_color['r'] ?? 0 ),
        (int) ( $bg_color['g'] ?? 0 ),
        (int) ( $bg_color['b'] ?? 0 ),
        floatval( $bg_color['a'] )
    );
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-custom-row',
    'style' => $wrapper_style,
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" style="<?php echo esc_attr( $bg_style ); ?>" />
    <?php endif; ?>
    <div class="custom-row-content">
        <?php echo $content; ?>
    </div>
</div>
