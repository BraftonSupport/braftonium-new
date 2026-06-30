<?php
/**
 * Render: braftonium/custom-row
 *
 * @var array  $attributes
 * @var string $content
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — block usage metadata, NOT parsed by WordPress.
 * @intent       A general full-width container/wrapper row: optional background image
 *               (independently offsettable) and/or background colour, with freeform content.
 * @options      backgroundImage (id) + backgroundImageUrl/Alt; backgroundPosition {top,left}
 *               via TWO RangeControls (-500…500px, shown only once an image is set);
 *               bgColor RGBA (rendered only when a>0). NO column/width/gap controls (that's
 *               custom-list); NO content template.
 * @innerblocks  Free InnerBlocks (useInnerBlocksProps), wrapper .custom-row-content. No template.
 * @render       div.braftonium-custom-row (inline background-color if set) ›
 *               img.background-image (inline top/left px) + div.custom-row-content › InnerBlocks.
 * @classes      .braftonium-custom-row .background-image .custom-row-content
 * @microstyles  braftonium-bg-full / braftonium-bg-wrap; pair with braftonium-full-bleed for a
 *               100vw breakout.
 * @usewhen      A full-width band / wrapper row with a background image or colour + freeform content.
 * @avoidwhen    A responsive multi-column grid (custom-list); a carousel (slider); a plain
 *               constrained content block with no background (core/group).
 * ───────────────────────────────────────────────────────────────────────────
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
