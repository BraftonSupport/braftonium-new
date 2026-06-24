<?php
/**
 * Render: braftonium/banner
 *
 * @var array  $attributes
 * @var string $content
 * @var WP_Block $block
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url   = isset( $attributes['backgroundImageUrl'] ) ? $attributes['backgroundImageUrl'] : '';
$bg_alt   = isset( $attributes['backgroundImageAlt'] ) ? $attributes['backgroundImageAlt'] : '';
$overlay  = isset( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0.5 );
$align    = isset( $attributes['alignContent'] ) ? $attributes['alignContent'] : 'left';

$overlay_css = sprintf(
    'rgba(%d, %d, %d, %s)',
    (int) ( $overlay['r'] ?? 0 ),
    (int) ( $overlay['g'] ?? 0 ),
    (int) ( $overlay['b'] ?? 0 ),
    isset( $overlay['a'] ) ? floatval( $overlay['a'] ) : 0.5
);

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-banner',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" />
    <?php endif; ?>
    <div class="overlay" style="background-color: <?php echo esc_attr( $overlay_css ); ?>;"></div>
    <div class="wrap align-wrap-<?php echo esc_attr( $align ); ?>">
        <?php echo $content; ?>
    </div>
</div>
