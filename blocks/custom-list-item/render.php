<?php
/**
 * Render: braftonium/custom-list-item
 *
 * @var array  $attributes
 * @var string $content
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — block usage metadata, NOT parsed by WordPress.
 * @intent       One cell of a custom-list grid. Pure container — no options.
 * @parent       braftonium/custom-list ONLY. reusable: false.
 * @innerblocks  Template = core/image + core/heading (h4) + core/paragraph; then any blocks.
 * @render       div.braftonium-custom-list-item › InnerBlocks.
 * @classes      .braftonium-custom-list-item
 * @usewhen      Always, as the repeated unit inside custom-list. Fill with any blocks.
 * ───────────────────────────────────────────────────────────────────────────
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'braftonium-custom-list-item',
) );
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
