<?php
/**
 * Title:          Two Column (Text + Image)
 * Slug:           braftonium/two-column
 * Categories:     braftonium, braftonium-sections
 * Keywords:       two column, media text, split, image, heading, paragraph, button
 * Viewport Width: 1200
 * Description:    A basic two-column layout — a heading, paragraph and button beside an image. Stacks on mobile.
 *
 * ───────────────────────────────────────────────────────────────────────────
 * AI / REUSE FRONTAGE — custom metadata, NOT parsed by WordPress.
 * @intent       A simple row pairing a block of copy with one supporting image.
 * @layout       core/columns (vertically centered, 50/50): a text column beside an
 *               image column. Stacks to 1-col on mobile.
 * @composition  core/columns ›
 *               [core/column: core/heading + core/paragraph + core/buttons] +
 *               [core/column: core/image].
 * @classes      (none — pure core blocks, no theme classes or tokens)
 * @slots        Replace heading + paragraph + button label/href; swap the image.
 * @variants      • Reversed: put the image column first for an image-left layout.
 *                • No CTA: delete the core/buttons block.
 * @reusewhen    A row pairs a block of copy with one supporting image.
 * @avoidwhen    A grid of repeated cards → use a card-grid pattern instead.
 * @responsive   2-col → stacks to 1-col on mobile.
 * ───────────────────────────────────────────────────────────────────────────
 */
?>
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading -->
<h2 class="wp-block-heading">Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Add your supporting copy here and pair it with the image beside it. Keep it to a few sentences so the two columns stay balanced.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Learn More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
