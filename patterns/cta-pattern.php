<?php
register_block_pattern(
    'braftonium/' . str_replace( '.php', '', basename( __FILE__ ) ),
    array(
        'title'       => __( 'Brafton CTA Block', 'braftonium' ),
        'description' => _x( 'Brafton CTA Block', 'Brafton CTA Block', 'braftonium' ),
        'categories'  => array( 'braftonium' ),
        'content'     => '<!-- wp:braftonium/cta -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button /--></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:braftonium/cta -->',
    )
);
