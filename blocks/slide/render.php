<?php
/**
 * Render: braftonium/slide
 *
 * @var array  $attributes
 * @var string $content
 *
 * Structure: the outer .braftonium-slide is the slick cell; .braftonium-slide__inner
 * is the visible card. The cell carries the horizontal gap (see slide.scss) so
 * adjacent cards don't touch, while the card holds the border / background.
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — block usage metadata, NOT parsed by WordPress.
 * @intent       One slide/card inside a slider carousel.
 * @options      bgColor RGBA (card background, rendered only when a>0). Only option.
 * @parent       braftonium/slider ONLY. reusable: false.
 * @innerblocks  Template = core/paragraph; then any blocks.
 * @render       div.braftonium-slide (the Slick cell, carries the horizontal gap) ›
 *               div.braftonium-slide__inner (the visible card, inline bg if a>0) › InnerBlocks.
 * @classes      .braftonium-slide .braftonium-slide__inner
 * @usewhen      Always, as the repeated unit inside slider.
 * ───────────────────────────────────────────────────────────────────────────
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_color = $attributes['bgColor'] ?? array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0 );

// Card background color (only when an alpha is set).
$inner_style = '';
if ( floatval( $bg_color['a'] ?? 0 ) > 0 ) {
    $inner_style = sprintf(
        'background-color:rgba(%d,%d,%d,%s);',
        (int) ( $bg_color['r'] ?? 0 ),
        (int) ( $bg_color['g'] ?? 0 ),
        (int) ( $bg_color['b'] ?? 0 ),
        floatval( $bg_color['a'] )
    );
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-slide',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <div class="braftonium-slide__inner"<?php echo $inner_style ? ' style="' . esc_attr( $inner_style ) . '"' : ''; ?>>
        <?php echo $content; ?>
    </div>
</div>
