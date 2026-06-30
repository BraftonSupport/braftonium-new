<?php
/**
 * Render: braftonium/cta
 *
 * @var array  $attributes
 * @var string $content
 * @var WP_Block $block
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — block usage metadata, NOT parsed by WordPress.
 * @intent       Call-to-action / promo band: like banner but the starter content
 *               includes a buttons row for CTAs.
 * @options      backgroundImage (id) + backgroundImageUrl/Alt (MediaUpload);
 *               overlayColor RGBA (default TRANSPARENT); alignContent left|center|right.
 *               supports.color.background (block-level background).
 * @innerblocks  Free InnerBlocks; template = core/heading (h2) + core/paragraph + core/buttons.
 * @render       div.braftonium-cta › img.background-image + div.overlay (inline rgba)
 *               + div.cta-content.align-wrap-{left|center|right} › InnerBlocks.
 * @classes      .braftonium-cta .background-image .overlay .cta-content .align-wrap-*
 * @microstyles  braftonium-bg-full / braftonium-bg-wrap (background width) apply.
 * @usewhen      A CTA/conversion band (image + headline + buttons).
 * @avoidwhen    No CTA buttons (use banner); plain content row (use custom-row).
 * ───────────────────────────────────────────────────────────────────────────
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bg_url   = isset( $attributes['backgroundImageUrl'] ) ? $attributes['backgroundImageUrl'] : '';
$bg_alt   = isset( $attributes['backgroundImageAlt'] ) ? $attributes['backgroundImageAlt'] : '';
$overlay  = isset( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : array( 'r' => 0, 'g' => 0, 'b' => 0, 'a' => 0 );
$align    = isset( $attributes['alignContent'] ) ? $attributes['alignContent'] : 'left';

$overlay_css = sprintf(
    'rgba(%d, %d, %d, %s)',
    (int) ( $overlay['r'] ?? 0 ),
    (int) ( $overlay['g'] ?? 0 ),
    (int) ( $overlay['b'] ?? 0 ),
    isset( $overlay['a'] ) ? floatval( $overlay['a'] ) : 0
);

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-cta',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_url ) : ?>
        <img src="<?php echo esc_url( $bg_url ); ?>" alt="<?php echo esc_attr( $bg_alt ); ?>" class="background-image" />
    <?php endif; ?>
    <div class="overlay" style="background-color: <?php echo esc_attr( $overlay_css ); ?>;"></div>
    <div class="cta-content align-wrap-<?php echo esc_attr( $align ); ?>">
        <?php echo $content; ?>
    </div>
</div>
